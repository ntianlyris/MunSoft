<?php
// ==========================================================
// REMITTANCE DETAILS — Excel Export
// Mirrors all five print_type layouts from the TCPDF version.
//
// SETUP:
//   your_project/
//   ├── includes/
//   │   └── SimpleXLSXGen.php        ← library (single file)
//   └── reports/
//       └── export_remittance_excel.php ← this file
// ==========================================================

require_once('../includes/simplexlsxgen/SimpleXLSXGen.php');
require_once('../includes/view/functions.php');
require_once('../includes/class/Employee.php');
require_once('../includes/class/Department.php');
require_once('../includes/class/Employment.php');
require_once('../includes/class/Signatory.php');
require_once('../includes/class/Remittance.php');

use Shuchkin\SimpleXLSXGen;

// ==========================================================
// SAME DATA INITIALIZATION AS PDF VERSION
// ==========================================================
$Employee   = new Employee();
$Department = new Department();
$Employment = new Employment();
$Signatory  = new Signatory();
$Remittance = new Remittance();

$report_code = $_GET['report_code'] ?? 'ACCTG';
$dept_id     = $_GET['dept_id']     ?? '';

$print_type    = $_POST['print_type']    ?? '';
$remittance_id = $_POST['remittance_id'] ?? '';

$remit_data    = $Remittance->GetRemittanceByID($remittance_id);
$remit_details = $Remittance->getRemittanceDetails($remittance_id);
$remit_type    = $remit_data['remittance_type'] ?? '';
$remit_title   = $remit_data['remittance_title'] ?? ($remit_details[0]['remittance_title'] ?? '');
$period_start  = OutputShortDate($remit_data['period_start'] ?? '');
$period_end    = OutputShortDate($remit_data['period_end']   ?? '');
$period_text   = "For the period: $period_start to $period_end";

// ==========================================================
// SIGNATORIES — same filter logic as PDF version
// ==========================================================
$signatories          = $Signatory->FetchActiveSignatoriesByReportType($report_code);
$filtered_signatories = [];
foreach ($signatories as $sign) {
    if ($sign['role_type'] !== 'HEAD') {
        $filtered_signatories[] = $sign;
        continue;
    }
    if ($sign['role_type'] === 'HEAD' && $sign['dept_id'] == $dept_id) {
        $filtered_signatories[] = $sign;
    }
}

// ==========================================================
// SIMPLEXLSXGEN STYLE TAG REFERENCE
// -------------------------------------------------------
// <b>              bold font
// <i>              italic font
// <center>         horizontal center
// <right>          horizontal right-align
// <left>           horizontal left-align
// <middle>         vertical center
// <wraptext>       wrap text in cell
// <style ...>      advanced styling:
//   bgcolor="AARRGGBB"           background fill
//   border="thin"                all 4 sides thin
//   border="none none thin none" individual sides (top right bottom left)
//   nf="format"                  number format  e.g. #,##0.00
//   font-size="N"                point size
//   height="N"                   row height (whole row)
// ==========================================================

// Shared style constants
$BG_HEADER = 'FFF2F2F2';   // light gray — matches PDF's #f2f2f2 header bg
$BG_TOTAL  = 'FFFAFAFA';   // near-white — subtle total row distinction
$NF_MONEY  = '#,##0.00';   // currency number format

// Pre-built tag prefixes
$H_TITLE    = '<b><center><style font-size="14">';               // "REMITTANCE DETAILS"
$H_MUN      = '<center><style font-size="11">';                  // "Municipality of Polanco, Z.N."
$H_REPTTITLE= '<b><center><style font-size="11">';               // report type title
$H_PERIOD   = '<center><style font-size="9">';                   // period text
$T_HDR      = '<b><center><middle>'
            . '<style bgcolor="' . $BG_HEADER . '" border="thin" font-size="9">';  // table column header
$T_NO       = '<center><middle><style border="thin" font-size="9">';               // # column
$T_LEFT     = '<left><middle><style border="thin" font-size="9">';                 // left-aligned data cell
$T_RIGHT    = '<right><middle><style border="thin" nf="' . $NF_MONEY . '" font-size="9">'; // money cell
$T_TOT_LBL  = '<b><right><middle>'
            . '<style bgcolor="' . $BG_TOTAL . '" border="thin" font-size="9">';   // "TOTAL:" label cell
$T_TOT_NUM  = '<b><right><middle>'
            . '<style bgcolor="' . $BG_TOTAL . '" border="thin" nf="' . $NF_MONEY . '" font-size="9">'; // total amount

// ==========================================================
// HELPER: convert 1-based column index → Excel letter(s)
// ==========================================================
function colLetter($n) {
    $c = '';
    for ($i = $n; $i > 0; $i = intdiv($i - 1, 26)) {
        $c = chr(65 + ($i - 1) % 26) . $c;
    }
    return $c;
}
function cr($c1, $r1, $c2, $r2) {
    return colLetter($c1) . $r1 . ':' . colLetter($c2) . $r2;
}
function eRow($n) { return array_fill(0, $n, null); }

// ==========================================================
// PER-TYPE COLUMN CONFIG & DATA COLLECTION
// ==========================================================
// Each type defines:
//   $num_cols    — total column count
//   $col_widths  — [col_index => width]  (1-based)
//   $headers     — [label, ...]  for table header row
//   $data_rows   — pre-built array of styled cell arrays
//   $total_row   — the styled total row array
//   $report_label — title shown above table (row 4)
//   $period_text_override — if type computes its own period
// ==========================================================

$data_rows    = [];
$total_row    = [];
$report_label = '';
$period_override = '';   // only set for breakdown types that parse their own period

// ----------------------------------------------------------
// TYPE: loans
// Columns: # | Loan Type | Total Loan Amount
// ----------------------------------------------------------
if ($print_type === 'loans') {
    $report_label    = 'Loans';
    $num_cols        = 3;
    $col_widths      = [1 => 6, 2 => 50, 3 => 22];
    $headers         = ['#', 'Loan Type', 'Total Loan Amount'];
    $loan_details    = $Remittance->getLoansRemittanceDetails($remittance_id);
    $loan_grandtotal = 0;
    $count           = 0;

    if (!empty($loan_details) && is_array($loan_details)) {
        foreach ($loan_details as $row) {
            $loan_grandtotal += $row['total_amount'];
            $count++;
            $data_rows[] = [
                $T_NO    . $count,
                $T_LEFT  . htmlspecialchars($row['loan_title']),
                $row['total_amount'] != 0
                    ? $T_RIGHT . number_format($row['total_amount'], 2)
                    : $T_LEFT  . '',
            ];
        }
    }
    // GRAND TOTAL row — label spans cols 1–2, amount in col 3
    $total_row = [
        $T_TOT_LBL . 'GRAND TOTAL:',   // col 1 (will be merged with col 2 below)
        null,                            // col 2 — merged
        $T_TOT_NUM . number_format($loan_grandtotal, 2),
    ];

// ----------------------------------------------------------
// TYPE: others
// Columns: # | Particulars | Total Amount
// ----------------------------------------------------------
} elseif ($print_type === 'others') {
    $report_label        = 'Other Remittances';
    $num_cols            = 3;
    $col_widths          = [1 => 6, 2 => 52, 3 => 22];
    $headers             = ['#', 'Particulars', 'Total Amount'];
    $other_remit_details = $Remittance->getOthersRemittanceDetails($remittance_id);
    $total_amounts       = 0;
    $count               = 0;

    if (!empty($other_remit_details) && is_array($other_remit_details)) {
        foreach ($other_remit_details as $row) {
            $total_amounts += $row['total_amount'];
            $count++;
            $data_rows[] = [
                $T_NO   . $count,
                $T_LEFT . htmlspecialchars($row['deduct_title']),
                $row['total_amount'] != 0
                    ? $T_RIGHT . number_format($row['total_amount'], 2)
                    : $T_LEFT  . '',
            ];
        }
    }
    $total_row = [
        $T_TOT_LBL . 'TOTAL:',
        null,
        $T_TOT_NUM . number_format($total_amounts, 2),
    ];

// ----------------------------------------------------------
// TYPE: others_breakdown
// Columns: # | Employee Name | Position | Amount
// ----------------------------------------------------------
} elseif ($print_type === 'others_breakdown') {
    $breakdown_param  = $_POST['breakdown'] ?? '';
    $breakdown_period = $_POST['period']    ?? '';
    list($start_date, $end_date) = explode('_', $breakdown_period);
    $report_label    = $_POST['deduct_title'] ?? 'Other Remittance Breakdown';
    $period_override = 'For the period: ' . OutputShortDate($start_date) . ' to ' . OutputShortDate($end_date);

    $num_cols   = 4;
    $col_widths = [1 => 6, 2 => 36, 3 => 38, 4 => 20];
    $headers    = ['#', 'Employee Name', 'Position', 'Amount'];

    $breakdown_details = json_decode($breakdown_param, true);
    $total_amounts     = 0;
    $count             = 0;

    if (!empty($breakdown_details) && is_array($breakdown_details)) {
        foreach ($breakdown_details as $row) {
            $total_amounts += $row['amount'];
            $count++;
            $data_rows[] = [
                $T_NO   . $count,
                $T_LEFT . htmlspecialchars($row['employee_name']),
                $T_LEFT . htmlspecialchars(trim(explode('(', $row['position_title'])[0])),
                $row['amount'] != 0
                    ? $T_RIGHT . number_format($row['amount'], 2)
                    : $T_LEFT  . '',
            ];
        }
    }
    // TOTAL label spans cols 1–3, amount in col 4
    $total_row = [
        $T_TOT_LBL . 'TOTAL:',   // col 1 (merged 1–3 below)
        null,                      // col 2 — merged
        null,                      // col 3 — merged
        $T_TOT_NUM . number_format($total_amounts, 2),
    ];

// ----------------------------------------------------------
// TYPE: loans_breakdown
// Columns: # | Employee Name | Position | Loan Amount
// ----------------------------------------------------------
} elseif ($print_type === 'loans_breakdown') {
    $breakdown_param  = $_POST['breakdown'] ?? '';
    $breakdown_period = $_POST['period']    ?? '';
    list($start_date, $end_date) = explode('_', $breakdown_period);
    $report_label    = $_POST['loan_title'] ?? 'Loans Breakdown';
    $period_override = 'For the period: ' . OutputShortDate($start_date) . ' to ' . OutputShortDate($end_date);

    $num_cols   = 4;
    $col_widths = [1 => 6, 2 => 36, 3 => 38, 4 => 20];
    $headers    = ['#', 'Employee Name', 'Position', 'Loan Amount'];

    $breakdown_details = json_decode($breakdown_param, true);
    $loan_totals       = 0;
    $count             = 0;

    if (!empty($breakdown_details) && is_array($breakdown_details)) {
        foreach ($breakdown_details as $row) {
            $loan_totals += $row['amount'];
            $count++;
            $data_rows[] = [
                $T_NO   . $count,
                $T_LEFT . htmlspecialchars($row['employee_name']),
                $T_LEFT . htmlspecialchars(trim(explode('(', $row['position_title'])[0])),
                $row['amount'] != 0
                    ? $T_RIGHT . number_format($row['amount'], 2)
                    : $T_LEFT  . '',
            ];
        }
    }
    $total_row = [
        $T_TOT_LBL . 'TOTAL:',
        null,
        null,
        $T_TOT_NUM . number_format($loan_totals, 2),
    ];

// ----------------------------------------------------------
// TYPE: default (else)
// Columns: # | Employee Name | Particulars | Employee Share | Employer Share
// ----------------------------------------------------------
} else {
    $report_label    = $remit_title;
    $num_cols        = 5;
    $col_widths      = [1 => 6, 2 => 34, 3 => 30, 4 => 20, 5 => 20];
    $headers         = ['#', 'Employee Name', 'Particulars', 'Employee Share', 'Employer Share'];
    $employee_totals = 0;
    $employer_totals = 0;
    $count           = 0;

    if (!empty($remit_details) && is_array($remit_details)) {
        foreach ($remit_details as $row) {
            $employee_totals += $row['employee_share'];
            $employer_totals += $row['employer_share'];
            $count++;
            $data_rows[] = [
                $T_NO   . $count,
                $T_LEFT . htmlspecialchars($row['employee_name']),
                $T_LEFT . htmlspecialchars($row['remittance_title']),
                $row['employee_share'] != 0
                    ? $T_RIGHT . number_format($row['employee_share'], 2)
                    : $T_LEFT  . '',
                $row['employer_share'] != 0
                    ? $T_RIGHT . number_format($row['employer_share'], 2)
                    : $T_LEFT  . '',
            ];
        }
    }
    // TOTAL label spans cols 1–3, amounts in cols 4 & 5
    $total_row = [
        $T_TOT_LBL . 'TOTAL:',   // col 1 (merged 1–3 below)
        null,                      // col 2 — merged
        null,                      // col 3 — merged
        $T_TOT_NUM . number_format($employee_totals, 2),
        $T_TOT_NUM . number_format($employer_totals, 2),
    ];
}

// Resolve which period text to display
$display_period = $period_override !== '' ? $period_override : $period_text;

// ==========================================================
// BUILD ROWS ARRAY
// Row numbering (1-based Excel rows):
//   1  — "Municipality of Polanco, Z.N."
//   2  — "REMITTANCE DETAILS"
//   3  — blank spacer
//   4  — Report title (e.g. "Loans", "GSIS", etc.)
//   5  — Period text
//   6  — blank spacer
//   7  — Table column headers
//   8… — Data rows
//   N  — Total row
//   N+1 — blank spacer
//   N+2 — blank spacer
//   N+3 onwards — signatories (2 per group of 4 rows)
// ==========================================================
$rows = [];

// ROW 1 — municipality name
$r = eRow($num_cols); $r[0] = $H_MUN . 'Municipality of Polanco, Z.N.';
$rows[] = $r;

// ROW 2 — main title
$r = eRow($num_cols); $r[0] = $H_TITLE . 'REMITTANCE DETAILS';
$rows[] = $r;

// ROW 3 — blank spacer
$rows[] = eRow($num_cols);

// ROW 4 — report type title
$r = eRow($num_cols); $r[0] = $H_REPTTITLE . $report_label;
$rows[] = $r;

// ROW 5 — period
$r = eRow($num_cols); $r[0] = $H_PERIOD . $display_period;
$rows[] = $r;

// ROW 6 — blank spacer
$rows[] = eRow($num_cols);

// ROW 7 — table column headers
$header_row = [];
foreach ($headers as $h) {
    $header_row[] = $T_HDR . $h;
}
$rows[] = $header_row;

// ROWS 8… — data rows (already built above per print_type)
$data_start_row = 8;
if (empty($data_rows)) {
    // No data fallback
    $fallback = eRow($num_cols);
    $fallback[0] = '<center><middle><style border="thin" font-size="9">No remittance details found';
    $rows[]      = $fallback;
    $data_rows   = [];   // ensure loop below does nothing
}
foreach ($data_rows as $dr) {
    $rows[] = $dr;
}

// TOTAL ROW
$total_row_excel = count($rows) + 1;   // track the Excel row number for merge
$rows[] = $total_row;

// SPACER rows before signatories
$rows[] = eRow($num_cols);
$rows[] = eRow($num_cols);

// ==========================================================
// SIGNATORIES — 2 per row (matching PDF layout)
// PDF uses: particulars / blank lines / Name bold / Position
// Excel: 4 rows per signatory group of 2:
//   Row A: particulars (left)
//   Row B: blank with bottom border (signature line)
//   Row C: Full Name bold
//   Row D: Position title
// ==========================================================
$sig_groups      = array_chunk($filtered_signatories, 2);
$sig_start_excel = count($rows) + 1;   // track start row for merges

// Each signatory block takes 2 columns. With $num_cols cols available,
// sig block A = cols 1..(num_cols/2), sig block B = cols (num_cols/2+1)..num_cols
$half = (int)floor($num_cols / 2);
if ($half < 1) { $half = 1; }

foreach ($sig_groups as $group) {
    while (count($group) < 2) { $group[] = null; }   // pad to 2

    $rowA = eRow($num_cols);   // particulars
    $rowB = eRow($num_cols);   // signature blank line
    $rowC = eRow($num_cols);   // full name
    $rowD = eRow($num_cols);   // position

    foreach ($group as $i => $sign) {
        $colIdx = $i * $half;   // 0-based array index of block start
        if ($sign === null) { continue; }
        $rowA[$colIdx] = '<left><style font-size="9"><i>' . $sign['sign_particulars'];
        $rowB[$colIdx] = '<style border="none none thin none" height="20">';   // signature underline
        $rowC[$colIdx] = '<b><left><style font-size="10">'   . $sign['full_name'];
        $rowD[$colIdx] = '<left><style font-size="9">'        . $sign['position_title'];
    }
    $rows[] = $rowA;
    $rows[] = $rowB;
    $rows[] = $rowC;
    $rows[] = $rowD;
}

// ==========================================================
// CREATE WORKBOOK
// ==========================================================
$xlsx = new SimpleXLSXGen();
$xlsx->setDefaultFont('Arial');
$xlsx->setDefaultFontSize(10);
$xlsx->setTitle('Remittance Details');
$xlsx->setSubject('Municipality of Polanco Remittance Report');
$xlsx->setAuthor('Municipality of Polanco');
$xlsx->setCompany('Municipality of Polanco');
$xlsx->setLanguage('en-PH');

$xlsx->addSheet($rows, 'Remittance');

// ==========================================================
// MERGE CELLS
// ==========================================================

// Row 1 (municipality) — full width
$xlsx->mergeCells(cr(1, 1, $num_cols, 1));
// Row 2 (main title) — full width
$xlsx->mergeCells(cr(1, 2, $num_cols, 2));
// Row 3 (spacer) — full width
$xlsx->mergeCells(cr(1, 3, $num_cols, 3));
// Row 4 (report title) — full width
$xlsx->mergeCells(cr(1, 4, $num_cols, 4));
// Row 5 (period) — full width
$xlsx->mergeCells(cr(1, 5, $num_cols, 5));
// Row 6 (spacer) — full width
$xlsx->mergeCells(cr(1, 6, $num_cols, 6));

// Total row: merge label columns (all but the last 1 or 2 money columns)
// The total_row array has null placeholders exactly where the merge spans
// loans / others (3 cols): merge cols 1–2 for label
// others_breakdown / loans_breakdown (4 cols): merge cols 1–3 for label
// default (5 cols): merge cols 1–3 for label, separate amounts in 4 & 5
if ($num_cols === 3) {
    $xlsx->mergeCells(cr(1, $total_row_excel, 2, $total_row_excel));
} elseif ($num_cols === 4) {
    $xlsx->mergeCells(cr(1, $total_row_excel, 3, $total_row_excel));
} elseif ($num_cols === 5) {
    $xlsx->mergeCells(cr(1, $total_row_excel, 3, $total_row_excel));
}

// No-data fallback: merge full row
if (empty($data_rows)) {
    $xlsx->mergeCells(cr(1, 8, $num_cols, 8));
}

// Signatory merges — each block spans $half columns for 4 rows
foreach ($sig_groups as $groupIdx => $group) {
    while (count($group) < 2) { $group[] = null; }
    $groupBaseRow = $sig_start_excel + ($groupIdx * 4);
    foreach ($group as $i => $sign) {
        if ($sign === null) { continue; }
        $cStart = 1 + ($i * $half);
        $cEnd   = min($cStart + $half - 1, $num_cols);
        for ($sl = 0; $sl <= 3; $sl++) {
            $xlsx->mergeCells(cr($cStart, $groupBaseRow + $sl, $cEnd, $groupBaseRow + $sl));
        }
    }
}

// ==========================================================
// COLUMN WIDTHS (1-based)
// ==========================================================
foreach ($col_widths as $col => $width) {
    $xlsx->setColWidth($col, $width);
}

// ==========================================================
// DOWNLOAD
// ==========================================================
$safe_type = preg_replace('/[^A-Za-z0-9_\-]/', '_', $print_type ?: 'default');
$safe_dept = preg_replace('/[^A-Za-z0-9_\-]/', '_', $report_label);
$filename  = 'remittance_' . $safe_type . '_' . $safe_dept . '_' . date('Ymd') . '.xlsx';

$xlsx->downloadAs($filename);
exit;
