<?php
// ==========================================================
// PAYROLL EXCEL EXPORT — SimpleXLSXGen v1.5.12 (Shuchkin)
// Mirrors the TCPDF General Payroll PDF layout exactly.
//
// SETUP — Zero dependencies. Drop both files anywhere:
//
//   your_project/
//   ├── includes/
//   │   └── SimpleXLSXGen.php        ← the library (single file)
//   └── reports/
//       └── export_payroll_excel.php ← this export script
//
// SOURCE:  https://github.com/shuchkin/simplexlsxgen
// LICENSE: MIT
// ==========================================================

require_once('../includes/simplexlsxgen/SimpleXLSXGen.php');   // ← only change from PhpXlsxGenerator version
require_once('../includes/view/functions.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Department.php');
require_once('../includes/class/Employee.php');
require_once('../includes/class/Employment.php');
require_once('../includes/class/Signatory.php');

use Shuchkin\SimpleXLSXGen;                      // ← only change from PhpXlsxGenerator version

// ==========================================================
// INITIALIZATION & DATA FETCHING (same logic as PDF version)
// ==========================================================
$Payroll           = new Payroll();
$Department        = new Department();
$Employee          = new Employee();
$Employment        = new Employment();
$Payroll_Signatory = new Signatory();

$period          = $_GET['period']          ?? '';
$dept_id         = $_GET['department']      ?? '';
$employment_type = $_GET['employment_type'] ?? '';

if (!$period || !$dept_id || !$employment_type) {
    die("Missing required parameters.");
}

if (strpos($period, '_') !== false) {
    list($start_date, $end_date) = explode('_', $period);
    $payroll_period_id = $Payroll->GetPayrollPeriodByDates($start_date, $end_date)['payroll_period_id'];
} else {
    $payroll_period_id = $period;
    $period_dates      = $Payroll->GetPeriodDatesByID($payroll_period_id);
    $start_date        = $period_dates['date_start'];
    $end_date          = $period_dates['date_end'];
}

$department_name  = $Department->GetDepartmentDetails($dept_id)['dept_name'];
$pay_period_start = OutputDate($start_date);
$pay_period_end   = OutputDate($end_date);

$configured_earnings   = $Payroll->FetchConfigEarningsIDandCode();
$configured_deductions = $Payroll->FetchConfigDeductionsIDandCode();
$configured_govshares  = $Payroll->FetchGovSharesIDandCode();

$deduction_headers = [];
foreach ($configured_deductions as $d) {
    $deduction_headers[] = $d['deduct_code'];
}
$deduction_headers[] = 'TOTAL';

$payroll_entries = $Payroll->FetchPayrollByPayPeriodAndDept($payroll_period_id, $dept_id, $employment_type);

$payrollData = [];
foreach ($payroll_entries as $row) {
    $pos            = $Employment->FetchEmployeeEmploymentDetailsByIDs($row['employee_id'], $row['employment_id'])['position_title'];
    $position_title = strpos($pos, '(') !== false ? substr($pos, 0, strpos($pos, '(')) : $pos;
    $employee_name  = $Employee->GetEmployeeFullNameByID($row['employee_id'])['full_name'];

    $payrollData[] = [
        'name'        => strtoupper($employee_name),
        'designation' => trim($position_title),
        'earnings'    => buildEarningsArray($configured_earnings,   json_decode($row['earnings_breakdown'],    true) ?? []),
        'deductions'  => buildDeductionsArray($configured_deductions, json_decode($row['deductions_breakdown'], true) ?? []),
        'govshares'   => buildGovSharesArray($configured_govshares,  json_decode($row['govshares_breakdown'],   true) ?? []),
        'net'         => (float)$row['net_pay'],
    ];
}

// ==========================================================
// HELPER FUNCTIONS (identical to PDF version)
// ==========================================================
function buildEarningsArray($configured_earnings, $earnings) {
    $map = [];
    foreach ($earnings as $e) { $map[$e['config_earning_id']] = $e; }
    $result = []; $basic_amount = 0;
    foreach ([1, 2] as $id) {
        if (isset($map[$id]) && $map[$id]['amount'] > 0) { $basic_amount += $map[$id]['amount']; }
    }
    if ($basic_amount > 0) { $result[] = ['label' => 'Basic', 'amount' => $basic_amount]; }
    $result[] = ['label' => 'PERA',   'amount' => $map[3]['amount'] ?? 0];
    $others   = 0;
    foreach ($configured_earnings as $conf) {
        $id = $conf['config_earning_id'];
        if (!in_array($id, [1, 2, 3])) { $others += $map[$id]['amount'] ?? 0; }
    }
    $result[] = ['label' => 'Others', 'amount' => $others];
    $result[] = ['label' => 'Gross',  'amount' => array_sum(array_column($result, 'amount'))];
    return $result;
}

function buildDeductionsArray($configured_deductions, $deductions) {
    $map = []; $result = []; $total = 0;
    foreach ($deductions as $d) { $map[$d['config_deduction_id']] = $d; }
    foreach ($configured_deductions as $conf) {
        $id = $conf['config_deduction_id']; $amount = $map[$id]['amount'] ?? 0;
        $result[] = ['label' => $conf['deduct_code'], 'amount' => $amount]; $total += $amount;
    }
    $result[] = ['label' => 'Total', 'amount' => $total];
    return $result;
}

function buildGovSharesArray($configured_govshares, $govshares) {
    $map = []; $result = [];
    foreach ($govshares as $g) { $map[$g['govshare_id']] = $g; }
    foreach ($configured_govshares as $conf) {
        $id = $conf['govshare_id'];
        $result[] = ['label' => $conf['govshare_code'], 'amount' => $map[$id]['amount'] ?? 0];
    }
    return $result;
}

// ==========================================================
// STYLE TAG CONSTANTS
// -------------------------------------------------------
// SimpleXLSXGen uses the same inline pseudo-HTML tag system
// as the old PhpXlsxGenerator — all your existing tags work.
//
// Tag reference:
//   <b>  <i>  <u>  <s>
//   <left>  <center>  <right>  <top>  <middle>  <bottom>
//   <wraptext>
//   <style bgcolor="AARRGGBB" color="AARRGGBB"
//          border="thin|medium|none none thin none|..."
//          nf="format_code"  font-size="N"  height="N">
//
// NEW features vs CodexWorld fork:
//   <raw>value</raw>  — disables type detection (e.g. keep +12345 as text)
//   SimpleXLSXGen::raw($v)        — same, static helper
//   SimpleXLSXGen::rawArray($arr) — applies raw() to all strings in 2-D array
//   ->setLanguage('en-PH')        — document language property
// ==========================================================

$BG_HEADER    = 'FFD9D9D9';
$BG_SUBHEADER = 'FFF2F2F2';
$BG_TOTAL     = 'FFEEEEEE';
$NF_CURRENCY  = '#,##0.00';

$TAG_HDR = '<b><center><middle><wraptext>'
         . '<style bgcolor="' . $BG_HEADER    . '" border="thin" font-size="8">';
$TAG_SUB = '<b><center><middle>'
         . '<style bgcolor="' . $BG_SUBHEADER . '" border="thin" font-size="7">';
$TAG_NUM = '<right><middle>'
         . '<style border="thin" nf="' . $NF_CURRENCY . '" font-size="7">';
$TAG_NUM_BOLD = '<b><right><middle>'
              . '<style border="thin" nf="' . $NF_CURRENCY . '" font-size="7">';
$TAG_TOT = '<b><right><middle>'
         . '<style bgcolor="' . $BG_TOTAL . '" border="thin" nf="' . $NF_CURRENCY . '" font-size="8">';
$TAG_TOT_EMPTY = '<style bgcolor="' . $BG_TOTAL . '" border="thin">';

// ==========================================================
// COLUMN LAYOUT
// ==========================================================
$ded_count = count($deduction_headers);
$gov_count = 4;

$col_no    = 1;  $col_name  = 2;  $col_basic = 3;
$col_pera  = 4;  $col_other = 5;  $col_gross = 6;
$col_ded1  = 7;
$col_gov1  = $col_ded1 + $ded_count;
$col_net   = $col_gov1 + $gov_count;
$col_sig   = $col_net  + 1;
$last_col  = $col_sig;

function colLetter($n) {
    $c = '';
    for ($i = $n; $i > 0; $i = intdiv($i - 1, 26)) { $c = chr(65 + ($i - 1) % 26) . $c; }
    return $c;
}
function cellRange($c1, $r1, $c2, $r2) { return colLetter($c1).$r1.':'.colLetter($c2).$r2; }
function emptyRow($n) { return array_fill(0, $n, null); }

// ==========================================================
// BUILD ROWS ARRAY
// ==========================================================
$rows = [];

// Rows 1–6: title block
$title_lines = [
    '<center><style font-size="9">Republic of the Philippines',
    '<center><style font-size="9">Province of Zamboanga del Norte',
    '<b><center><style font-size="12">MUNICIPALITY OF POLANCO',
    '<b><center><style font-size="11">GENERAL PAYROLL',
    '<center><style font-size="9">Period: ' . $pay_period_start . ' to ' . $pay_period_end,
    '<center><style font-size="9">Department: ' . $department_name,
];
foreach ($title_lines as $line) {
    $r = emptyRow($last_col); $r[0] = $line; $rows[] = $r;
}

// Row 7: group headers
$row7 = emptyRow($last_col);
$row7[$col_no    - 1] = $TAG_HDR . 'NO.';
$row7[$col_name  - 1] = $TAG_HDR . 'EMPLOYEE / DESIGNATION';
$row7[$col_basic - 1] = $TAG_HDR . 'EARNINGS';
$row7[$col_ded1  - 1] = $TAG_HDR . 'DEDUCTIONS';
$row7[$col_gov1  - 1] = $TAG_HDR . 'GOV SHARES';
$row7[$col_net   - 1] = $TAG_HDR . 'NET PAY';
$row7[$col_sig   - 1] = $TAG_HDR . 'SIGNATURE';
$rows[] = $row7;

// Row 8: sub-column headers
$row8 = emptyRow($last_col);
foreach (['Basic', 'PERA', 'Others', 'Gross'] as $i => $lbl) {
    $row8[$col_basic - 1 + $i] = $TAG_SUB . $lbl;
}
foreach ($deduction_headers as $i => $dh) {
    $row8[$col_ded1 - 1 + $i] = $TAG_SUB . strtoupper($dh);
}
foreach (['L/R', 'HDMF', 'PHIC', 'ECC'] as $i => $lbl) {
    $row8[$col_gov1 - 1 + $i] = $TAG_SUB . $lbl;
}
$rows[] = $row8;

// Data rows
$total_basic = $total_pera = $total_others = $total_gross = $total_net = 0;
$total_ded   = array_fill(0, $ded_count, 0);
$total_gov   = array_fill(0, $gov_count, 0);

foreach ($payrollData as $index => $employee) {
    $count   = $index + 1;
    $dataRow = emptyRow($last_col);

    $dataRow[$col_no   - 1] = '<center><middle><style border="thin" font-size="7">' . $count . '.';
    $dataRow[$col_name - 1] = '<wraptext><middle><style border="thin" height="30">'
        . '<b>' . $employee['name'] . "\n" . '<i>' . $employee['designation'];

    foreach ($employee['earnings'] as $ei => $val) {
        $a = $val['amount'];
        $dataRow[$col_basic - 1 + $ei] = $TAG_NUM . ($a != 0 ? number_format($a, 2) : '-');
        if ($val['label'] === 'Basic')  { $total_basic  += $a; }
        if ($val['label'] === 'PERA')   { $total_pera   += $a; }
        if ($val['label'] === 'Others') { $total_others += $a; }
        if ($val['label'] === 'Gross')  { $total_gross  += $a; }
    }
    foreach ($employee['deductions'] as $di => $val) {
        $a = $val['amount'];
        $dataRow[$col_ded1 - 1 + $di] = $TAG_NUM . ($a != 0 ? number_format($a, 2) : '-');
        $total_ded[$di] += $a;
    }
    foreach ($employee['govshares'] as $gi => $val) {
        $a = $val['amount'];
        $dataRow[$col_gov1 - 1 + $gi] = $TAG_NUM . ($a != 0 ? number_format($a, 2) : '-');
        $total_gov[$gi] += $a;
    }

    $dataRow[$col_net - 1] = $TAG_NUM_BOLD . number_format($employee['net'], 2);
    $total_net += $employee['net'];
    $dataRow[$col_sig - 1] = '<style border="thin" font-size="7">' . $count . '.';
    $rows[] = $dataRow;
}

// Totals row
$totalRow = emptyRow($last_col);
$totalRow[$col_no   - 1] = '<b><right><middle><style bgcolor="' . $BG_TOTAL . '" border="thin" font-size="8">TOTAL';
$totalRow[$col_name - 1] = $TAG_TOT_EMPTY;
foreach ([$total_basic, $total_pera, $total_others, $total_gross] as $ei => $v) {
    $totalRow[$col_basic - 1 + $ei] = $TAG_TOT . number_format($v, 2);
}
foreach ($total_ded as $di => $v) {
    $totalRow[$col_ded1 - 1 + $di] = $TAG_TOT . ($v != 0 ? number_format($v, 2) : '-');
}
foreach ($total_gov as $gi => $v) {
    $totalRow[$col_gov1 - 1 + $gi] = $TAG_TOT . ($v != 0 ? number_format($v, 2) : '-');
}
$totalRow[$col_net - 1] = $TAG_TOT . number_format($total_net, 2);
$totalRow[$col_sig - 1] = $TAG_TOT_EMPTY;
$rows[] = $totalRow;

$totalExcelRow = 9 + count($payrollData);

// Spacer rows
$rows[] = emptyRow($last_col);
$rows[] = emptyRow($last_col);
$rows[] = emptyRow($last_col);

// Signatories
$signatories = $Payroll_Signatory->FetchActiveSignatoriesByReportType('PAYROLL');
$active_sigs = [];
foreach ($signatories as $sign) {
    if ($sign['role_type'] === 'HEAD' && $sign['dept_id'] != $dept_id) { continue; }
    $active_sigs[] = $sign;
}
$cols_per_sig    = max(1, (int)floor($last_col / 4));
$sig_groups      = array_chunk($active_sigs, 4);
$sigBaseExcelRow = $totalExcelRow + 4;

foreach ($sig_groups as $group) {
    while (count($group) < 4) { $group[] = null; }
    $rowA = emptyRow($last_col); $rowB = emptyRow($last_col);
    $rowC = emptyRow($last_col); $rowD = emptyRow($last_col);
    foreach ($group as $i => $sign) {
        $ci = $i * $cols_per_sig;
        if ($sign === null) { continue; }
        $rowA[$ci] = '<center><style font-size="8"><i>' . $sign['sign_particulars'];
        $rowB[$ci] = '<style border="none none thin none" height="20">';   // signature underline
        $rowC[$ci] = '<b><center><style font-size="10">'  . $sign['full_name'];
        $rowD[$ci] = '<center><style font-size="8">'       . $sign['position_title'];
    }
    $rows[] = $rowA; $rows[] = $rowB; $rows[] = $rowC; $rows[] = $rowD;
}

// ==========================================================
// INSTANTIATE + CONFIGURE
// ==========================================================
$xlsx = new SimpleXLSXGen();
$xlsx->setDefaultFont('Arial');
$xlsx->setDefaultFontSize(8);
$xlsx->setTitle('General Payroll - ' . $department_name);
$xlsx->setSubject('Municipality of Polanco - General Payroll');
$xlsx->setAuthor('Municipality of Polanco');
$xlsx->setCompany('Municipality of Polanco');
$xlsx->setLanguage('en-PH');   // available in SimpleXLSXGen — not in old CodexWorld fork

$xlsx->addSheet($rows, 'Payroll');

// ==========================================================
// MERGE CELLS
// ==========================================================
for ($r = 1; $r <= 6; $r++) {
    $xlsx->mergeCells(cellRange($col_no, $r, $last_col, $r));
}
$xlsx->mergeCells(cellRange($col_no,   7, $col_no,   8));
$xlsx->mergeCells(cellRange($col_name, 7, $col_name, 8));
$xlsx->mergeCells(cellRange($col_net,  7, $col_net,  8));
$xlsx->mergeCells(cellRange($col_sig,  7, $col_sig,  8));
$xlsx->mergeCells(cellRange($col_basic, 7, $col_gross,                  7));
$xlsx->mergeCells(cellRange($col_ded1,  7, $col_ded1 + $ded_count - 1, 7));
$xlsx->mergeCells(cellRange($col_gov1,  7, $col_gov1 + $gov_count - 1, 7));
$xlsx->mergeCells(cellRange($col_no, $totalExcelRow, $col_name, $totalExcelRow));

foreach ($sig_groups as $groupIdx => $group) {
    while (count($group) < 4) { $group[] = null; }
    $groupBaseRow = $sigBaseExcelRow + ($groupIdx * 4);
    foreach ($group as $i => $sign) {
        if ($sign === null) { continue; }
        $cStart = $col_no + ($i * $cols_per_sig);
        $cEnd   = min($cStart + $cols_per_sig - 1, $last_col);
        for ($sl = 0; $sl <= 3; $sl++) {
            $xlsx->mergeCells(cellRange($cStart, $groupBaseRow + $sl, $cEnd, $groupBaseRow + $sl));
        }
    }
}

// ==========================================================
// COLUMN WIDTHS
// ==========================================================
$xlsx->setColWidth($col_no,   5);
$xlsx->setColWidth($col_name, 28);
for ($c = $col_basic; $c <= $col_gross; $c++)            { $xlsx->setColWidth($c, 13); }
for ($c = $col_ded1;  $c < $col_ded1 + $ded_count; $c++) { $xlsx->setColWidth($c, 12); }
for ($c = $col_gov1;  $c < $col_gov1 + $gov_count; $c++) { $xlsx->setColWidth($c, 12); }
$xlsx->setColWidth($col_net, 14);
$xlsx->setColWidth($col_sig, 18);

$xlsx->freezePanes('A9');

// ==========================================================
// DOWNLOAD
// ==========================================================
$safe_dept = preg_replace('/[^A-Za-z0-9_\-]/', '_', $department_name);
$xlsx->downloadAs('general_payroll_' . $safe_dept . '_' . date('Ymd') . '.xlsx');
exit;
