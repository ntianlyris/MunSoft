<?php
/**
 * DEDUCTION REGISTERS EXCEL EXPORT
 * Exports deduction register data to Excel (single type or all types)
 * 
 * Parameters:
 * - action: 'export_type' or 'export_all'
 * - type: deduction type (tax, gsis, gsis_ecc, philhealth, pagibig, sss, loans, others)
 * - format: 'excel'
 * - year: fiscal year
 * - period_id: payroll period ID
 * - dept_id: (optional) department ID
 */

session_start();
require_once('../includes/simplexlsxgen/SimpleXLSXGen.php');
require_once('../includes/view/functions.php');
require_once('../includes/class/DB_conn.php');
require_once('../includes/class/Remittance.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Admin.php');
require_once('../includes/class/AccessControl.php');
require_once('../includes/class/Signatory.php');

use Shuchkin\SimpleXLSXGen;

// ===== RBAC: Permission Check =====
$MyAdmin = new Admin();
$Access = new AccessControl($MyAdmin);
$Access->syncToGlobals();

global $access_payroll;
if (!isset($access_payroll) || !$access_payroll) {
    header('Content-Type: application/json');
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Forbidden: You do not have Payroll access permission']));
}

// ===== PARAMETER VALIDATION =====
$action = $_GET['action'] ?? null;
$type = $_GET['type'] ?? null;
$year = intval($_GET['year'] ?? 0);
$period_id = intval($_GET['period_id'] ?? 0);
$dept_id = !empty($_GET['dept_id']) ? intval($_GET['dept_id']) : null;

if (!$action || !$year || !$period_id) {
    header('Content-Type: application/json');
    die(json_encode(['status' => 'error', 'message' => 'Missing required parameters: action=' . $action . ', year=' . $year . ', period_id=' . $period_id]));
}

if (!in_array($action, ['export_type', 'export_all', 'export_breakdown'])) {
    header('Content-Type: application/json');
    die(json_encode(['status' => 'error', 'message' => 'Invalid action']));
}

if ($action === 'export_type' && !$type) {
    header('Content-Type: application/json');
    die(json_encode(['status' => 'error', 'message' => 'Missing type parameter']));
}

// ===== INITIALIZE CLASSES =====
$db = new DB_conn();
$remittance = new Remittance();
$payroll = new Payroll();
$Signatory = new Signatory();

// ===== FETCH PERIOD INFO (Skip if export_breakdown as it provides its own period) =====
$start_date = null;
$end_date = null;
$display_period = '';
$period_format = '';

if ($action !== 'export_breakdown') {
    $periodInfo = $payroll->GetPayrollPeriodByID($period_id);
    if (!$periodInfo) {
        header('Content-Type: application/json');
        die(json_encode(['status' => 'error', 'message' => 'Invalid period ID: ' . $period_id]));
    }

    $start_date = $periodInfo['date_start'] ?? null;
    $end_date = $periodInfo['date_end'] ?? null;

    if (!$start_date || !$end_date) {
        header('Content-Type: application/json');
        die(json_encode(['status' => 'error', 'message' => 'Period has no date information']));
    }

    $period_format = $start_date . '_' . $end_date;
    $display_period = "For the period: " . OutputShortDate($start_date) . " to " . OutputShortDate($end_date);
} else {
    // For breakdown, period is passed as YYYY-MM-DD_YYYY-MM-DD
    $period_raw = $_POST['period'] ?? '';
    if (strpos($period_raw, '_') !== false) {
        list($s, $e) = explode('_', $period_raw);
        $display_period = "For the period: " . OutputShortDate($s) . " to " . OutputShortDate($e);
    } else {
        $display_period = "Period: " . $period_raw;
    }
}

// ==========================================================
// STYLE TAGS (Mirrored from export_remit_details_excel.php)
// ==========================================================
$BG_HEADER = 'FFF2F2F2';   // light gray
$BG_TOTAL  = 'FFFAFAFA';   // near-white
$NF_MONEY  = '#,##0.00';   // currency number format

$H_MUN      = '<center><style font-size="11">';                  
$H_TITLE    = '<b><center><style font-size="14">';               
$H_REPTTITLE= '<b><center><style font-size="11">';               
$H_PERIOD   = '<center><style font-size="9">';                   
$T_HDR      = '<b><center><middle><style bgcolor="' . $BG_HEADER . '" border="thin" font-size="9">';  
$T_NO       = '<center><middle><style border="thin" font-size="9">';               
$T_LEFT     = '<left><middle><style border="thin" font-size="9">';                 
$T_RIGHT    = '<right><middle><style border="thin" nf="' . $NF_MONEY . '" font-size="9">'; 
$T_TOT_LBL  = '<b><right><middle><style bgcolor="' . $BG_TOTAL . '" border="thin" font-size="9">';   
$T_TOT_NUM  = '<b><right><middle><style bgcolor="' . $BG_TOTAL . '" border="thin" nf="' . $NF_MONEY . '" font-size="9">'; 

// ==========================================================
// HELPERS
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

function cleanPosition($title, $code = '') {
    if (empty($title)) return '';
    return trim(explode('(', $title)[0]);
}

// ===== EXPORT BREAKDOWN (NEW) =====
if ($action === 'export_breakdown') {
    $type = $_POST['type'] ?? 'loans_breakdown';
    $breakdown_json = $_POST['breakdown'] ?? '[]';
    $data = json_decode($breakdown_json, true) ?: [];
    $title = $_POST['title'] ?? 'Breakdown';
    generateExcel('breakdown', $data, $display_period, $dept_id, $Signatory, $type, $title);
}
// ===== EXPORT SINGLE TYPE =====
elseif ($action === 'export_type') {
    $data = fetchDeductionType($remittance, $type, $year, $period_format);
    generateExcel($type, $data, $display_period, $dept_id, $Signatory);
}
// ===== EXPORT ALL TYPES =====
else {
    $types = ['tax', 'gsis', 'gsis_ecc', 'philhealth', 'pagibig', 'sss', 'loans', 'others'];
    $all_data = [];
    foreach ($types as $t) {
        $all_data[$t] = fetchDeductionType($remittance, $t, $year, $period_format);
    }
    generateExcel('all', $all_data, $display_period, $dept_id, $Signatory);
}

// ===== FUNCTION: Fetch Deduction Type Data =====
function fetchDeductionType($remittance, $type, $year, $period) {
    $data = [];
    switch ($type) {
        case 'tax':        $data = $remittance->GetRemittanceBIRTax($year, $period); break;
        case 'gsis':       $data = $remittance->GetRemittanceGSIS($year, $period); break;
        case 'gsis_ecc':   $data = $remittance->GetRemittanceGSISECC($year, $period); break;
        case 'philhealth': $data = $remittance->GetRemittancePhilHealth($year, $period); break;
        case 'pagibig':    $data = $remittance->GetRemittancePagibig($year, $period); break;
        case 'sss':        $data = $remittance->GetRemittanceSSS($year, $period); break;
        case 'loans':      $data = $remittance->GetRemittanceLoans($year, $period); break;
        case 'others':     $data = $remittance->GetRemittanceOthers($year, $period); break;
    }
    return $data ?? [];
}

// ===== FUNCTION: Generate Excel (Unified for Single or All) =====
function generateExcel($export_mode, $data, $period_text, $dept_id, $SignatoryObj, $breakdown_type = '', $breakdown_title = '') {
    global $H_MUN, $H_TITLE, $H_REPTTITLE, $H_PERIOD, $T_HDR, $T_NO, $T_LEFT, $T_RIGHT, $T_TOT_LBL, $T_TOT_NUM;

    $xlsx = new SimpleXLSXGen();
    $xlsx->setDefaultFont('Arial');
    $xlsx->setDefaultFontSize(10);

    $type_titles = [
        'tax' => 'BIR Tax', 'gsis' => 'GSIS', 'gsis_ecc' => 'GSIS ECC', 
        'philhealth' => 'PhilHealth', 'pagibig' => 'Pag-IBIG', 'sss' => 'SSS', 
        'loans' => 'Loans', 'others' => 'Other Deductions'
    ];

    if ($export_mode === 'breakdown') {
        $export_types = [$breakdown_type];
    } else {
        $export_types = ($export_mode === 'all') ? array_keys($data) : [$export_mode];
    }
    
    $sheet_count = 0;

    foreach ($export_types as $type) {
        if ($export_mode === 'breakdown') {
            $type_data = $data;
            $type_title = $breakdown_title . ' - Breakdown';
        } else {
            $type_data = ($export_mode === 'all') ? $data[$type] : $data;
            $type_title = $type_titles[$type] ?? 'Deduction Register';
        }
        
        // Define Columns and Widths
        $config = getTableConfig($type);
        $num_cols = $config['num_cols'];
        $col_widths = $config['widths'];
        $headers = $config['headers'];
        
        $rows = [];
        // Header Rows (Rows 1-6)
        $r = eRow($num_cols); $r[0] = $H_MUN . 'Municipality of Polanco, Z.N.'; $rows[] = $r;
        $r = eRow($num_cols); $r[0] = $H_TITLE . 'DEDUCTION REGISTER'; $rows[] = $r;
        $rows[] = eRow($num_cols); // spacer
        $r = eRow($num_cols); $r[0] = $H_REPTTITLE . strtoupper($type_title); $rows[] = $r;
        $r = eRow($num_cols); $r[0] = $H_PERIOD . $period_text; $rows[] = $r;
        $rows[] = eRow($num_cols); // spacer

        // Table Header
        $header_row = [];
        foreach ($headers as $h) { $header_row[] = $T_HDR . $h; }
        $rows[] = $header_row;

        // Data Rows
        $total_amount = 0;
        $total_share_1 = 0; // for GSIS/PHIC employer share
        $count = 0;

        if (empty($type_data)) {
            $fallback = eRow($num_cols);
            $fallback[0] = $T_LEFT . 'No records found for this period';
            $rows[] = $fallback;
        } else {
            foreach ($type_data as $row) {
                $count++;
                $item = buildRowData($type, $row, $count, $T_NO, $T_LEFT, $T_RIGHT);
                $rows[] = $item['row'];
                $total_amount += $item['amount'];
                $total_share_1 += $item['share_1'] ?? 0;
            }
        }

        // Total Row
        $total_row_idx = count($rows) + 1;
        $total_row = buildTotalRow($type, $num_cols, $total_amount, $total_share_1, $T_TOT_LBL, $T_TOT_NUM);
        if ($total_row) $rows[] = $total_row;

        // Signatories
        $rows[] = eRow($num_cols); // spacer
        $rows[] = eRow($num_cols); // spacer
        
        $signatories = getSignatories($SignatoryObj, $dept_id);
        $sig_start_row = count($rows) + 1;
        $sig_blocks = appendSignatories($rows, $signatories, $num_cols);

        // Add Sheet
        $sheet_name = ($export_mode === 'all') ? substr(strtoupper($type), 0, 31) : 'Deduction Register';
        $xlsx->addSheet($rows, $sheet_name);
        
        // Merges & Widths for this sheet
        applySheetFormatting($xlsx, $sheet_name, $num_cols, $total_row_idx, $sig_start_row, $sig_blocks, $col_widths, !empty($type_data));
        
        $sheet_count++;
    }

    $filename = "Deduction_Register_" . ($export_mode === 'all' ? 'Complete' : ($export_mode === 'breakdown' ? 'Breakdown' : $export_mode)) . "_" . date('YmdHis') . ".xlsx";
    $xlsx->downloadAs($filename);
    exit;
}

function getTableConfig($type) {
    switch ($type) {
        case 'tax':
        case 'sss':
            return [
                'num_cols' => 6, 
                'widths' => [1 => 6, 2 => 35, 3 => 35, 4 => 20, 5 => 20, 6 => 20],
                'headers' => ['#', 'Employee', 'Position', 'TIN/SSS', 'Basic Salary', 'Amount']
            ];
        case 'gsis':
        case 'philhealth':
        case 'pagibig':
            return [
                'num_cols' => 8, 
                'widths' => [1 => 6, 2 => 35, 3 => 35, 4 => 20, 5 => 20, 6 => 20, 7 => 20, 8 => 20],
                'headers' => ['#', 'Employee', 'Position', 'ID Number', 'Basic Salary', 'Employee Share', 'Employer Share', 'Total']
            ];
        case 'gsis_ecc':
            return [
                'num_cols' => 5, 
                'widths' => [1 => 6, 2 => 35, 3 => 35, 4 => 20, 5 => 20],
                'headers' => ['#', 'Employee', 'Position', 'GSIS BP', 'Employer Share']
            ];
        case 'loans':
        case 'others':
            return [
                'num_cols' => 3, 
                'widths' => [1 => 6, 2 => 60, 3 => 22],
                'headers' => ['#', 'Deduction Particulars', 'Total Amount']
            ];
        case 'loans_breakdown':
        case 'others_breakdown':
            return [
                'num_cols' => 4,
                'widths' => [1 => 6, 2 => 35, 3 => 35, 4 => 22],
                'headers' => ['#', 'Employee Name', 'Position', 'Amount']
            ];
        default:
            return [
                'num_cols' => 4, 
                'widths' => [1 => 10, 2 => 40, 3 => 20, 4 => 20],
                'headers' => ['#', 'Description', 'Detail', 'Amount']
            ];
    }
}

function buildRowData($type, $row, $count, $T_NO, $T_LEFT, $T_RIGHT) {
    $emp = $row['employee'] ?? $row['employee_name'] ?? '';
    $pos = cleanPosition($row['position_title'] ?? '');
    $basic = $row['locked_basic'] ?? 0;
    
    switch ($type) {
        case 'tax':
            $amt = $row['amount'] ?? 0;
            return ['amount' => $amt, 'row' => [$T_NO . $count, $T_LEFT . $emp, $T_LEFT . $pos, $T_LEFT . ($row['tin'] ?? 'N/A'), $T_RIGHT . number_format($basic, 2), $T_RIGHT . number_format($amt, 2)]];
        case 'sss':
            $amt = $row['employee_share'] ?? $row['contribution_amount'] ?? 0;
            return ['amount' => $amt, 'row' => [$T_NO . $count, $T_LEFT . $emp, $T_LEFT . $pos, $T_LEFT . ($row['sss_no'] ?? 'N/A'), $T_RIGHT . number_format($basic, 2), $T_RIGHT . number_format($amt, 2)]];
        case 'gsis':
            $total = $row['total_amount'] ?? 0;
            return ['amount' => $total, 'share_1' => $row['employer_share'] ?? 0, 'row' => [$T_NO . $count, $T_LEFT . $emp, $T_LEFT . $pos, $T_LEFT . ($row['gsis_bp'] ?? 'N/A'), $T_RIGHT . number_format($basic, 2), $T_RIGHT . number_format($row['employee_share'] ?? 0, 2), $T_RIGHT . number_format($row['employer_share'] ?? 0, 2), $T_RIGHT . number_format($total, 2)]];
        case 'philhealth':
        case 'pagibig':
            $total = $row['total'] ?? 0;
            $id = ($type === 'philhealth') ? ($row['philhealth_no'] ?? 'N/A') : ($row['mid_no'] ?? $row['pagibig_mid'] ?? 'N/A');
            return ['amount' => $total, 'share_1' => $row['employer_share'] ?? 0, 'row' => [$T_NO . $count, $T_LEFT . $emp, $T_LEFT . $pos, $T_LEFT . $id, $T_RIGHT . number_format($basic, 2), $T_RIGHT . number_format($row['employee_share'] ?? 0, 2), $T_RIGHT . number_format($row['employer_share'] ?? 0, 2), $T_RIGHT . number_format($total, 2)]];
        case 'gsis_ecc':
            $amt = $row['employer_share'] ?? 0;
            return ['amount' => $amt, 'row' => [$T_NO . $count, $T_LEFT . $emp, $T_LEFT . $pos, $T_LEFT . ($row['gsis_bp'] ?? 'N/A'), $T_RIGHT . number_format($amt, 2)]];
        case 'loans':
        case 'others':
            $label = ($row['deduct_code'] ?? '') . ' - ' . ($row['deduct_title'] ?? $row['loan_name'] ?? $row['name'] ?? 'Other');
            $amt = $row['total_loan_amount'] ?? $row['total_amount'] ?? 0;
            return ['amount' => $amt, 'row' => [$T_NO . $count, $T_LEFT . $label, $T_RIGHT . number_format($amt, 2)]];
        case 'loans_breakdown':
        case 'others_breakdown':
            $amt = $row['amount'] ?? 0;
            return ['amount' => $amt, 'row' => [$T_NO . $count, $T_LEFT . $emp, $T_LEFT . $pos, $T_RIGHT . number_format($amt, 2)]];
    }
    return ['amount' => 0, 'row' => []];
}

function buildTotalRow($type, $num_cols, $total, $share_1, $T_TOT_LBL, $T_TOT_NUM) {
    $row = eRow($num_cols);
    $row[0] = $T_TOT_LBL . 'TOTAL';
    
    switch ($type) {
        case 'tax':
        case 'sss':
            $row[5] = $T_TOT_NUM . number_format($total, 2); break;
        case 'gsis':
        case 'philhealth':
        case 'pagibig':
            $row[6] = $T_TOT_NUM . number_format($share_1, 2);
            $row[7] = $T_TOT_NUM . number_format($total, 2); break;
        case 'gsis_ecc':
            $row[4] = $T_TOT_NUM . number_format($total, 2); break;
        case 'loans':
        case 'others':
            $row[2] = $T_TOT_NUM . number_format($total, 2); break;
        case 'loans_breakdown':
        case 'others_breakdown':
            $row[3] = $T_TOT_NUM . number_format($total, 2); break;
    }
    return $row;
}

function getSignatories($Signatory, $dept_id) {
    $report_code = 'ACCTG';
    $signatories = $Signatory->FetchActiveSignatoriesByReportType($report_code);
    $filtered = [];
    foreach ($signatories as $sign) {
        if ($sign['role_type'] !== 'HEAD') { $filtered[] = $sign; continue; }
        if ($sign['role_type'] === 'HEAD' && $sign['dept_id'] == $dept_id) { $filtered[] = $sign; }
    }
    return $filtered;
}

function appendSignatories(&$rows, $signatories, $num_cols) {
    $sig_groups = array_chunk($signatories, 2);
    $half = (int)floor($num_cols / 2);
    if ($half < 1) $half = 1;
    $blocks = [];

    foreach ($sig_groups as $group) {
        while (count($group) < 2) { $group[] = null; }
        $rowA = eRow($num_cols); // Particulars
        $rowB = eRow($num_cols); // Signature Line
        $rowC = eRow($num_cols); // Name
        $rowD = eRow($num_cols); // Position

        foreach ($group as $i => $sign) {
            $colIdx = $i * $half;
            if ($sign === null) continue;
            $rowA[$colIdx] = '<left><style font-size="9"><i>' . $sign['sign_particulars'];
            $rowB[$colIdx] = '<style border="none none thin none" height="20">';
            $rowC[$colIdx] = '<b><left><style font-size="10">' . $sign['full_name'];
            $rowD[$colIdx] = '<left><style font-size="9">' . $sign['position_title'];
            $blocks[] = ['col' => $colIdx + 1, 'half' => $half];
        }
        $rows[] = $rowA; $rows[] = $rowB; $rows[] = $rowC; $rows[] = $rowD;
    }
    return $blocks;
}

function applySheetFormatting($xlsx, $sheet_name, $num_cols, $total_row_idx, $sig_start_row, $sig_blocks, $col_widths, $has_data) {
    // Header merges
    for ($i = 1; $i <= 6; $i++) {
        $xlsx->mergeCells(cr(1, $i, $num_cols, $i), $sheet_name);
    }
    
    // Total row merge
    if ($num_cols === 3) $xlsx->mergeCells(cr(1, $total_row_idx, 2, $total_row_idx), $sheet_name);
    elseif ($num_cols === 4) $xlsx->mergeCells(cr(1, $total_row_idx, 3, $total_row_idx), $sheet_name);
    elseif ($num_cols === 5) $xlsx->mergeCells(cr(1, $total_row_idx, 4, $total_row_idx), $sheet_name);
    elseif ($num_cols === 6) $xlsx->mergeCells(cr(1, $total_row_idx, 5, $total_row_idx), $sheet_name);
    elseif ($num_cols === 8) $xlsx->mergeCells(cr(1, $total_row_idx, 5, $total_row_idx), $sheet_name);

    if (!$has_data) {
        $xlsx->mergeCells(cr(1, 8, $num_cols, 8), $sheet_name);
    }

    // Signatory merges
    $row_offset = 0;
    foreach ($sig_blocks as $idx => $block) {
        $cur_row = $sig_start_row + (floor($idx / 2) * 4);
        $cStart = $block['col'];
        $cEnd = min($cStart + $block['half'] - 1, $num_cols);
        for ($sl = 0; $sl <= 3; $sl++) {
            $xlsx->mergeCells(cr($cStart, $cur_row + $sl, $cEnd, $cur_row + $sl), $sheet_name);
        }
    }

    // Widths
    foreach ($col_widths as $col => $width) {
        $xlsx->setColWidth($col, $width, $sheet_name);
    }
}
?>
