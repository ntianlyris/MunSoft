<?php
ob_start();
require_once('../includes/tcpdf/tcpdf.php');
require_once('../includes/view/functions.php');
require_once('../includes/class/DB_conn.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Signatory.php');

$db = new DB_conn();
$Payroll = new Payroll();
$Signatory = new Signatory();

$period_id = $_GET['period_id'] ?? '';
$dept_id_filter = $_GET['dept_id'] ?? 'all';
$emp_type_filter = $_GET['employment_type'] ?? 'all';

if (empty($period_id)) {
    die("Error: Payroll period is required.");
}

// Fetch Period info
$periodInfo = $Payroll->GetPayrollPeriodByID($period_id);
if (!$periodInfo) {
    die("Error: Payroll period not found.");
}

// Fetch SLP Data
$query = "SELECT pe.*, e.employee_id_num, 
            CONCAT(e.lastname, ', ', e.firstname, ' ', IF(LENGTH(e.middlename) > 0, CONCAT(LEFT(e.middlename, 1), '. '), ''), ' ', e.extension) AS full_name,
            pos.position_title, dept.dept_title
          FROM payroll_entries pe
          INNER JOIN employees_tbl e ON pe.employee_id = e.employee_id
          INNER JOIN employee_employments_tbl ee ON pe.employment_id = ee.employment_id
          INNER JOIN positions_tbl pos ON ee.position_id = pos.position_id
          INNER JOIN departments_tbl dept ON pe.dept_id = dept.dept_id
          WHERE pe.payroll_period_id = '$period_id'";

if ($dept_id_filter !== 'all') {
    $query .= " AND pe.dept_id = '" . $db->escape_string($dept_id_filter) . "'";
}
if ($emp_type_filter !== 'all') {
    $query .= " AND pe.emp_type_stamp = '" . $db->escape_string($emp_type_filter) . "'";
}
if ($dept_id_filter !== 'all') {
    $query .= " ORDER BY dept.dept_title ASC, e.lastname ASC, e.firstname ASC";
} else {
    $query .= " ORDER BY e.lastname ASC, e.firstname ASC";
}

$result = $db->query($query);
$data_by_dept = [];
$grand_total = ['gross' => 0, 'deductions' => 0, 'net' => 0];

$dept_label = "All Departments";
if ($dept_id_filter !== 'all') {
    $deptRes = $db->query("SELECT dept_title FROM departments_tbl WHERE dept_id = '" . $db->escape_string($dept_id_filter) . "'");
    if ($deptRow = $deptRes->fetch_assoc()) {
        $dept_label = $deptRow['dept_title'];
    }
}

while ($row = $result->fetch_assoc()) {
    $dept_name = $row['dept_title'];
    $data_list[] = $row;
    if (!isset($data_by_dept[$dept_name])) { $data_by_dept[$dept_name] = []; }
    $data_by_dept[$dept_name][] = $row;

    $grand_total['gross'] += $row['gross'];
    $grand_total['deductions'] += $row['total_deductions'];
    $grand_total['net'] += $row['net_pay'];
}

// Setup PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('MunSoft Payroll System');
$pdf->SetTitle('Summary List of Payroll');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage();

// Header
$pdf->Image('../includes/images/polanco_logo.jpg', 30, 15, 20, 20);
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 7, 'Republic of the Philippines', 0, 1, 'C');
$pdf->Cell(0, 7, 'Province of Zamboanga del Norte', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 7, 'Municipality of Polanco', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'SUMMARY LIST OF PAYROLL', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'For the Period: ' . $periodInfo['period_label'], 0, 1, 'C');
$pdf->Cell(0, 6, 'Department: ' . $dept_label, 0, 1, 'C');
$pdf->Ln(5);

// Table Header
$html = '<table border="1" cellpadding="4" cellspacing="0" width="100%">
            <thead>
                <tr style="background-color:#f2f2f2; font-weight:bold;">
                    <th width="5%" align="center">#</th>
                    <th width="25%" align="center">Employee Name</th>
                    <th width="30%" align="center">Position</th>
                    <th width="13%" align="center">Gross</th>
                    <th width="13%" align="center">Deductions</th>
                    <th width="14%" align="center">Net Pay</th>
                </tr>
            </thead>
            <tbody>';

if ($dept_id_filter !== 'all') {
    foreach ($data_by_dept as $dept => $employees) {
        $html .= '<tr style="background-color:#f9f9f9;"><td colspan="6"><b>' . $dept . '</b></td></tr>';
        $count = 1;
        $dept_total = ['gross' => 0, 'deductions' => 0, 'net' => 0];
        
        foreach ($employees as $emp) {
            $html .= '<tr>
                        <td width="5%" align="center">' . $count++ . '</td>
                        <td width="25%">' . htmlspecialchars($emp['full_name']) . '</td>
                        <td width="30%">' . htmlspecialchars($emp['position_title']) . '</td>
                        <td width="13%" align="right">' . number_format($emp['gross'], 2) . '</td>
                        <td width="13%" align="right">' . number_format($emp['total_deductions'], 2) . '</td>
                        <td width="14%" align="right">' . number_format($emp['net_pay'], 2) . '</td>
                      </tr>';
            $dept_total['gross'] += $emp['gross'];
            $dept_total['deductions'] += $emp['total_deductions'];
            $dept_total['net'] += $emp['net_pay'];
        }
        
        $html .= '<tr style="font-style:italic; background-color:#fcfcfc;">
                    <td width="60%" colspan="3" align="right">Department Total:</td>
                    <td width="13%" align="right">' . number_format($dept_total['gross'], 2) . '</td>
                    <td width="13%" align="right">' . number_format($dept_total['deductions'], 2) . '</td>
                    <td width="14%" align="right">' . number_format($dept_total['net'], 2) . '</td>
                  </tr>';
    }
} else {
    $count = 1;
    foreach ($data_list as $emp) {
        $html .= '<tr>
                    <td width="5%" align="center">' . $count++ . '</td>
                    <td width="25%">' . htmlspecialchars($emp['full_name']) . '</td>
                    <td width="30%">' . htmlspecialchars($emp['position_title']) . '</td>
                    <td width="13%" align="right">' . number_format($emp['gross'], 2) . '</td>
                    <td width="13%" align="right">' . number_format($emp['total_deductions'], 2) . '</td>
                    <td width="14%" align="right">' . number_format($emp['net_pay'], 2) . '</td>
                  </tr>';
    }
}

// Grand Total
$html .= '<tr style="background-color:#e9e9e9; font-weight:bold;">
            <td width="60%" colspan="3" align="right">GRAND TOTAL:</td>
            <td width="13%" align="right">' . number_format($grand_total['gross'], 2) . '</td>
            <td width="13%" align="right">' . number_format($grand_total['deductions'], 2) . '</td>
            <td width="14%" align="right">' . number_format($grand_total['net'], 2) . '</td>
          </tr>';

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Signatories (Accounting Reports -> report_code = 'ACCTG')
$pdf->Ln(10);
$signatories = $Signatory->FetchActiveSignatoriesByReportType('ACCTG');

if (!empty($signatories)) {
    $sig_html = '<table border="0" cellpadding="4" width="100%"><tr>';
    $col_width = floor(100 / count($signatories)) . '%';

    foreach ($signatories as $sig) {
        $sig_html .= '<td width="' . $col_width . '">
                        <br>' . $sig['sign_particulars'] . '<br><br><br>
                        <b>' . $sig['full_name'] . '</b><br>
                        <span>' . $sig['position_title'] . '</span>
                      </td>';
    }
    $sig_html .= '</tr></table>';
    $pdf->writeHTML($sig_html, true, false, true, false, '');
}

ob_end_clean();
$pdf->Output('Summary_List_of_Payroll.pdf', 'I');
?>