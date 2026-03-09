<?php
require_once('../includes/simplexlsxgen/SimpleXLSXGen.php');
require_once('../includes/view/functions.php');
require_once('../includes/class/DB_conn.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Signatory.php');

use Shuchkin\SimpleXLSXGen;

$db = new DB_conn();
$Payroll = new Payroll();
$Signatory = new Signatory();

$period_id = $_GET['period_id'] ?? '';
$dept_id_filter = $_GET['dept_id'] ?? 'all';
$emp_type_filter = $_GET['employment_type'] ?? 'all';

// Fetch Period info
$periodInfo = $Payroll->GetPayrollPeriodByID($period_id);
$period_label = $periodInfo['period_label'] ?? 'Unknown Period';

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
$query .= " ORDER BY dept.dept_title ASC, e.lastname ASC, e.firstname ASC";

$result = $db->query($query);
$data_by_dept = [];
$grand_total = ['gross' => 0, 'deductions' => 0, 'net' => 0];

while ($row = $result->fetch_assoc()) {
    $dept_name = $row['dept_title'];
    if (!isset($data_by_dept[$dept_name])) {
        $data_by_dept[$dept_name] = [];
    }
    $data_by_dept[$dept_name][] = $row;
    $grand_total['gross'] += $row['gross'];
    $grand_total['deductions'] += $row['total_deductions'];
    $grand_total['net'] += $row['net_pay'];
}

// Styles
$H_CENTER = '<b><center><style font-size="12">';
$T_HDR    = '<b><center><style bgcolor="FFF2F2F2" border="thin">';
$T_TEXT   = '<style border="thin">';
$T_NUM    = '<right><style border="thin" nf="#,##0.00">';
$T_BOLD_L = '<b><style border="thin" bgcolor="FFFAFAFA">';
$T_BOLD_R = '<b><right><style border="thin" bgcolor="FFFAFAFA" nf="#,##0.00">';

$rows = [
    [$H_CENTER . 'Republic of the Philippines'],
    [$H_CENTER . 'Municipality of Polanco'],
    [],
    ['<b><center><style font-size="14">SUMMARY LIST OF PAYROLL'],
    ['<center>For the Period: ' . $period_label],
    [],
    [$T_HDR . '#', $T_HDR . 'Employee Name', $T_HDR . 'Position', $T_HDR . 'Gross', $T_HDR . 'Deductions', $T_HDR . 'Net Pay']
];

foreach ($data_by_dept as $dept => $employees) {
    $rows[] = ['<b>' . $dept, null, null, null, null, null];
    $count = 1;
    foreach ($employees as $emp) {
        $rows[] = [
            $T_TEXT . $count++,
            $T_TEXT . $emp['full_name'],
            $T_TEXT . $emp['position_title'],
            $T_NUM  . $emp['gross'],
            $T_NUM  . $emp['total_deductions'],
            $T_NUM  . $emp['net_pay']
        ];
    }
}

$rows[] = [
    '<b><right>GRAND TOTAL:', null, null,
    $T_BOLD_R . $grand_total['gross'],
    $T_BOLD_R . $grand_total['deductions'],
    $T_BOLD_R . $grand_total['net']
];

// Signatories
$rows[] = [];
$rows[] = [];
$signatories = $Signatory->FetchActiveSignatoriesByReportType('ACCTG');
if (!empty($signatories)) {
    $sigRow1 = []; $sigRow2 = []; $sigRow3 = []; $sigRow4 = [];
    foreach ($signatories as $sig) {
        $sigRow1[] = '<i>' . $sig['sign_particulars']; $sigRow1[] = null;
        $sigRow2[] = null; $sigRow2[] = null;
        $sigRow3[] = '<b>' . $sig['full_name']; $sigRow3[] = null;
        $sigRow4[] = $sig['position_title']; $sigRow4[] = null;
    }
    $rows[] = $sigRow1;
    $rows[] = $sigRow2;
    $rows[] = $sigRow3;
    $rows[] = $sigRow4;
}

$xlsx = SimpleXLSXGen::fromArray($rows);
$xlsx->setColWidth(1, 5);
$xlsx->setColWidth(2, 35);
$xlsx->setColWidth(3, 35);
$xlsx->setColWidth(4, 15);
$xlsx->setColWidth(5, 15);
$xlsx->setColWidth(6, 15);

// Merges
$xlsx->mergeCells('A1:F1');
$xlsx->mergeCells('A2:F2');
$xlsx->mergeCells('A4:F4');
$xlsx->mergeCells('A5:F5');

$filename = 'SLP_' . str_replace(' ', '_', $period_label) . '.xlsx';
$xlsx->downloadAs($filename);
exit;
?>
