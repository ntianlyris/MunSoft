<?php
ob_start();
require_once('..//includes/tcpdf/tcpdf.php');
require_once('..//includes/view/functions.php');
require_once('..//includes/class/Employee.php');
require_once('..//includes/class/Department.php');
require_once('..//includes/class/Employment.php');
require_once('..//includes/class/Signatory.php');
require_once('..//includes/class/Remittance.php');

$Employee = new Employee();
$Department = new Department();
$Employment = new Employment();
$Signatory = new Signatory();
$Remittance = new Remittance();

// Set report code for signatories
$report_code = isset($_GET['report_code']) ? $_GET['report_code'] : 'ACCTG';

// Setup PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('MunSoft Payroll System');
$pdf->SetAuthor('Municipality of Polanco');
$pdf->SetTitle('Remittance Details');
$pdf->SetSubject('Remittance Report');
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

// get current Y position for single-row placement
$y = $pdf->GetY();

$pdf->SetFont('helvetica', '', $font_size_sub);
$pdf->SetXY(20, $y);
$pdf->Cell(0, 5, 'Municipality of Polanco, Z.N.', 0, 1, 'C');

// Write centered title across the full usable width without advancing the cursor
$pdf->SetFont('helvetica', 'B', $font_size_title);
$pdf->SetXY(20, $y+5);
$pdf->Cell(0, 8, 'REMITTANCE DETAILS', 0, 0, 'C');

$pdf->Ln(15);
$pdf->SetFont('helvetica', '', $font_size_body);

$print_type = isset($_POST['print_type']) ? $_POST['print_type'] : '';    //loans or loans_breakdown
$remittance_id = isset($_POST['remittance_id']) ? $_POST['remittance_id'] : '';
$remit_data = $Remittance->GetRemittanceByID($remittance_id);
$remit_details = $Remittance->getRemittanceDetails($remittance_id);
$remit_type = $remit_data['remittance_type'] ?? '';
// Remittance title can come from remittance record or from first detail row; guard both
$remit_title = $remit_data['remittance_title'] ?? ($remit_details[0]['remittance_title'] ?? '');
// Guard period fields to avoid undefined index notices
$period_start = OutputShortDate($remit_data['period_start'] ?? '');
$period_end = OutputShortDate($remit_data['period_end'] ?? '');
$period_text = "For the period: $period_start to $period_end";


// Apply minimal inline styling for better layout in TCPDF
$html_content = '
    <style>
            table {
                width: 100%;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
                text-align: center;
            }
            th, td {
                padding: 6px;
            }
        </style>';

if($print_type === 'loans') {
    $remit_title = 'Loans';
    $loan_details = $Remittance->getLoansRemittanceDetails($remittance_id);
    
    if (!empty($loan_details) && is_array($loan_details)) {
        $loan_grandtotals = 0;
        $count = 0;

        $html_content = '
            <div style="width:80%; margin: 0 auto;">
                <h3 style="text-align:center; margin-bottom:10px;">' . $remit_title . '</h3>
                <p style="text-align:center;">' . $period_text . '</p>
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background-color:#f2f2f2; font-weight:bold;">
                            <th width="5%" style="text-align:center;">#</th>
                            <th width="60%" style="text-align:center;">Loan Type</th>
                            <th width="35%" style="text-align:center;">Total Loan Amount</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($loan_details as $row) {
            $loan_grandtotals += $row['total_amount'];
            $count++;

            $html_content .= '
                <tr>
                    <td width="5%" style="text-align:center;">' . $count . '</td>
                    <td width="60%">' . htmlspecialchars($row['loan_title']) . '</td>
                    <td width="35%" style="text-align:right;">' . ($row["total_amount"] != 0 ? OutputMoney($row["total_amount"]) : "&nbsp;") . '</td>
                </tr>';
        }

        // Totals row
        $html_content .= '
            <tr style="font-weight:bold;">
                <td colspan="2" style="text-align:right;">GRAND TOTAL:</td>
                <td style="text-align:right;">' . OutputMoney($loan_grandtotals) . '</td>
            </tr>';

        $html_content .= '
                    </tbody>
                </table>
            </div>';
    } else {
        $html_content = "
            <table border='1' cellpadding='4' cellspacing='0'>
                <tr><td colspan='3' class='text-center text-muted'>No remittance details found</td></tr>
            </table>
        ";
    }  
} 

elseif($print_type === 'others'){
    $remit_title = "Other Remittances";
    $other_remit_details = $Remittance->getOthersRemittanceDetails($remittance_id);

    if (!empty($other_remit_details) && is_array($other_remit_details)) {
        $total_amounts = 0;
        $count = 0;

        $html_content = '
            <div style="width:80%; margin: 0 auto;">
                <h3 style="text-align:center; margin-bottom:10px;">' . $remit_title . '</h3>
                <p style="text-align:center;">' . $period_text . '</p>
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background-color:#f2f2f2; font-weight:bold;">
                            <th width="5%" style="text-align:center;">#</th>
                            <th width="65%" style="text-align:center;">Particulars</th>
                            <th width="30%" style="text-align:center;">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($other_remit_details as $row) {
            $total_amounts += $row['total_amount'];
            $count++;

            $html_content .= '
                <tr>
                    <td width="5%" style="text-align:center;">' . $count . '</td>
                    <td width="65%">' . htmlspecialchars($row['deduct_title']) . '</td>
                    <td width="30%" style="text-align:right;">' . ($row["total_amount"] != 0 ? OutputMoney($row["total_amount"]) : "&nbsp;") . '</td>
                </tr>';
        }
        // Totals row
        $html_content .= '
            <tr style="font-weight:bold;">
                <td colspan="2" style="text-align:right;">TOTAL:</td>
                <td style="text-align:right;">' . OutputMoney($total_amounts) . '</td>
            </tr>';
        $html_content .= '
                    </tbody>
                </table>
            </div>';
    } else {
        $html_content = "
            <table border='1' cellpadding='4' cellspacing='0'>
                <tr><td colspan='3' class='text-center text-muted'>No remittance details found</td></tr>
            </table>
        ";
    }
}

// for other remittance types e.g. PhilAm Ins,PMGEA Dues, etc.
elseif($print_type === 'others_breakdown'){
    // Detailed breakdown of other remittances per employee
    $breakdown_param = isset($_POST['breakdown']) ? $_POST['breakdown'] : '';
    $breakdown_period = isset($_POST['period']) ? $_POST['period'] : '';
    list($start_date, $end_date) = explode('_', $breakdown_period);
    
    $remit_title = isset($_POST['deduct_title']) ? $_POST['deduct_title'] : 'Other Remittance Breakdown';
    $period_text = "For the period: " . OutputShortDate($start_date) . " to " . OutputShortDate($end_date);

    $breakdown_details = json_decode($breakdown_param, true);
    if (!empty($breakdown_details) && is_array($breakdown_details)) {
        $total_amounts = 0;
        $count = 0;

        $html_content = '
            <div style="width:90%; margin: 0 auto;">
                <h3 style="text-align:center; margin-bottom:10px;">' . $remit_title . '</h3>
                <p style="text-align:center;">' . $period_text . '</p>
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background-color:#f2f2f2; font-weight:bold;">
                            <th width="5%" style="text-align:center;">#</th>
                            <th width="35%" style="text-align:center;">Employee Name</th>
                            <th width="40%" style="text-align:center;">Position</th>
                            <th width="20%" style="text-align:center;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($breakdown_details as $row) {
            $total_amounts += $row['amount'];
            $count++;

            $html_content .= '
                <tr>
                    <td width="5%" style="text-align:center;">' . $count . '</td>
                    <td width="35%">' . htmlspecialchars($row['employee_name']) . '</td>
                    <td width="40%">' . htmlspecialchars(trim(explode('(', $row['position_title'])[0])) . '</td>
                    <td width="20%" style="text-align:right;">' . ($row["amount"] != 0 ? OutputMoney($row["amount"]) : "&nbsp;") . '</td>
                </tr>';
        }
        // Totals row
        $html_content .= '
            <tr style="font-weight:bold;">
                <td colspan="3" style="text-align:right;">TOTAL:</td>
                <td style="text-align:right;">' . OutputMoney($total_amounts) . '</td>
            </tr>';
        $html_content .= '
                    </tbody>
                </table>
            </div>';
    } else {
        $html_content = "
            <table border='1' cellpadding='4' cellspacing='0'>
                <tr><td colspan='3' class='text-center text-muted'>No remittance details found</td></tr>
            </table>
        ";
    }
}        

elseif($print_type === 'loans_breakdown'){
    // Detailed breakdown of loans per employee
    $breakdown_param = isset($_POST['breakdown']) ? $_POST['breakdown'] : '';
    $breakdown_period = isset($_POST['period']) ? $_POST['period'] : '';
    list($start_date, $end_date) = explode('_', $breakdown_period);
    
    $remit_title = isset($_POST['loan_title']) ? $_POST['loan_title'] : 'Loans Breakdown';
    $period_text = "For the period: " . OutputShortDate($start_date) . " to " . OutputShortDate($end_date);

    $breakdown_details = json_decode($breakdown_param, true);
    if (!empty($breakdown_details) && is_array($breakdown_details)) {
        $loan_totals = 0;
        $count = 0;

        $html_content = '
            <div style="width:90%; margin: 0 auto;">
                <h3 style="text-align:center; margin-bottom:10px;">' . $remit_title . '</h3>
                <p style="text-align:center;">' . $period_text . '</p>
                <table border="1" cellpadding="4" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background-color:#f2f2f2; font-weight:bold;">
                            <th width="5%" style="text-align:center;">#</th>
                            <th width="35%" style="text-align:center;">Employee Name</th>
                            <th width="40%" style="text-align:center;">Position</th>
                            <th width="20%" style="text-align:center;">Loan Amount</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($breakdown_details as $row) {
            $loan_totals += $row['amount'];
            $count++;

            $html_content .= '
                <tr>
                    <td width="5%" style="text-align:center;">' . $count . '</td>
                    <td width="35%">' . htmlspecialchars($row['employee_name']) . '</td>
                    <td width="40%">' . htmlspecialchars(trim(explode('(', $row['position_title'])[0])) . '</td>
                    <td width="20%" style="text-align:right;">' . ($row["amount"] != 0 ? OutputMoney($row["amount"]) : "&nbsp;") . '</td>
                </tr>';
        }

        // Totals row
        $html_content .= '
            <tr style="font-weight:bold;">
                <td colspan="3" style="text-align:right;">TOTAL:</td>
                <td style="text-align:right;">' . OutputMoney($loan_totals) . '</td>
            </tr>';

        $html_content .= '
                    </tbody>
                </table>
            </div>';
    } else {
        $html_content = "
            <table border='1' cellpadding='4' cellspacing='0'>
                <tr><td colspan='3' class='text-center text-muted'>No remittance details found</td></tr>
            </table>
        ";
    }
}

else{
    if (!empty($remit_details) && is_array($remit_details)) {
        $employee_totals = 0;
        $employer_totals = 0;
        $count = 0;
        // Add a simple title or header above the table (optional)
        $html_content = '
        <h3 style="text-align:center; margin-bottom:10px;">' . $remit_title . '</h3>
        <p style="text-align:center;">' . $period_text . '</p>
        
        <table border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr style="background-color:#f2f2f2; font-weight:bold;">
                    <th width="4%" style="text-align:center;">#</th>
                    <th width="28%" style="text-align:center;">Employee Name</th>
                    <th width="32%" style="text-align:center;">Particulars</th>
                    <th width="18%" style="text-align:center;">Employee Share</th>
                    <th width="18%" style="text-align:center;">Employer Share</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($remit_details as $row) {
            $employee_totals += $row['employee_share'];
            $employer_totals += $row['employer_share'];
            $count++;

            $html_content .= '
                <tr>
                    <td width="4%" style="text-align:left;">' . $count . '</td>
                    <td width="28%" style="text-align:left;">' . $row["employee_name"] . '</td>
                    <td width="32%" style="text-align:left;">' . $row["remittance_title"] . '</td>
                    <td width="18%" style="text-align:right;">' . ($row["employee_share"] != 0 ? OutputMoney($row["employee_share"]) : "&nbsp;") . '</td>
                    <td width="18%" style="text-align:right;">' . ($row["employer_share"] != 0 ? OutputMoney($row["employer_share"]) : "&nbsp;") . '</td>
                </tr>
            ';
        }

        // Totals row
        $html_content .= '
            <tr style="font-weight:bold;">
                <td colspan="3" style="text-align:right;">TOTAL:</td>
                <td style="text-align:right;">' . OutputMoney($employee_totals) . '</td>
                <td style="text-align:right;">' . OutputMoney($employer_totals) . '</td>
            </tr>
        ';

        $html_content .= '
                </tbody>
            </table>
        ';
    } else {
        $html_content .= "
            <table class='table table-bordered table-sm'>
                <tr><td colspan='4' class='text-center text-muted'>No remittance details found</td></tr>
            </table>
        ";
    }  
}




// --- Render HTML into the PDF ---
$pdf->SetXY(0, $y+15);
$pdf->writeHTML($html_content, true, false, true, false, '');

// Footer for signatories
$pdf->Ln(10); // Space before footer
$pdf->SetFont('helvetica', '', $font_size_body);


// Get active signatories for payroll
$dept_id = $_GET['dept_id'] ?? '';
$signatories = $Signatory->FetchActiveSignatoriesByReportType($report_code);
$filtered_signatories = array();

// Filter signatories based on role and department
foreach ($signatories as $sign) {
    // Include non-department head signatories
    if ($sign['role_type'] !== 'HEAD') {
        $filtered_signatories[] = $sign;
        continue;
    }
    
    // For department heads, only include if department matches
    if ($sign['role_type'] === 'HEAD' && $sign['dept_id'] == $dept_id) {
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
                <span>' . $sign['position_title'] . '</span>
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



// Get the last Y position
$lastY = $pdf->GetY();
//$pdf->Line(10, 10, 10, $lastY-10); // vertical lines that dynamically adjust based on content leftmost border
//$pdf->Line(200, 10, 200, $lastY-10); // vertical lines that dynamically adjust based on content rightmost border
//$pdf->Line(10, $pdf->GetY()-10, 200, $pdf->GetY()-10, $style);  // horizontal line above footer

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
$pdf->Output('remit_details.pdf', 'I');
?>