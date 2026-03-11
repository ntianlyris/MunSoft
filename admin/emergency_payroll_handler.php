<?php
require_once '../includes/class/Admin.php';
require_once '../includes/class/Payroll.php';
require_once '../includes/class/Employment.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$MyAdmin = new Admin();
$user_id = $MyAdmin->getSessionUID();
$roles = $MyAdmin->initRoles($user_id);
$is_admin = false;
$has_manage_system = false;

if ($roles) {
    foreach ($roles as $role_name => $perms) {
        if ($role_name === 'Administrator') {
            $is_admin = true;
        }
        if ($perms && in_array('Manage System', $perms)) {
            $has_manage_system = true;
        }
    }
}

// Security Check: Only Administrators with Manage System permissions
if (!$user_id || !$is_admin || !$has_manage_system) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if($action = isset($_POST['action'])?$_POST['action']:'') {
    switch ($action) {
        case 'fetch_payroll_data':
            $Payroll = new Payroll();
            $Employee = new Employee();
            $Employment = new Employment();

            $payroll_period_raw = $_POST['payroll_period_id'];
            $department_id = intval($_POST['department_id']);
            $employment_type = isset($_POST['employment_type']) ? $_POST['employment_type'] : 'Regular';

            if (strpos($payroll_period_raw, '_') !== false) {
                list($start, $end) = explode('_', $payroll_period_raw);
                $periodRow = $Payroll->GetPayrollPeriodByDates($start, $end);
                $payroll_period_id = $periodRow ? $periodRow['payroll_period_id'] : 0;
            } else {
                $payroll_period_id = intval($payroll_period_raw);
            }

            $payroll_data = $Payroll->FetchPayrollByPayPeriodAndDept($payroll_period_id, $department_id, $employment_type);
            // ... (rest of processing)
            $enhanced_payroll_data = [];
            foreach ($payroll_data as $row) {
                $employee_id = $row['employee_id'];
                $employment_id = $row['employment_id'];
                $id_num = $Employee->GetEmployeeDetails($employee_id)['employee_id_num'];
                $full_name = $Employee->GetEmployeeFullNameByID($employee_id)['full_name'];
                $position_full = $Employment->FetchEmployeeEmploymentDetailsByIDs($employee_id, $employment_id)['position_title'];
                $position_title = strpos($position_full, '(') !== false ? substr($position_full, 0, strpos($position_full, '(')) : $position_full;

                $row['id_num'] = $id_num;
                $row['full_name'] = $full_name;
                $row['position_title'] = trim($position_title);

                $enhanced_payroll_data[] = $row;
            }

            usort($enhanced_payroll_data, function($a, $b) {
                $sa = isset($a['salary_grade']) ? (int)$a['salary_grade'] : 0;
                $sb = isset($b['salary_grade']) ? (int)$b['salary_grade'] : 0;
                return $sb <=> $sa;
            });

            header('Content-Type: application/json');
            echo json_encode($enhanced_payroll_data);
            break;

        case 'delete_payroll_records':
            $Payroll = new Payroll();
            $payroll_period_raw = $_POST['payroll_period_id'] ?? 0;
            $dept_id = intval($_POST['dept_id'] ?? 0);
            $emp_type_stamp = $_POST['emp_type_stamp'] ?? 'Regular';
            $user_id = intval($_SESSION['uid']);
            
            if (strpos($payroll_period_raw, '_') !== false) {
                list($start, $end) = explode('_', $payroll_period_raw);
                $periodRow = $Payroll->GetPayrollPeriodByDates($start, $end);
                $payroll_period_id = $periodRow ? $periodRow['payroll_period_id'] : 0;
            } else {
                $payroll_period_id = intval($payroll_period_raw);
            }

            if ($payroll_period_id <= 0 || $dept_id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid parameters.']);
                exit;
            }
            
            $result = $Payroll->EmergencyDeleteAllPayrollRecordsForPeriodAndDept($payroll_period_id, $dept_id, $emp_type_stamp, $user_id);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
            break;
    }
}
?>
