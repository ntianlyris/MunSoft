<?php
ob_start();
require_once('..//includes/tcpdf/tcpdf.php');
require_once('..//includes/view/functions.php');
require_once('..//includes/class/Payroll.php');
require_once('..//includes/class/Department.php');
require_once('..//includes/class/Employee.php');
require_once('..//includes/class/Employment.php');
require_once('..//includes/class/JournalEntry.php');
require_once('..//includes/class/Signatory.php');

$Payroll = new Payroll();
$Department = new Department();
$Employee = new Employee();
$Employment = new Employment();
$JournalEntry = new JournalEntry();
$Payroll_Signatory = new Signatory();

// GET parameters
$year = $_GET['year'] ?? '';
$period_id = $_GET['period_id'] ?? '';
$dept_id = $_GET['dept_id'] ?? '';
$employment_type = $_GET['employment_type'] ?? '';

if (!$year || !$period_id || !$dept_id || !$employment_type) {
    die("Missing required parameters.");
}

if (strpos($period_id, '_') !== false) {
    // Period is a date range (e.g., "2025-08-01_2025-08-15")
    list($start_date, $end_date) = explode('_', $period);
    $payroll_period_id = $Payroll->GetPayrollPeriodByDates($start_date, $end_date)['payroll_period_id'];
} else {
    // Period is already a payroll_period_id
    $payroll_period_id = $period_id;
    $period_dates = $Payroll->GetPeriodDatesByID($payroll_period_id);
    if ($period_dates) {
        $start_date = $period_dates['date_start'];
        $end_date = $period_dates['date_end'];
    } else {
        die("Invalid payroll period ID.");
    }
}

// Generate journal entries
$journal_entries = array();

// Get department details for responsibility center
$dept_details = $Department->GetDepartmentDetails($dept_id);
if (!$dept_details) {
    throw new Exception('Department not found');
}

// Set Labels for department and period
$responsibility_ctr = $dept_details['dept_code'];
$pay_period_start = OutputDate($start_date);
$pay_period_end = OutputDate($end_date);
$date = new DateTime();
$date = OutputShortDate($date->format('Y-m-d'));

// Get payroll data for the period
$payroll_data = $Payroll->FetchPayrollByPayPeriodAndDept($period_id, $dept_id, $employment_type);
if (!$payroll_data || empty($payroll_data)) {
    throw new Exception('No payroll data found for the selected period');
}

// Set payroll data in JournalEntry class
$JournalEntry->setPayrollData($payroll_data);
$JournalEntry->setSalaryTypeAcctCodeByEmploymentType($employment_type);

// Build journal entries
$debit_entries = $JournalEntry->BuildDebitEntriesFromEarningsAndGovShares();
$credit_entries = $JournalEntry->BuildCreditEntriesFromDeductions();
$journal_entries = array_merge($debit_entries, $credit_entries);

// Setup PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Municipality of Polanco');
$pdf->SetTitle('Journal Entry Voucher');
$pdf->SetSubject('Payroll Journal Entry');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Adjustable spacing settings (tweak these numbers)
$margin_lr = 12;         // left/right margin (mm) — try 8..18
$margin_top = 12;        // top margin (mm) — try 8..20
$margin_bottom = 12;     // bottom margin (mm)
$cell_height = 5;        // default cell height (mm) to use in $pdf->Cell(..., $cell_height, ...)
$cell_padding_top = 0.5; // cell top padding (mm) — try 0..2
$cell_padding_bottom = 0.5;// cell bottom padding (mm)
$cell_padding_lr = 1;    // left/right padding (mm)
$cell_height_ratio = 0.95; // line height multiplier for MultiCell/writeHTML — try 0.85..1.05
$font_size_title = 14;
$font_size_sub = 11;
$font_size_body = 9;     // reduce main body font to shrink overall spacing

// Set line style (optional)
$style = array(
    'width' => 0.3,               // line width in mm
    'color' => array(0, 0, 0)     // black color (RGB)
);

$pdf->SetMargins($margin_lr, $margin_top, $margin_lr);
$pdf->SetAutoPageBreak(true, $margin_bottom);

// Reduce internal paddings
$pdf->setCellPaddings($cell_padding_lr, $cell_padding_top, $cell_padding_lr, $cell_padding_bottom);
$pdf->setCellHeightRatio($cell_height_ratio);

// Fonts (smaller body font helps reduce vertical spacing)
$pdf->SetFont('helvetica', 'B', $font_size_title);

$pdf->AddPage(); // A4 Portrait by default

// Header: center title across usable width and place "No. ______" aligned to right margin on same row
$pageWidth = $pdf->getPageWidth();
list($lMargin, $tMargin, $rMargin) = $pdf->getMargins();
$usableWidth = $pageWidth - $lMargin - $rMargin;

// width reserved for the "No. ______" block (mm) — tweak this to change underline length / spacing
$rightW = 60; // try 50..80 if you want a longer/shorter right block

//lines
// Draw a line from (20mm, 50mm) to (190mm, 50mm)
// Line (x, y, x2, y2, style) horizontal line across page
// Line (x, y, x, y2, style) vertical line down page
$pdf->Line(10, 10, 200, 10, $style);
$pdf->Line(10, 26, 200, 26, $style);
$pdf->Line(10, 35, 200, 35, $style);
$pdf->Line(32, 40, 200, 40, $style);
$pdf->Line(32, 50, 200, 50, $style);

// prepare underline string (adjust number for length if needed)
$no_underline = 'No.: ' . str_repeat('_', 15);

// get current Y position for single-row placement
$y = $pdf->GetY();

// Write centered title across the full usable width without advancing the cursor
$pdf->SetFont('helvetica', 'B', $font_size_title);
$pdf->SetXY(0, $y);
$pdf->Cell($leftW, 8, 'JOURNAL ENTRY VOUCHER', 0, 0, 'C');

// Place the "No. ______" at the right side on the same row
$xNo = $pageWidth - $rMargin - $rightW;
$pdf->SetXY(155, $y);
$pdf->SetFont('helvetica', '', $font_size_sub);
$pdf->Cell($rightW, 8, $no_underline, 0, 1, 'L');

// small gap before subtitle
$pdf->Ln(2);
$pdf->SetFont('helvetica', '', $font_size_sub);
$pdf->SetXY(0, 20);
$pdf->Cell(0, 5, 'Municipality of Polanco, Z.N.', 0, 1, 'C');

$pdf->SetXY(155, 20);
$pdf->SetFont('helvetica', '', $font_size_sub);
$pdf->Cell($rightW, 8, 'Date: ' . $date, 0, 1, 'L');

$pdf->SetXY(15, 26);
$pdf->SetFont('helvetica', '', $font_size_sub);
$pdf->Cell(0, 8, '______ Collection  ______ Check Disbursement  ______ Cash Disbursement  ___x___ Others', 0, 1, 'L');

$pdf->SetXY(10, 35);
$pdf->SetFont('helvetica', '', $font_size_body);
// WITH wrapping (multi-line)
$pdf->MultiCell(22, 15, "\nResponsibility Center", 1, 'L', 0, 1);
$pdf->SetFont('helvetica', '', $font_size_sub);
$pdf->SetXY(10, 35);
$pdf->Cell(0, 5, "ACCOUNTING ENTRIES", 0, 1, 'C');
$pdf->SetXY(40, 42);
$pdf->Cell(0, 5, "Accounts and Explanation", 0, 1, 'L');
$pdf->SetXY(103, 40);
$pdf->MultiCell(30, 10, "Account \nCode", 1, 'C', 0, 1);
$pdf->SetXY(80, 42);
$pdf->Cell(0, 5, "PR", 0, 1, 'C');
$pdf->SetXY(144, 40);
$pdf->Cell(56, 5, "Amount", 1, 1, 'C');
$pdf->SetXY(144, 45);
$pdf->Cell(28, 5, "Debit", 1, 1, 'C');
$pdf->SetXY(172, 45);
$pdf->Cell(28, 5, "Credit", 1, 1, 'C');
$pdf->SetFont('helvetica', '', $font_size_body);
$pdf->SetXY(10, 50);
$pdf->Cell(28, 5, ' '. $responsibility_ctr, 0, 1, 'L');


// Start HTML table
$html = '
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
                text-align: center;
            }
            th, td {
                border-left: 1px solid #000;
                border-right: 1px solid #000;
                border-top: none;
                border-bottom: none;
                padding: 6px;
            }
        </style>

        <table>
            <tbody>';

        // Loop your array to create table rows
        $total_debit = 0;
        $total_credit = 0;
        foreach ($journal_entries as $row) {
            $total_debit += $row['debit_amt'];
            $total_credit += $row['credit_amt'];
            $html .= '<tr>
                        <td style="width:63px; height:12;"></td>
                        <td style="width:200px; height:12;">' . htmlspecialchars($row['description']) . '</td>
                        <td style="width:86px; height:12; text-indent:15px;">' . htmlspecialchars($row['acct_code']) . '</td>
                        <td style="width:30px; height:12;"></td>
                        <td style="width:80px; height:12;" align="right">' . ($row['debit_amt'] == 0 ? ' ' : number_format($row['debit_amt'], 2)) . '</td>
                        <td style="width:80px; height:12;" align="right">' . ($row['credit_amt'] == 0 ? ' ' : number_format($row['credit_amt'], 2)) . '</td>
                    </tr>';
        }
$html .= '<tr>
            <td colspan="3" align="center" style="font-weight:bold; height:12; border:1px solid #000;">TOTAL</td>
            <td style="width:30px; height:12; border:1px solid #000;"></td>
            <td align="right" style="font-weight:bold; height:12; border:1px solid #000;">' . number_format($total_debit, 2) . '</td>
            <td align="right" style="font-weight:bold; height:12; border:1px solid #000;">' . number_format($total_credit, 2) . '</td>
        </tr>
    </tbody>
</table>';

// Write the Data HTML to the PDF
$pdf->SetXY(10, 50);
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Line(10, $pdf->GetY()-3, 200, $pdf->GetY()-3, $style);

// Footer for signatories
$pdf->Ln(1); // Space before footer
$pdf->SetFont('helvetica', '', $font_size_body);

// Get active signatories for payroll
$signatories = $Payroll_Signatory->FetchActiveSignatoriesByReportType('JOURNAL');
$filtered_signatories = array();

// Filter signatories based on role and department
foreach ($signatories as $sign) {
    // Include non-department head signatories
    if ($sign['role_type'] !== 'HEAD') {
        $filtered_signatories[] = $sign;
        continue;
    }
    
    // For department heads, only include if department matches
    if ($sign['role_type'] === 'HEAD' && $sign['dept_id'] == $department) {
        $filtered_signatories[] = $sign;
    }
}

// Calculate rows needed (# signatories per row)
$signatories_per_row = 2;
$total_signatories = count($filtered_signatories);
$total_rows = ceil($total_signatories / $signatories_per_row);

// Build signatory table with multiple rows
$signatory_table = '<table border="0" cellpadding="4" cellspacing="0" width="100%">';

for ($row = 0; $row < $total_rows; $row++) {
    $signatory_table .= '<tr>';
    
    // Calculate start and end index for current row
    $start_index = $row * $signatories_per_row;
    $end_index = min(($row + 1) * $signatories_per_row, $total_signatories);
    
    // Add cells for current row
    for ($i = $start_index; $i < $end_index; $i++) {
        $sign = $filtered_signatories[$i];
        $signatory_table .= '
            <td width="50%" align="left">
                ' . $sign['sign_particulars'] . '<br>
                <br><br><br>
                <b>' . $sign['full_name'] . '</b><br>
                ' . $sign['position_title'] . '
            </td>';
    }
    
    // Fill remaining cells in the last row if needed
    $remaining_cells = $signatories_per_row - ($end_index - $start_index);
    for ($i = 0; $i < $remaining_cells; $i++) {
        $signatory_table .= '<td width="25%"></td>';
    }
    
    $signatory_table .= '</tr>';
}

$signatory_table .= '</table>';

// Add some spacing between rows
$signatory_table = str_replace('</tr>', '</tr><tr><td colspan="4" height="10"></td></tr>', $signatory_table);

$pdf->writeHTML($signatory_table, true, false, true, false, '');
$pdf->Line(10, $pdf->GetY()-10, 200, $pdf->GetY()-10, $style);

// Get the last Y position
$lastY = $pdf->GetY();
$pdf->Line(10, 10, 10, $lastY-10); // vertical lines that dynamically adjust based on content leftmost border
$pdf->Line(200, 10, 200, $lastY-10); // vertical lines that dynamically adjust based on content rightmost border

// If you render HTML table via writeHTML(), add a compact CSS block to reduce cell padding and line-height:
$compact_css = '
<style>
    table { border-collapse: collapse; font-size: ' . $font_size_body . 'pt; }
    table td, table th { padding: 2px 4px; }         /* tweak padding (px) */
    table tr { line-height: 1.05; }                 /* tweak line-height */
</style>
';

// Later when building the HTML table:
// $html = $compact_css . $html_table_body;
// $pdf->writeHTML($html, true, false, true, false, '');

// If you use $pdf->Cell() for each row, use $cell_height variable for the height argument:
// $pdf->Cell($w_col1, $cell_height, $text, 'LR', 0, 'L', 0, '', 0, false, 'T', 'M');

// ...existing code...
ob_end_clean();
$pdf->Output('journal_entry_voucher.pdf', 'I');
?>