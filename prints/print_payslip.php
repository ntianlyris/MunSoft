<?php
/**
 * Suppress PHP 8.x deprecation notices emitted by TCPDF 6.x.
 * Those notices are printed before any header is sent, which makes
 * TCPDF unable to deliver the PDF ("headers already sent" error).
 * Output buffering + display_errors=0 ensure no stray output reaches
 * the browser before ob_end_clean() is called just before Output().
 */
ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

require_once('../includes/tcpdf/tcpdf.php');
require_once('../includes/view/functions.php');
require_once '../includes/class/Payslip.php';
require_once '../includes/class/Employee.php';
require_once '../includes/class/Employment.php';

// ── Parameters ────────────────────────────────────────────────────────────────
$employee_id = $_POST['employee_id'] ?? $_GET['employee_id'] ?? '';
$user_id     = $_POST['user_id']     ?? $_GET['user_id']     ?? '';
$year           = $_POST['year']           ?? $_GET['year']           ?? '';
$payroll_period = $_POST['payroll_period'] ?? $_GET['payroll_period'] ?? '';

if (!empty($user_id) && empty($employee_id)) {
    $Employee    = new Employee();
    $employee_id = $Employee->getEmployeeIDByUserId($user_id);
    if (!$employee_id) {
        die('Error: Could not find employee record for the logged-in user.');
    }
}

if (empty($employee_id) || empty($year) || empty($payroll_period)) {
    die('Invalid parameters. Please provide employee_id (or user_id), year, and payroll_period.');
}

$period_parts = explode('_', $payroll_period);
if (count($period_parts) < 2) {
    die('Invalid payroll_period format. Expected format: YYYY-MM-DD_YYYY-MM-DD');
}

// ── Generate payslip data ─────────────────────────────────────────────────────
try {
    $PaySlip = new Payslip();
    
    // Check if the user is authorized to download/view the payslip yet
    $is_downloadable = $PaySlip->IsPayslipDownloadable($employee_id, $payroll_period);
    if (!$is_downloadable) {
        die('Error: Payslip is locked and not yet available for download. Wait until it is approved/paid and the period ends.');
    }
    
    $payslip = $PaySlip->GeneratePayslip($employee_id, $year, $payroll_period);
} catch (Exception $e) {
    die('Error generating payslip: ' . htmlspecialchars($e->getMessage()));
}

if (empty($payslip) || !isset($payslip['coverage'])) {
    die('Unable to generate payslip. Missing coverage information.');
}

$coverage_parts = explode('|', $payslip['coverage']);
if (count($coverage_parts) < 2) {
    die('Invalid coverage format in payslip data.');
}

$start_date_obj = DateTime::createFromFormat('m-d-Y', trim($coverage_parts[0]));
$end_date_obj   = DateTime::createFromFormat('m-d-Y', trim($coverage_parts[1]));
if (!$start_date_obj || !$end_date_obj) {
    die('Error parsing coverage dates from payslip data.');
}

$start_coverage       = $start_date_obj->format('m/d/Y');
$end_coverage         = $end_date_obj->format('m/d/Y');
$date_issued_formatted = date('m/d/Y');

if (empty($payslip['employee_num'])) {
    die('Unable to generate payslip. Please check the provided information.');
}

// ── Layout constants ──────────────────────────────────────────────────────────
// Page: 215.9 × 330.2 mm  (8.5 × 13 in folio)
// Margins: L=5 T=3 R=5  → usable width = 205.9 mm
//
// Two equal columns: each = 205.9 / 2 = 102.95 mm
// Left column  starts at x = 5
// Right column starts at x = 5 + 102.95 = 107.95
//
// Amount column width = 22 mm  (fits "999,999.99" at 6pt Helvetica)
// Label column width  = col_w - AMT_W  = 102.95 - 22 = 80.95 mm
// (1 mm inner padding already accounted for in Cell rendering)

define('PAGE_L',   5);       // left margin (mm)
define('PAGE_R',   210.9);   // right edge = 215.9 - 5
define('PAGE_T',   3);       // top margin (mm)
define('COL_W',    102.95);  // each half-width column
define('COL_R',    107.95);  // x-start of right column  (PAGE_L + COL_W)
define('AMT_W',    22);      // amount cell width
define('LBL_W',    COL_W - AMT_W);  // label cell width = 80.95
define('ROW_H',    3);       // standard row height (mm)
define('HDR_H',    4);       // section-header row height
define('SECTION_FONT', 8);
define('BODY_FONT',    7);
define('SMALL_FONT',   6);
define('TINY_FONT',    5);

// ── Colours ───────────────────────────────────────────────────────────────────
// TCPDF SetFillColor takes R,G,B integers 0-255
define('FG_GREY_R',  240);  define('FG_GREY_G',  240);  define('FG_GREY_B',  240);  // #F0F0F0
define('FG_BLUE_R',  100);  define('FG_BLUE_G',  150);  define('FG_BLUE_B',  200);  // #6496C8
define('FG_BORD_R',  200);  define('FG_BORD_G',  200);  define('FG_BORD_B',  200);  // #C8C8C8

// ── PDF setup ─────────────────────────────────────────────────────────────────
$pdf = new TCPDF('P', 'mm', [215.9, 330.2], true, 'UTF-8', false);

$pdf->SetCreator('MunSoft Payroll System');
$pdf->SetAuthor('MunSoft');
$pdf->SetTitle('Payslip - ' . $payslip['employee_name']);
$pdf->SetSubject('Employee Payslip');
$pdf->SetDefaultMonospacedFont('courier');

$pdf->SetMargins(PAGE_L, PAGE_T, PAGE_L);   // L, T, R — all symmetric
$pdf->SetAutoPageBreak(true, PAGE_L);

$pdf->AddPage();

// ─────────────────────────────────────────────────────────────────────────────
// Helper: draw a full-width horizontal rule
// ─────────────────────────────────────────────────────────────────────────────
function drawRule($pdf) {
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);
    $pdf->Line(PAGE_L, $pdf->GetY(), PAGE_R, $pdf->GetY());
}

// ─────────────────────────────────────────────────────────────────────────────
// PAYSLIP title
// ─────────────────────────────────────────────────────────────────────────────
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, 'PAYSLIP', 0, 1, 'C');

$pdf->SetFont('helvetica', '', SMALL_FONT);
$pdf->Cell(0, ROW_H, 'MunSoft Payroll System', 0, 1, 'C');
$pdf->Ln(1);

// ─────────────────────────────────────────────────────────────────────────────
// Divider
// ─────────────────────────────────────────────────────────────────────────────
drawRule($pdf);
$pdf->Ln(1);

// ─────────────────────────────────────────────────────────────────────────────
// EMPLOYEE INFORMATION — two equal-width columns
//
// Left col  (x=5,    w=102.95):  label 35mm | value 67.95mm
// Right col (x=107.95, w=102.95): label 35mm | value 67.95mm
// ─────────────────────────────────────────────────────────────────────────────
$pdf->SetFont('helvetica', 'B', SECTION_FONT);
$pdf->Cell(0, HDR_H, 'EMPLOYEE INFORMATION', 0, 1, 'L');

$INFO_LBL_W = 35;
$INFO_VAL_W = COL_W - $INFO_LBL_W;   // 67.95 mm

$yInfo = $pdf->GetY();

// ── Left column rows ─────────────────────────────────────────────────────────
$leftRows = [
    ['Employee No:', $payslip['employee_num']],
    ['Name:',        $payslip['employee_name']],
    ['Position:',    $payslip['position']],
    ['Department:',  $payslip['department']],
];
$yL = $yInfo;
foreach ($leftRows as [$lbl, $val]) {
    $pdf->SetXY(PAGE_L, $yL);
    $pdf->SetFont('helvetica', 'B', BODY_FONT);
    $pdf->Cell($INFO_LBL_W, ROW_H, $lbl, 0, 0, 'L');
    $pdf->SetFont('helvetica', '', BODY_FONT);
    $pdf->Cell($INFO_VAL_W, ROW_H, $val, 0, 1, 'L');
    $yL += ROW_H;
}

// ── Right column rows ────────────────────────────────────────────────────────
$rightRows = [
    ['Period:',      $start_coverage . ' - ' . $end_coverage],
    ['Date Issued:', $date_issued_formatted],
];
$yR = $yInfo;
foreach ($rightRows as [$lbl, $val]) {
    $pdf->SetXY(COL_R, $yR);
    $pdf->SetFont('helvetica', 'B', BODY_FONT);
    $pdf->Cell($INFO_LBL_W, ROW_H, $lbl, 0, 0, 'L');
    $pdf->SetFont('helvetica', '', BODY_FONT);
    $pdf->Cell($INFO_VAL_W, ROW_H, $val, 0, 1, 'L');
    $yR += ROW_H;
}

// Advance cursor past both columns
$pdf->SetY(max($yL, $yR) + 2);

// ─────────────────────────────────────────────────────────────────────────────
// Divider
// ─────────────────────────────────────────────────────────────────────────────
drawRule($pdf);
$pdf->Ln(1);

// ─────────────────────────────────────────────────────────────────────────────
// EARNINGS | DEDUCTIONS section headers
// ─────────────────────────────────────────────────────────────────────────────
$pdf->SetFont('helvetica', 'B', SECTION_FONT);
$pdf->SetX(PAGE_L);
$pdf->Cell(COL_W, ROW_H, 'EARNINGS',   0, 0, 'L');
$pdf->Cell(COL_W, ROW_H, 'DEDUCTIONS', 0, 1, 'L');

// ─────────────────────────────────────────────────────────────────────────────
// Sub-header row: "Description" | "Amount"  ×2
// ─────────────────────────────────────────────────────────────────────────────
$ySubHdr = $pdf->GetY();
$pdf->SetFont('helvetica', '', SMALL_FONT);
$pdf->SetDrawColor(FG_BORD_R, FG_BORD_G, FG_BORD_B);

// Left sub-header
$pdf->SetXY(PAGE_L, $ySubHdr);
$pdf->Cell(LBL_W, ROW_H, 'Description', 'B', 0, 'L');   // bottom border only
$pdf->Cell(AMT_W, ROW_H, 'Amount',      'B', 0, 'R');

// Right sub-header
$pdf->SetXY(COL_R, $ySubHdr);
$pdf->Cell(LBL_W, ROW_H, 'Description', 'B', 0, 'L');
$pdf->Cell(AMT_W, ROW_H, 'Amount',      'B', 1, 'R');

// Reset draw colour
$pdf->SetDrawColor(0, 0, 0);

// ─────────────────────────────────────────────────────────────────────────────
// EARNINGS rows
// ─────────────────────────────────────────────────────────────────────────────
$yEarnings = $pdf->GetY();

// BASIC
$pdf->SetXY(PAGE_L, $yEarnings);
$pdf->SetFont('helvetica', '', SMALL_FONT);
$pdf->Cell(LBL_W, ROW_H, 'BASIC', 0, 0, 'L');
$pdf->Cell(AMT_W, ROW_H, number_format($payslip['basic'], 2), 0, 0, 'R');
$yEarnings += ROW_H;

// OTHER INCOME (conditional)
if ($payslip['other_earnings'] > 0) {
    $pdf->SetXY(PAGE_L, $yEarnings);
    $pdf->Cell(LBL_W, ROW_H, 'OTHER INCOME', 0, 0, 'L');
    $pdf->Cell(AMT_W, ROW_H, number_format($payslip['other_earnings'], 2), 0, 0, 'R');
    $yEarnings += ROW_H;
}

// GROSS PAY (shaded)
$pdf->SetXY(PAGE_L, $yEarnings);
$pdf->SetFont('helvetica', 'B', SMALL_FONT);
$pdf->SetFillColor(FG_GREY_R, FG_GREY_G, FG_GREY_B);
$pdf->Cell(LBL_W, ROW_H, 'GROSS PAY', 0, 0, 'L', true);
$pdf->Cell(AMT_W, ROW_H, number_format($payslip['gross'], 2), 0, 0, 'R', true);
$yEarnings += ROW_H;

// ─────────────────────────────────────────────────────────────────────────────
// DEDUCTIONS rows — start at same Y as earnings rows (ySubHdr + ROW_H)
// ─────────────────────────────────────────────────────────────────────────────
$yDeductions   = $pdf->GetY() - ($yEarnings - ($ySubHdr + ROW_H + 3));   // sync to earnings start
$deduction_items = $payslip['deductions'];
$total_ded_calc  = 0;

$pdf->SetFont('helvetica', '', SMALL_FONT);
foreach ($deduction_items as $key => $deduction) {
    if ($key === 'total_deductions' || !is_array($deduction) || !isset($deduction['amount'])) {
        continue;
    }
    $label  = $deduction['label']  ?? '';
    $amount = (float)($deduction['amount'] ?? 0);
    $total_ded_calc += $amount;

    $pdf->SetXY(COL_R, $yDeductions);
    $pdf->Cell(LBL_W, ROW_H, $label, 0, 0, 'L');
    $pdf->Cell(AMT_W, ROW_H, number_format($amount, 2), 0, 0, 'R');
    $yDeductions += ROW_H;
}

// TOTAL DEDUCTIONS (shaded)
$total_ded = $deduction_items['total_deductions'] ?? $total_ded_calc;
$pdf->SetXY(COL_R, $yDeductions);
$pdf->SetFont('helvetica', 'B', SMALL_FONT);
$pdf->SetFillColor(FG_GREY_R, FG_GREY_G, FG_GREY_B);
$pdf->Cell(LBL_W, ROW_H, 'TOTAL DEDUCTIONS', 0, 0, 'L', true);
$pdf->Cell(AMT_W, ROW_H, number_format($total_ded, 2), 0, 0, 'R', true);
$yDeductions += ROW_H;

// ─────────────────────────────────────────────────────────────────────────────
// Advance cursor past whichever column is longer, then add 1mm gap
// ─────────────────────────────────────────────────────────────────────────────
$pdf->SetY(max($yEarnings, $yDeductions) + 1);

// ─────────────────────────────────────────────────────────────────────────────
// Divider
// ─────────────────────────────────────────────────────────────────────────────
drawRule($pdf);
$pdf->Ln(1);

// ─────────────────────────────────────────────────────────────────────────────
// NET PAY bar
// ─────────────────────────────────────────────────────────────────────────────
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(FG_BLUE_R, FG_BLUE_G, FG_BLUE_B);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 6, 'NET PAY: ' . number_format($payslip['net_pay'], 2), 0, 1, 'C', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(0.5);

// ─────────────────────────────────────────────────────────────────────────────
// Footer
// ─────────────────────────────────────────────────────────────────────────────
$pdf->SetFont('helvetica', '', TINY_FONT);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 2, 'This is an electronically generated document. No signature is required.', 0, 1, 'C');
$pdf->Cell(0, 2, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

// ─────────────────────────────────────────────────────────────────────────────
// Output
// ─────────────────────────────────────────────────────────────────────────────
$filename = 'Payslip_' . str_replace(' ', '_', $payslip['employee_name']) . '_' . $year . '.pdf';
ob_end_clean();
$pdf->Output($filename, 'I');
?>
