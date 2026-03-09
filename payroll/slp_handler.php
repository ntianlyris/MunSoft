<?php
header('Content-Type: application/json');
require_once '../includes/class/DB_conn.php';
require_once '../includes/class/Payroll.php';

$db = new DB_conn();
$Payroll = new Payroll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'fetch_slp') {
        $period_id = $_POST['period_id'] ?? '';
        if (empty($period_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Period selection is required.']);
            exit;
        }
        $period_id = $db->escape_string($period_id);
        $dept_id = $_POST['dept_id'] ?? 'all';
        $employment_type = $_POST['employment_type'] ?? 'all';

        $query = "SELECT pe.*, e.employee_id_num, 
                    CONCAT(e.lastname, ', ', e.firstname, ' ', IF(LENGTH(e.middlename) > 0, CONCAT(LEFT(e.middlename, 1), '. '), ''), ' ', e.extension) AS full_name,
                    pos.position_title, dept.dept_title
                  FROM payroll_entries pe
                  INNER JOIN employees_tbl e ON pe.employee_id = e.employee_id
                  INNER JOIN employee_employments_tbl ee ON pe.employment_id = ee.employment_id
                  INNER JOIN positions_tbl pos ON ee.position_id = pos.position_id
                  INNER JOIN departments_tbl dept ON pe.dept_id = dept.dept_id
                  WHERE pe.payroll_period_id = '$period_id'";

        if ($dept_id !== 'all') {
            $query .= " AND pe.dept_id = '" . $db->escape_string($dept_id) . "'";
        }

        if ($employment_type !== 'all') {
            $query .= " AND pe.emp_type_stamp = '" . $db->escape_string($employment_type) . "'";
        }

        $query .= " ORDER BY dept.dept_title ASC, e.lastname ASC, e.firstname ASC";

        $result = $db->query($query);
        $data = [];
        $totals = ['gross' => 0, 'deductions' => 0, 'net' => 0];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
                $totals['gross'] += floatval($row['gross']);
                $totals['deductions'] += floatval($row['total_deductions']);
                $totals['net'] += floatval($row['net_pay']);
            }
            echo json_encode([
                'status' => 'success',
                'data' => $data,
                'totals' => $totals
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No payroll records found for the selected filter.'
            ]);
        }
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
?>
