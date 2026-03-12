<?php
ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');
require_once('../includes/tcpdf/tcpdf.php');
require_once('../includes/class/Department.php');
require_once('../includes/view/functions.php');

$MyDepartment = new Department();

$employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;

// Get employee and employment details using classes
require_once('../includes/class/Employee.php');
require_once('../includes/class/Employment.php');
$MyEmployee = new Employee();
$MyEmployment = new Employment();

$employee = $MyEmployee->GetEmployeeDetails($employee_id);
$employments = $MyEmployment->GetEmployeeEmployments($employee_id);

// Create new PDF document - Standard letter size portrait
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Reduced margins (15mm left/right, 12mm top/bottom)
$pdf->SetMargins(15, 12, 15);
$pdf->SetAutoPageBreak(TRUE, 10);

// Set document info
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LGU Polanco - Human Resource Section');
$pdf->SetTitle('Certification of Service Record');
$pdf->SetSubject('Service Record');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Logo Section - Add official Polanco logo centered at the top
$logo_path = '../includes/images/polanco_logo.jpg';
if (file_exists($logo_path)) {
    // Add logo image - 25mm width, centered
    $pdf->Image($logo_path, 82, 12, 25, 0, 'JPG', '', 'T', true, 150, 'C');
    $pdf->Ln(22);
} 

// Header Section
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'MUNICIPALITY OF POLANCO', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Province of Zamboanga del Norte', 0, 1, 'C');
$pdf->Cell(0, 5, 'Human Resource Section', 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', 11);
$pdf->Ln(2);
$pdf->Cell(0, 7, 'CERTIFICATION OF SERVICE RECORD', 0, 1, 'C');

// Divider line
$pdf->SetLineWidth(0.5);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(4);

// Employee Information Section
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(50, 5, 'EMPLOYEE INFORMATION', 0, 1);

$pdf->SetFont('helvetica', '', 9);
$y_pos = $pdf->GetY();

// Get employee name properly
$emp_name = 'N/A';
$emp_id_num = 'N/A';
$emp_dob = 'N/A';
$emp_status = 'N/A';

if ($employee) {
    // Use the correct field names from employees_tbl
    $fname = isset($employee['firstname']) ? trim($employee['firstname']) : '';
    $lname = isset($employee['lastname']) ? trim($employee['lastname']) : '';
    $mname = isset($employee['middlename']) ? trim($employee['middlename']) : '';
    
    if (!empty($lname) && !empty($fname)) {
        $emp_name = strtoupper($lname . ', ' . $fname . ' ' . $mname);
    } elseif (!empty($lname)) {
        $emp_name = strtoupper($lname);
    }

    if (isset($employee['employee_id_num']) && !empty($employee['employee_id_num'])) {
        $emp_id_num = $employee['employee_id_num'];
    }
    
    if (isset($employee['birthdate']) && !empty($employee['birthdate']) && $employee['birthdate'] != '0000-00-00') {
        $emp_dob = OutputShortDate($employee['birthdate']);
    }
    
    if (isset($employee['civil_status'])) {
        $emp_status = $employee['civil_status'];
    }
}

// Create info box
$pdf->SetXY(15, $y_pos);
$pdf->MultiCell(170, 3.5, 
    "Name: " . $emp_name . "\n" .
    "Employee ID: " . $emp_id_num . "\n" .
    "Date of Birth: " . $emp_dob . "\n" .
    "Civil Status: " . $emp_status, 
    0, 'L'
);

$pdf->Ln(1);


// Service History Section
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 7, 'SERVICE HISTORY / APPOINTMENT RECORD', 0, 1);

// Service history table with proper column alignment
$pdf->SetFont('helvetica', '', 7.5);

$html = '<table border="1" cellpadding="2" style="font-size:7.5pt;">
  <thead>
    <tr style="background-color:#D3D3D3;font-weight:bold;">
      <th width="5%" align="center">#</th>
      <th width="11%" align="center">Period From</th>
      <th width="11%" align="center">Period To</th>
      <th width="18%" align="left">Position/Designation</th>
      <th width="10%" align="center">Status</th>
      <th width="15%" align="left">Department/Office</th>
      <th width="13%" align="right">Salary Grade/Rate</th>
      <th width="17%" align="left">Work Nature</th>
    </tr>
  </thead>
  <tbody>';

$department_assigned = "";
$i = 1;
$rows = [];
if ($employments) {
  foreach ($employments as $row) {
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
      ? 'PRESENT' 
      : OutputShortDate($row_end->format('Y-m-d'));

    $rows[] = [
      'position_title' => $row['position_title'],
      'employment_type' => $row['employment_type'],
      'row_start' => OutputShortDate($row_start->format('Y-m-d')),
      'display_end' => $display_end,
      'dept_title' => $row['dept_title'],
      'department_assigned' => $department_assigned,
      'designation' => $row['designation'] ? $row['designation'] : $row['position_title'],
      'work_nature' => $row['work_nature'],
      'work_specifics' => $row['work_specifics'],
      'rate' => $row['rate'],
      'sort_date' => $row_start->format('Y-m-d'),
    ];
  }
}
}

// Sort rows by 'sort_date' descending
usort($rows, function($a, $b) {
  return strcmp($b['sort_date'], $a['sort_date']);
});

$i = 1;
foreach ($rows as $row) {
  $rate_display = !empty($row['rate']) ? 'PHP ' . number_format($row['rate'], 2) : 'N/A';
  
  $html .= '<tr>
    <td width="5%" align="center">' . $i++ . '</td>
    <td width="11%" align="center">' . htmlspecialchars($row['row_start']) . '</td>
    <td width="11%" align="center">' . htmlspecialchars($row['display_end']) . '</td>
    <td width="18%" align="left">' . htmlspecialchars($row['designation']) . '</td>
    <td width="10%" align="center">' . htmlspecialchars($row['employment_type']) . '</td>
    <td width="15%" align="left">' . htmlspecialchars($row['dept_title']) . '</td>
    <td width="13%" align="right">' . $rate_display . '</td>
    <td width="17%" align="left">' . htmlspecialchars($row['work_nature']) . '</td>
  </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Ln(3);

// Certification Section
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(0, 4, 
    "This is to certify that the above information is a true and correct record of service of the above-named employee based on the official records of this office.\n\n" .
    "This certification is issued to " . $emp_name . " for whatever legal purpose/s it may serve.",
    0, 'L'
);

$pdf->Ln(8);

// Signature lines
$pdf->SetFont('helvetica', '', 9);
$signatory_y = $pdf->GetY();

// Certifying Officer signature
$pdf->SetXY(15, $signatory_y);
$pdf->Cell(75, 4, '', 0, 0);
$pdf->Cell(75, 4, '', 0, 1);

$pdf->SetXY(15, $signatory_y + 12);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(75, 3, '____________________________', 0, 0, 'C');
$pdf->Cell(75, 3, '____________________________', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(15, $signatory_y + 15);
$pdf->Cell(75, 3, 'Certifying Officer / HR Head', 0, 0, 'C');
$pdf->Cell(75, 3, 'Department Head / Official in Charge', 0, 1, 'C');

$pdf->SetXY(15, $signatory_y + 18);
$pdf->Cell(75, 2.5, 'Name and Position', 0, 0, 'C');
$pdf->Cell(75, 2.5, 'Name and Position', 0, 1, 'C');

$pdf->Ln(2);

// Date of Certification
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 3.5, 'Date of Certification: ______________________', 0, 1, 'L');

$pdf->Ln(2);

ob_end_clean();
// Output
$pdf->Output('service_record_' . $employee_id . '.pdf', 'I');
