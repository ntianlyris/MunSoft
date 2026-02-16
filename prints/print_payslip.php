<?php
require_once('..//includes/tcpdf/tcpdf.php');
require_once('..//includes/view/functions.php');
require_once '..//includes/class/Payslip.php';
require_once '..//includes/class/Employee.php';
require_once '..//includes/class/Employment.php';

// Get parameters - support both employee_id (from payroll side) and user_id (from employee side)
$employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : (isset($_GET['employee_id']) ? $_GET['employee_id'] : '');
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : (isset($_GET['user_id']) ? $_GET['user_id'] : '');
$year = isset($_POST['year']) ? $_POST['year'] : (isset($_GET['year']) ? $_GET['year'] : '');
$payroll_period = isset($_POST['payroll_period']) ? $_POST['payroll_period'] : (isset($_GET['payroll_period']) ? $_GET['payroll_period'] : '');

// If user_id is provided instead of employee_id, convert it
if (!empty($user_id) && empty($employee_id)) {
    $Employee = new Employee();
    $employee_id = $Employee->getEmployeeIDByUserId($user_id);
    if (!$employee_id) {
        die('Error: Could not find employee record for the logged-in user.');
    }
}

// Validate parameters
if (empty($employee_id) || empty($year) || empty($payroll_period)) {
    die('Invalid parameters. Please provide employee_id (or user_id), year, and payroll_period.');
}

// Validate payroll_period format
$period_parts = explode('_', $payroll_period);
if (count($period_parts) < 2) {
    die('Invalid payroll_period format. Expected format: YYYY-MM-DD_YYYY-MM-DD (e.g., 2026-03-16_2026-03-31)');
}

// Generate payslip data
try {
    $PaySlip = new Payslip();
    $payslip = $PaySlip->GeneratePayslip($employee_id, $year, $payroll_period);
} catch (Exception $e) {
    die('Error generating payslip: ' . htmlspecialchars($e->getMessage()));
}

// Validate payslip coverage data exists
if (empty($payslip) || !isset($payslip['coverage'])) {
    die('Unable to generate payslip. Missing coverage information.');
}

$coverage_parts = explode('|', $payslip['coverage']);
if (count($coverage_parts) < 2) {
    die('Invalid coverage format in payslip data.');
}

$start_coverage = trim($coverage_parts[0]);
$end_coverage = trim($coverage_parts[1]);

// Parse coverage dates with error handling
$start_date_obj = DateTime::createFromFormat('m-d-Y', $start_coverage);
$end_date_obj = DateTime::createFromFormat('m-d-Y', $end_coverage);

if ($start_date_obj === false || $end_date_obj === false) {
    die('Error parsing coverage dates from payslip data.');
}

$start_coverage = $start_date_obj->format("m/d/Y");
$end_coverage = $end_date_obj->format("m/d/Y");

$date_issued = date('m-d-Y');
$date_issued_obj = DateTime::createFromFormat('m-d-Y', $date_issued);
if ($date_issued_obj === false) {
    die('Error formatting current date.');
}
$date_issued_formatted = $date_issued_obj->format("m/d/Y");

// Check if payslip data is valid
if (empty($payslip) || !isset($payslip['employee_num'])) {
    die('Unable to generate payslip. Please check the provided information.');
}

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('MunSoft Payroll System');
$pdf->SetAuthor('MunSoft');
$pdf->SetTitle('Payslip - ' . $payslip['employee_name']);
$pdf->SetSubject('Employee Payslip');

// Set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// Set margins
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(TRUE, 10);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Company Header
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 8, 'PAYSLIP', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'MunSoft Payroll System', 0, 1, 'C');
$pdf->Ln(3);

// Horizontal line
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(3);

// Employee Information Section
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 6, 'EMPLOYEE INFORMATION', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Left column for employee info
$leftX = 12;
$rightX = 105;
$yStart = $pdf->GetY();

// Employee details in two columns
$pdf->SetXY($leftX, $yStart);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 5, 'Employee No:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 5, $payslip['employee_num'], 0, 1, 'L');

$pdf->SetXY($leftX, $pdf->GetY());
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 5, 'Name:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 5, $payslip['employee_name'], 0, 1, 'L');

$pdf->SetXY($leftX, $pdf->GetY());
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 5, 'Position:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 5, $payslip['position'], 0, 1, 'L');

$pdf->SetXY($leftX, $pdf->GetY());
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 5, 'Department:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 5, $payslip['department'], 0, 1, 'L');

// Right column for period info
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY($rightX, $yStart);
$pdf->Cell(35, 5, 'Period Coverage:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 5, $start_coverage . ' - ' . $end_coverage, 0, 1, 'L');

$pdf->SetXY($rightX, $pdf->GetY());
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(35, 5, 'Date Issued:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50, 5, $date_issued_formatted, 0, 1, 'L');

$pdf->Ln(10);

// Horizontal line
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(3);

// Earnings and Deductions Section
$leftColWidth = 92;
$rightColWidth = 92;

// Earnings Table
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell($leftColWidth, 6, 'EARNINGS', 0, 0, 'L');
$pdf->Cell($rightColWidth, 6, 'DEDUCTIONS', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);

// Earnings content
$earnings_y = $pdf->GetY();

$pdf->SetXY(12, $earnings_y);
$pdf->SetDrawColor(200, 200, 200);
$pdf->Rect(12, $earnings_y, $leftColWidth - 4, 5, 'D');
$pdf->Cell($leftColWidth - 4, 5, 'Description', 0, 0, 'L');

$pdf->SetXY(105, $earnings_y);
$pdf->Rect(105, $earnings_y, $rightColWidth - 4, 5, 'D');
$pdf->Cell($rightColWidth - 4, 5, 'Description', 0, 1, 'L');

// Basic Pay
$earnings_y += 5;
$pdf->SetXY(12, $earnings_y);
$pdf->Cell($leftColWidth - 8, 5, 'BASIC', 0, 0, 'L');
$pdf->Cell(8, 5, number_format($payslip['basic'], 2), 0, 1, 'R');

// Other Earnings
$earnings_y = $pdf->GetY();
if ($payslip['other_earnings'] > 0) {
    $pdf->SetXY(12, $earnings_y);
    $pdf->Cell($leftColWidth - 8, 5, 'OTHER INCOME', 0, 0, 'L');
    $pdf->Cell(8, 5, number_format($payslip['other_earnings'], 2), 0, 1, 'R');
    $earnings_y = $pdf->GetY();
}

// Gross Pay
$pdf->SetXY(12, $earnings_y);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell($leftColWidth - 8, 5, 'GROSS PAY', 0, 0, 'L', true);
$pdf->Cell(8, 5, number_format($payslip['gross'], 2), 0, 1, 'R', true);

// Deductions content
$deductions_y = $earnings_y - 10;
$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(105, $deductions_y);

$deduction_items = $payslip['deductions'];
$total_deductions = 0;

if (is_array($deduction_items)) {
    foreach ($deduction_items as $key => $deduction) {
        if ($key !== 'total_deductions' && is_array($deduction) && isset($deduction['amount'])) {
            $pdf->SetXY(105, $deductions_y);
            $label = isset($deduction['label']) ? $deduction['label'] : '';
            $amount = floatval($deduction['amount']);
            $total_deductions += $amount;
            
            $pdf->Cell($rightColWidth - 8, 5, $label, 0, 0, 'L');
            $pdf->Cell(8, 5, number_format($amount, 2), 0, 1, 'R');
            $deductions_y = $pdf->GetY();
        }
    }
}

// Total Deductions
$pdf->SetXY(105, $deductions_y);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$total_ded = isset($deduction_items['total_deductions']) ? $deduction_items['total_deductions'] : $total_deductions;
$pdf->Cell($rightColWidth - 8, 5, 'TOTAL DEDUCTIONS', 0, 0, 'L', true);
$pdf->Cell(8, 5, number_format($total_ded, 2), 0, 1, 'R', true);

$pdf->Ln(2);

// Horizontal line
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(3);

// Net Pay Section
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(100, 150, 200);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 10, 'NET PAY: ' . number_format($payslip['net_pay'], 2), 0, 1, 'C', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(1);

// Footer
$pdf->SetFont('helvetica', '', 7);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 4, 'This is an electronically generated document. No signature is required.', 0, 1, 'C');
$pdf->Cell(0, 4, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');

// Output PDF to browser
$filename = 'Payslip_' . str_replace(' ', '_', $payslip['employee_name']) . '_' . $year . '.pdf';
ob_end_clean();
$pdf->Output($filename, 'I');
?>
