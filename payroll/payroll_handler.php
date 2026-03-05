<?php
require_once '../includes/class/Payroll.php';
require_once '../includes/class/Employee.php';
require_once '../includes/class/Employment.php';

if($action = isset($_POST['action'])?$_POST['action']:'') {
    switch ($action) {
        case 'compute_payroll':
            $Payroll = new Payroll();

            $period = $_POST['period'];     // e.g., "2025-07-01_2025-07-15"
            $department = $_POST['department'];
            $employment_type = isset($_POST['employment_type']) ? $_POST['employment_type'] : 'Regular';
            $employeesPayrollData = [];

            list($start_date, $end_date) = explode('_', $period); // Split to get start and end

            $employees = $Payroll->GetEmployeeListForPayrollByDept($department, $employment_type);

            $employeesPayrollData = $Payroll->ComputePayrollOfEmployees($employees, $start_date, $end_date);

            // Return as JSON (for AJAX)
            header('Content-Type: application/json');
            echo json_encode($employeesPayrollData);
            
            break;
        
        case 'save_payroll':

            $Payroll = new Payroll();

            $period = $_POST['period']; // e.g., "2025-07-01_2025-07-15"
            $department = $_POST['department'];
            $employment_type = isset($_POST['employment_type']) ? $_POST['employment_type'] : 'Regular';
            $payroll_data = [];

            list($start_date, $end_date) = explode('_', $period);

            $employees = $Payroll->GetEmployeeListForPayrollByDept($department, $employment_type);

            $payroll_data = $Payroll->ComputePayrollOfEmployees($employees, $start_date, $end_date);

            //$payroll_data = $_POST['payroll_data']; // array of computed data per employee (from frontend)

            // Get payroll_period_id using the date range
            $periodRow = $Payroll->GetPayrollPeriodByDates($start_date, $end_date);

            if (!$periodRow) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Payroll period not found.']);
                exit;
            }
            $payroll_period_id = $periodRow['payroll_period_id'];

            //pass payroll data and payroll period id
            $Payroll->setPayrollData($payroll_data);
            $Payroll->setPayrollPeriodID($payroll_period_id);
            $Payroll->setPayrollDepartmentID($department);
            $Payroll->setPayrollEmploymentType($employment_type);

            //save to db, 
            $returned_data = [];
            if ($returned_data = $Payroll->SavePayrollEntry()) {
                header('Content-Type: application/json');
                echo json_encode($returned_data);
            }
            else{
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'xxx',
                    'message' => "0 payroll records saved. Please try again.",
                    'saved' => 0
                ]);
            }
            break;

        case 'fetch_payroll_periods_of_year':
            $Payroll = new Payroll();
            $year = intval($_POST['year']);
            $active_frequency = $Payroll->GetCurrentActiveFrequency();
            $frequency = $active_frequency['freq_code'];
            
            $periods = $Payroll->GetPayrollPeriodsByYear($year,$frequency);

            $data = [];
            foreach ($periods as $period) {
                $data[] = [
                    "payroll_period_id" => $period['payroll_period_id'],
                    "period_label"      => $period['period_label']
                ];
            }
            header('Content-Type: application/json');
            echo json_encode($data);
            break;

        case 'fetch_payroll_data':
            $Payroll = new Payroll();
            $Employee = new Employee();
            $Employment = new Employment();

            //$year = intval($_POST['year']);
            $payroll_period_id = intval($_POST['payroll_period_id']);
            $department_id = intval($_POST['department_id']);
            $employment_type = isset($_POST['employment_type']) ? $_POST['employment_type'] : 'Regular';

            $payroll_data = $Payroll->FetchPayrollByPayPeriodAndDept($payroll_period_id, $department_id, $employment_type);

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
                // keep the array sorted by salary_grade descending
                usort($enhanced_payroll_data, function($a, $b) {
                    $sa = isset($a['salary_grade']) ? (int)$a['salary_grade'] : 0;
                    $sb = isset($b['salary_grade']) ? (int)$b['salary_grade'] : 0;
                    return $sb <=> $sa;
                });
            }

            header('Content-Type: application/json');
            echo json_encode($enhanced_payroll_data);
            break;

        case 'toggle_include_payroll':
            $Payroll = new Payroll();
            $employee_id = intval($_POST['employee_id']);
            $include_in_payroll = intval($_POST['include_in_payroll']);
            $result = $Payroll->UpdateIncludeInPayroll($employee_id, $include_in_payroll);
            
            if($result){
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error']);
            }
            break;

        case 'get_payroll_entry':
            // SECURITY: Validate user session exists
            session_start();
            if (!isset($_SESSION['uid'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized - please login']);
                exit;
            }
            
            $Payroll = new Payroll();
            $payroll_entry_id = intval($_POST['payroll_entry_id'] ?? 0);
            
            // SECURITY: Validate input
            if ($payroll_entry_id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid payroll entry ID']);
                exit;
            }
            
            $payroll = $Payroll->GetPayrollEntryByID($payroll_entry_id);
            
            if ($payroll) {
                // Parse JSON breakdown data
                $payroll['earnings'] = json_decode($payroll['earnings_breakdown'] ?? '[]', true);
                $payroll['deductions_list'] = json_decode($payroll['deductions_breakdown'] ?? '[]', true);
                $payroll['govshares_list'] = json_decode($payroll['govshares_breakdown'] ?? '[]', true);
            }
            
            header('Content-Type: application/json');
            echo json_encode($payroll);
            break;
        case 'get_payroll_audit_trail':
            // SECURITY: Validate user session exists
            session_start();
            if (!isset($_SESSION['uid'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized - please login']);
                exit;
            }
            
            $Payroll = new Payroll();
            $payroll_entry_id = intval($_POST['payroll_entry_id'] ?? 0);
            $limit = intval($_POST['limit'] ?? 50);
            
            // SECURITY: Validate input
            if ($payroll_entry_id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid payroll entry ID']);
                exit;
            }
            
            if ($limit <= 0 || $limit > 100) {
                $limit = 50;
            }
            
            $audit_trail = $Payroll->GetPayrollAuditTrail($payroll_entry_id, $limit);
            
            header('Content-Type: application/json');
            echo json_encode($audit_trail);
            break;

        case 'delete_payroll_records':
            // BULK DELETE: Delete DRAFT payroll records for a department and period
            // SECURITY: Requires session validation and confirmation
            session_start();
            
            if (!isset($_SESSION['uid'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized - please login']);
                exit;
            }
            
            $Payroll = new Payroll();
            $payroll_period_id = intval($_POST['payroll_period_id'] ?? 0);
            $dept_id = intval($_POST['dept_id'] ?? 0);
            $emp_type_stamp = $_POST['emp_type_stamp'] ?? 'Regular';
            $emp_type_stamp = ($emp_type_stamp === 'Casual') ? 'Casual' : 'Regular';  // Validate against known values
            $user_id = intval($_SESSION['uid']);
            
            // SECURITY: Validate input
            if ($payroll_period_id <= 0 || $dept_id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid payroll period or department ID']);
                exit;
            }
            
            // Call bulk delete method
            $result = $Payroll->DeleteAllPayrollRecordsForPeriodAndDept($payroll_period_id, $dept_id, $emp_type_stamp, $user_id);
            
            header('Content-Type: application/json');
            http_response_code($result['status'] === 'success' ? 200 : 400);
            echo json_encode($result);
            break;

        default:
            # code...
            break;
    }
}



?>