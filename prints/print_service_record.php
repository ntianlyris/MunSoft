<?php
ob_start();
require_once('..//includes/tcpdf/tcpdf.php');
require_once('..//includes/class/Department.php');
require_once('..//includes/view/functions.php');

$MyDepartment = new Department();

$employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;

// Create new PDF document with custom paper size (11.5 x 13 inches)
$pdf = new TCPDF('L', 'mm', array(292.1, 330.2), true, 'UTF-8', false);

// Margins: Left = 12.7mm, Top = 25mm (about 1 inch), Right = 12.7mm
$pdf->SetMargins(12.7, 25, 12.7);

// Bottom margin (also 0.5 inch) via auto page break
$pdf->SetAutoPageBreak(TRUE, 12.7);

// Set document info
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LGU Polanco - HR Section');
$pdf->SetTitle('Print Service Record');
$pdf->SetSubject('Service Record');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default font
$pdf->SetFont('helvetica', '', 9); // or use 6 for very tight tables

// Add a page
$pdf->AddPage();

// Title
$pdf->Cell(0, 10, 'Service Record for Employee ID: ' . $employee_id, 0, 1, 'C');

// Build table HTML
$html = '
<table border="1" cellpadding="4">
  <thead>
    <tr style="background-color:#d9edf7;font-weight:bold;">
      <th>#</th>
      <th>Ref #</th>
      <th>Position</th>
      <th>Employment Type</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Department</th>
      <th>Assigned</th>
      <th>Designation</th>
      <th>Work Nature</th>
      <th>Work Specifics</th>
      <th>Rate</th>
    </tr>
  </thead>
  <tbody>';

// Get employment data
$conn = new mysqli('localhost', 'root', '', 'munsoft_polanco'); // Update as needed
$sql = "SELECT * FROM employee_employments_tbl a 
        INNER JOIN positions_tbl b 
        ON a.position_id = b.position_id 
        INNER JOIN departments_tbl c
        ON b.dept_id = c.dept_id
        WHERE employee_id = $employee_id ORDER BY employment_start DESC";
$result = $conn->query($sql);

$department_assigned = "";
$i = 1;
$rows = [];
while ($row = $result->fetch_assoc()) {
  $start_date = new DateTime($row['employment_start']);
  $end_date = ($row['employment_end'] == '0000-00-00' || empty($row['employment_end']))
    ? new DateTime()
    : new DateTime($row['employment_end']);

  $department_assigned = $MyDepartment->GetDepartmentDetails($row['dept_assigned'])['dept_title'];

  $period_start = clone $start_date;
  $period_end = clone $end_date;

  for ($year = (int)$period_start->format('Y'); $year <= (int)$period_end->format('Y'); $year++) {
    $row_start = ($year == (int)$start_date->format('Y')) ? $start_date : new DateTime("$year-01-01");
    $row_end = ($year == (int)$end_date->format('Y')) ? $end_date : new DateTime("$year-12-31");

    $display_end = ($row_end == $end_date && $row['employment_end'] == '0000-00-00') 
      ? 'Present' 
      : OutputShortDate($row_end->format('Y-m-d'));

    $rows[] = [
      'employment_refnum' => $row['employment_refnum'],
      'position_title' => $row['position_title'],
      'employment_type' => $row['employment_type'],
      'row_start' => OutputShortDate($row_start->format('Y-m-d')),
      'display_end' => $display_end,
      'dept_title' => $row['dept_title'],
      'department_assigned' => $department_assigned,
      'designation' => $row['designation'],
      'work_nature' => $row['work_nature'],
      'work_specifics' => $row['work_specifics'],
      'rate' => $row['rate'],
      'sort_date' => $row_start->format('Y-m-d'), // for sorting
    ];
  }
}

// Sort rows by 'sort_date' descending
usort($rows, function($a, $b) {
  return strcmp($b['sort_date'], $a['sort_date']);
});

$i = 1;
foreach ($rows as $row) {
  $html .= '<tr>
    <td>' . $i++ . '</td>
    <td>' . htmlspecialchars($row['employment_refnum']) . '</td>
    <td>' . htmlspecialchars($row['position_title']) . '</td>
    <td>' . htmlspecialchars($row['employment_type']) . '</td>
    <td>' . htmlspecialchars($row['row_start']) . '</td>
    <td>' . htmlspecialchars($row['display_end']) . '</td>
    <td>' . htmlspecialchars($row['dept_title']) . '</td>
    <td>' . htmlspecialchars($row['department_assigned']) . '</td>
    <td>' . htmlspecialchars($row['designation']) . '</td>
    <td>' . htmlspecialchars($row['work_nature']) . '</td>
    <td>' . htmlspecialchars($row['work_specifics']) . '</td>
    <td>' . number_format($row['rate'], 2) . '</td>
  </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean();
// Output
$pdf->Output('service_record_' . $employee_id . '.pdf', 'I');
