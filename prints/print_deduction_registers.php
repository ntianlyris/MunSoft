<?php
/**
 * DEDUCTION REGISTERS PDF EXPORT
 * Generates PDF exports for deduction register reports
 * Uses TCPDF library (same as print_remit_details.php)
 * 
 * POST Parameters:
 * - type: deduction type (tax, gsis, gsis_ecc, philhealth, pagibig, sss, loans, others)
 * - year: fiscal year
 * - period_id: payroll period ID
 * - dept_id: (optional) department ID
 * 
 * Note: period_label is constructed server-side from period_id to ensure accuracy
 */

ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

require_once('../includes/tcpdf/tcpdf.php');
require_once('../includes/view/functions.php');
require_once('../includes/class/DB_conn.php');
require_once('../includes/class/Remittance.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Admin.php');
require_once('../includes/class/Signatory.php');

// ===== PARAMETER VALIDATION =====
$type = $_POST['type'] ?? '';
$year = intval($_POST['year'] ?? 0);
$period_id = intval($_POST['period_id'] ?? 0);
$dept_id = !empty($_POST['dept_id']) ? intval($_POST['dept_id']) : null;

// Breakdown-specific parameters
$is_breakdown = ($type === 'loans_breakdown' || $type === 'others_breakdown');
$breakdown_json = $_POST['breakdown'] ?? '[]';
$breakdown_title = $_POST['title'] ?? 'Breakdown';
$breakdown_period_raw = $_POST['period'] ?? '';

if (!$type || (!$is_breakdown && (!$year || !$period_id))) {
    die('Missing required parameters');
}

// ===== INITIALIZE CLASSES =====
$db = new DB_conn();
$remittance = new Remittance();
$payroll = new Payroll();

// ===== FETCH PERIOD INFO =====
$period_label = '';
$period_format = '';

if (!$is_breakdown) {
    $periodInfo = $payroll->GetPayrollPeriodByID($period_id);
    if (!$periodInfo) {
        die('Invalid period ID');
    }

    $start_date = $periodInfo['date_start'];
    $end_date = $periodInfo['date_end'];

    $period_format = $start_date . '_' . $end_date;
    $period_label = date('Y-m-d', strtotime($start_date)) . ' to ' . date('Y-m-d', strtotime($end_date));
} else {
    // For breakdown, use passed period
    if (strpos($breakdown_period_raw, '_') !== false) {
        list($s, $e) = explode('_', $breakdown_period_raw);
        $period_label = date('Y-m-d', strtotime($s)) . ' to ' . date('Y-m-d', strtotime($e));
    } else {
        $period_label = $breakdown_period_raw;
    }
}

// ===== FETCH DEDUCTION DATA =====
function fetchDeductionType($remittance, $type, $year, $period, $dept_id = null) {
    $data = [];
    
    switch ($type) {
        case 'tax':
            $data = $remittance->GetRemittanceBIRTax($year, $period);
            break;
        case 'gsis':
            $data = $remittance->GetRemittanceGSIS($year, $period);
            break;
        case 'gsis_ecc':
            $data = $remittance->GetRemittanceGSISECC($year, $period);
            break;
        case 'philhealth':
            $data = $remittance->GetRemittancePhilHealth($year, $period);
            break;
        case 'pagibig':
            $data = $remittance->GetRemittancePagibig($year, $period);
            break;
        case 'sss':
            $data = $remittance->GetRemittanceSSS($year, $period);
            break;
        case 'loans':
            $data = $remittance->GetRemittanceLoans($year, $period);
            break;
        case 'others':
            $data = $remittance->GetRemittanceOthers($year, $period);
            break;
    }
    
    return $data ?? [];
}

if ($is_breakdown) {
    $data = json_decode($breakdown_json, true) ?: [];
} else {
    $data = fetchDeductionType($remittance, $type, $year, $period_format, $dept_id);
}

// ===== SETUP PDF =====
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('MunSoft Payroll System');
$pdf->SetAuthor('Municipality of Polanco');
$pdf->SetTitle('Deduction Register - ' . ucfirst(str_replace('_', ' ', $type)));
$pdf->SetSubject('Deduction Register Report');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Spacing and layout settings
$margin_lr = 12;
$margin_top = 12;
$margin_bottom = 12;
$cell_height = 5;
$cell_height_ratio = 0.95;
$font_size_title = 14;
$font_size_sub = 11;
$font_size_body = 9;

$pdf->SetMargins($margin_lr, $margin_top, $margin_lr);
$pdf->SetAutoPageBreak(true, $margin_bottom);
$pdf->setCellPaddings(1, 0.5, 1, 0.5);
$pdf->setCellHeightRatio($cell_height_ratio);

$pdf->AddPage();

// Header
$pdf->SetFont('helvetica', '', $font_size_sub);
$pdf->Cell(0, 5, 'Municipality of Polanco, Z.N.', 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', $font_size_title);
$pdf->Cell(0, 8, 'DEDUCTION REGISTER', 0, 1, 'C');

$pdf->SetFont('helvetica', '', $font_size_body);
$type_titles = [
    'tax' => 'Withholding Tax Register',
    'gsis' => 'GSIS (Loyalty & Retirement) Deductions',
    'gsis_ecc' => 'GSIS Emergency Cost Contribution (ECC)',
    'philhealth' => 'PhilHealth Premiums',
    'pagibig' => 'Pag-IBIG Contributions',
    'sss' => 'SSS Contributions',
    'loans' => 'Loan Deductions',
    'others' => 'Other Payables',
    'loans_breakdown' => $breakdown_title . ' - Breakdown',
    'others_breakdown' => $breakdown_title . ' - Breakdown'
];
$type_title = $type_titles[$type] ?? ucfirst(str_replace('_', ' ', $type));

$pdf->Cell(0, 5, $type_title, 0, 1, 'C');
$pdf->Cell(0, 5, 'Period: ' . $period_label, 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', $font_size_body);

// ===== BUILD TABLE HTML =====
$html_content = '';

if (empty($data)) {
    $html_content = '<table border="1" cellpadding="3">
        <tr><td style="text-align: center; color: #999;">No deduction records found for this period</td></tr>
    </table>';
} else {
    switch ($type) {
        case 'tax':
            $html_content = buildTaxTable($data);
            break;
        case 'gsis':
        case 'gsis_ecc':
        case 'philhealth':
        case 'pagibig':
            $html_content = buildShareTable($data, $type);
            break;
        case 'sss':
            $html_content = buildSSSTable($data);
            break;
        case 'loans':
            $html_content = buildLoansTable($data);
            break;
        case 'others':
            $html_content = buildOthersTable($data);
            break;
        case 'loans_breakdown':
        case 'others_breakdown':
            $html_content = buildBreakdownTable($data);
            break;
    }
}

$pdf->writeHTML($html_content, true, false, true, false, '');

// ===== ADD SIGNATORIES SECTION =====
$Signatory = new Signatory();
$report_code = 'ACCTG'; // Accounting report type

$pdf->Ln(10); // Space before footer
$pdf->SetFont('helvetica', '', $font_size_body);

// Get active signatories filtered by report type and department
$signatories = $Signatory->FetchActiveSignatoriesByReportType($report_code);
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

// Build signatory table
if (!empty($filtered_signatories)) {
    $signatories_per_row = 2;
    $total_signatories = count($filtered_signatories);
    $total_rows = ceil($total_signatories / $signatories_per_row);
    
    $signatory_table = '<table border="0" cellpadding="4" cellspacing="0" width="100%">';
    
    for ($row = 0; $row < $total_rows; $row++) {
        $signatory_table .= '<tr>';
        $start_index = $row * $signatories_per_row;
        $end_index = min(($row + 1) * $signatories_per_row, $total_signatories);
        
        for ($i = $start_index; $i < $end_index; $i++) {
            $sign = $filtered_signatories[$i];
            $signatory_table .= '
                <td width="50%" align="left">
                    ' . $sign['sign_particulars'] . '<br>
                    <br><br><br>
                    <b>' . $sign['full_name'] . '</b><br>
                    <span>' . $sign['position_title'] . '</span>
                </td>';
        }
        
        $remaining_cells = $signatories_per_row - ($end_index - $start_index);
        for ($i = 0; $i < $remaining_cells; $i++) {
            $signatory_table .= '<td width="50%"></td>';
        }
        $signatory_table .= '</tr>';
    }
    $signatory_table .= '</table>';
    $pdf->writeHTML($signatory_table, true, false, true, false, '');
}

// Output PDF
ob_end_clean();
$filename = 'Deduction_Register_' . $type . '_' . str_replace(' to ', '_to_', str_replace('-', '', $period_label)) . '.pdf';
$pdf->Output($filename, 'D');

// ===== TABLE BUILDER FUNCTIONS =====

function buildTaxTable($data) {
    $total = 0;
    $count = 0;
    
    $html = '<table border="1" cellpadding="3" style="font-size: 9px;">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th width="5%" style="text-align: center;">#</th>
                <th width="28%" style="text-align: center;">Employee Name</th>
                <th width="32%" style="text-align: center;">Position</th>
                <th width="15%" style="text-align: center;">TIN</th>
                <th width="10%" style="text-align: right;">Basic Salary</th>
                <th width="10%" style="text-align: right;">Tax Amount</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data as $row) {
        $count++;
        $amount = $row['amount'] ?? 0;
        $total += $amount;
        
        $position = cleanPositionTitle($row['position_title'] ?? '', $row['position_code'] ?? '');
        
        $html .= '<tr>
            <td width="5%" style="text-align: center;">' . $count . '</td>
            <td width="28%">' . htmlspecialchars(substr($row['employee'] ?? '', 0, 30)) . '</td>
            <td width="32%">' . htmlspecialchars(substr($position, 0, 35)) . '</td>
            <td width="15%" style="text-align: center;">' . htmlspecialchars($row['tin'] ?? 'N/A') . '</td>
            <td width="10%" style="text-align: right;">' . number_format($row['locked_basic'] ?? 0, 2) . '</td>
            <td width="10%" style="text-align: right;">' . number_format($amount, 2) . '</td>
        </tr>';
    }
    
    $html .= '<tr style="font-weight: bold; background-color: #f9f9f9;">
        <td colspan="5" style="text-align: right;">TOTAL:</td>
        <td style="text-align: right;">' . number_format($total, 2) . '</td>
    </tr>';
    
    $html .= '</tbody></table>';
    return $html;
}

function buildShareTable($data, $type) {
    $total = 0;
    $count = 0;
    $total_ee = 0;
    $total_er = 0;
    
    $type_labels = [
        'gsis' => 'GSIS BP',
        'gsis_ecc' => 'GSIS BP',
        'philhealth' => 'PhilHealth No.',
        'pagibig' => 'MID No.'
    ];
    
    $label_col = $type_labels[$type] ?? 'ID';
    $col_name = ($type === 'gsis' || $type === 'gsis_ecc') ? 'gsis_bp' : 
                ($type === 'philhealth' ? 'philhealth_no' : 'mid_no');
    $amount_field = ($type === 'gsis_ecc') ? 'employer_share' : 'total_amount';
    
    $html = '<table border="1" cellpadding="3" style="font-size: 8px;">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th width="4%" style="text-align: center;">#</th>
                <th width="22%" style="text-align: center;">Employee Name</th>
                <th width="25%" style="text-align: center;">Position</th>
                <th width="12%" style="text-align: center;">' . $label_col . '</th>
                <th width="8%" style="text-align: right;">Basic</th>
                <th width="9%" style="text-align: right;">E.Share</th>
                <th width="9%" style="text-align: right;">R.Share</th>
                <th width="11%" style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data as $row) {
        $count++;
        $amount = $row[$amount_field] ?? 0;
        $total += $amount;
        $ee_share = $row['employee_share'] ?? 0;
        $er_share = $row['employer_share'] ?? 0;
        $total_ee += $ee_share;
        $total_er += $er_share;
        
        $position = cleanPositionTitle($row['position_title'] ?? '', $row['position_code'] ?? '');
        $id_value = $row[$col_name] ?? 'N/A';
        
        $html .= '<tr>
            <td width="4%" style="text-align: center;">' . $count . '</td>
            <td width="22%">' . htmlspecialchars(substr($row['employee'] ?? '', 0, 25)) . '</td>
            <td width="25%">' . htmlspecialchars(substr($position, 0, 28)) . '</td>
            <td width="12%" style="text-align: center;">' . htmlspecialchars($id_value) . '</td>
            <td width="8%" style="text-align: right;">' . number_format($row['locked_basic'] ?? 0, 2) . '</td>
            <td width="9%" style="text-align: right;">' . number_format($ee_share, 2) . '</td>
            <td width="9%" style="text-align: right;">' . number_format($er_share, 2) . '</td>
            <td width="11%" style="text-align: right;">' . number_format($amount, 2) . '</td>
        </tr>';
    }
    
    $html .= '<tr style="font-weight: bold; background-color: #f9f9f9;">
        <td colspan="5" style="text-align: right;">TOTAL:</td>
        <td style="text-align: right;">' . number_format($total_ee, 2) . '</td>
        <td style="text-align: right;">' . number_format($total_er, 2) . '</td>
        <td style="text-align: right;">' . number_format($total, 2) . '</td>
    </tr>';
    
    $html .= '</tbody></table>';
    return $html;
}

function buildSSSTable($data) {
    $total = 0;
    $count = 0;
    
    $html = '<table border="1" cellpadding="3" style="font-size: 9px;">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th width="5%" style="text-align: center;">#</th>
                <th width="28%" style="text-align: center;">Employee Name</th>
                <th width="32%" style="text-align: center;">Position</th>
                <th width="15%" style="text-align: center;">SSS No.</th>
                <th width="20%" style="text-align: right;">Deduction Amount</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data as $row) {
        $count++;
        $amount = $row['employee_share'] ?? $row['contribution_amount'] ?? 0;
        $total += $amount;
        
        $position = cleanPositionTitle($row['position_title'] ?? '', $row['position_code'] ?? '');
        
        $html .= '<tr>
            <td width="5%" style="text-align: center;">' . $count . '</td>
            <td width="28%">' . htmlspecialchars(substr($row['employee'] ?? '', 0, 30)) . '</td>
            <td width="32%">' . htmlspecialchars(substr($position, 0, 35)) . '</td>
            <td width="15%" style="text-align: center;">' . htmlspecialchars($row['sss_no'] ?? 'N/A') . '</td>
            <td width="20%" style="text-align: right;">' . number_format($amount, 2) . '</td>
        </tr>';
    }
    
    $html .= '<tr style="font-weight: bold; background-color: #f9f9f9;">
        <td colspan="4" style="text-align: right;">TOTAL:</td>
        <td style="text-align: right;">' . number_format($total, 2) . '</td>
    </tr>';
    
    $html .= '</tbody></table>';
    return $html;
}

function buildLoansTable($data) {
    $total = 0;
    $count = 0;
    
    $html = '<table border="1" cellpadding="3" style="font-size: 9px;">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th width="5%" style="text-align: center;">#</th>
                <th width="75%" style="text-align: center;">Loan Type</th>
                <th width="20%" style="text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data as $row) {
        $count++;
        $amount = $row['total_loan_amount'] ?? $row['total_amount'] ?? 0;
        $total += $amount;
        
        $label = ($row['deduct_code'] ?? '') . ' - ' . ($row['deduct_title'] ?? $row['loan_name'] ?? 'Loan');
        
        $html .= '<tr>
            <td width="5%" style="text-align: center;">' . $count . '</td>
            <td width="75%">' . htmlspecialchars(substr($label, 0, 60)) . '</td>
            <td width="20%" style="text-align: right;">' . number_format($amount, 2) . '</td>
        </tr>';
    }
    
    $html .= '<tr style="font-weight: bold; background-color: #f9f9f9;">
        <td colspan="2" style="text-align: right;">TOTAL:</td>
        <td style="text-align: right;">' . number_format($total, 2) . '</td>
    </tr>';
    
    $html .= '</tbody></table>';
    return $html;
}

function buildOthersTable($data) {
    $total = 0;
    $count = 0;
    
    $html = '<table border="1" cellpadding="3" style="font-size: 9px;">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th width="5%" style="text-align: center;">#</th>
                <th width="75%" style="text-align: center;">Deduction Type</th>
                <th width="20%" style="text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data as $row) {
        $count++;
        $amount = $row['total_amount'] ?? $row['amount'] ?? 0;
        $total += $amount;
        
        $label = ($row['deduct_code'] ?? $row['code'] ?? '') . ' - ' . ($row['deduct_title'] ?? $row['name'] ?? 'Deduction');
        
        $html .= '<tr>
            <td width="5%" style="text-align: center;">' . $count . '</td>
            <td width="75%">' . htmlspecialchars(substr($label, 0, 60)) . '</td>
            <td width="20%" style="text-align: right;">' . number_format($amount, 2) . '</td>
        </tr>';
    }
    
    $html .= '<tr style="font-weight: bold; background-color: #f9f9f9;">
        <td colspan="2" style="text-align: right;">TOTAL:</td>
        <td style="text-align: right;">' . number_format($total, 2) . '</td>
    </tr>';
    
    $html .= '</tbody></table>';
    return $html;
}

function buildBreakdownTable($data) {
    $total = 0;
    $count = 0;
    
    $html = '<table border="1" cellpadding="3" style="font-size: 9px;">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th width="5%" style="text-align: center;">#</th>
                <th width="40%" style="text-align: center;">Employee Name</th>
                <th width="35%" style="text-align: center;">Position</th>
                <th width="20%" style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data as $row) {
        $count++;
        $amount = $row['amount'] ?? 0;
        $total += $amount;
        
        $position = cleanPositionTitle($row['position_title'] ?? '', '');
        
        $html .= '<tr>
            <td width="5%" style="text-align: center;">' . $count . '</td>
            <td width="40%">' . htmlspecialchars(substr($row['employee_name'] ?? '', 0, 40)) . '</td>
            <td width="35%">' . htmlspecialchars(substr($position, 0, 35)) . '</td>
            <td width="20%" style="text-align: right;">' . number_format($amount, 2) . '</td>
        </tr>';
    }
    
    $html .= '<tr style="font-weight: bold; background-color: #f9f9f9;">
        <td colspan="3" style="text-align: right;">TOTAL:</td>
        <td style="text-align: right;">' . number_format($total, 2) . '</td>
    </tr>';
    
    $html .= '</tbody></table>';
    return $html;
}

function cleanPositionTitle($title, $code) {
    if ($code && strpos($title, '(' . $code . ')') !== false) {
        return str_replace(' (' . $code . ')', '', $title);
    }
    return trim(explode('(', $title)[0]);
}
