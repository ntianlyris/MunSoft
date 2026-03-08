<?php
require_once '../includes/class/Payroll.php';
require_once '../includes/class/Employee.php';
require_once '../includes/class/Employment.php';
require_once '../includes/class/DB_conn.php';

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

<<<<<<< HEAD
            // ═══════════════════════════════════════════════════════════
            // GAA NET PAY THRESHOLD VALIDATION - Phase 5 Integration
            // ═══════════════════════════════════════════════════════════
            require_once '../includes/class/GAANetPayValidator.php';
            $db_for_gaa = new DB_conn();
            $validator = new GAANetPayValidator($db_for_gaa, $_SESSION['admin_id'] ?? $_SESSION['user_id'] ?? 0);
            
            // Validate each employee's net pay before saving any records
            $gaa_violations = [];
            foreach ($payroll_data as $entry) {
                $employee_id = $entry['employee_id'];
                $gross = floatval($entry['gross']);
                $deductions_total = floatval($entry['deductions']);
                // Estimate mandatory (for now, assume all are deductions)
                $mandatory_deductions = $deductions_total * 0.7; // Rough estimate
                $authorized_deductions = $deductions_total * 0.3;
                
                $net_pay = $validator->computeNetPay($gross, $mandatory_deductions, $authorized_deductions);
                
                // Check threshold
                if ($net_pay < 5000.00) {
                    $gaa_violations[] = [
                        'employee_id' => $employee_id,
                        'employee_name' => $entry['employee_name'] ?? 'Unknown',
                        'net_pay' => $net_pay,
                        'shortfall' => round(5000.00 - $net_pay, 2)
=======
            // ========== NEW: GAA VALIDATION (Stage 2) ==========
            require_once '../includes/class/GAANetPayValidator.php';
            $GAAValidator = new GAANetPayValidator();
            $active_frequency = $Payroll->GetCurrentActiveFrequency();
            $payroll_frequency = $active_frequency['freq_code'];
            
            $validation_errors = [];
            $validation_results = []; // Store validation per employee for audit
            
            foreach ($payroll_data as $emp) {
                $emp_id = $emp['employee_id'];
                $emp_name = $emp['name'];
                $emp_gross = $emp['gross'];
                $emp_deductions = $emp['deductions'];
                
                // Validate this employee's payroll
                $validation_result = $GAAValidator->validatePayrollEntry(
                    $emp_id,
                    $emp_gross,
                    $emp_deductions,
                    $payroll_frequency
                );
                
                $validation_results[$emp_id] = $validation_result;
                
                // If validation fails, collect error
                if (!$validation_result['is_valid']) {
                    $violation = $validation_result['violations'][0];
                    $validation_errors[] = [
                        'employee_id' => $emp_id,
                        'employee' => $emp_name,
                        'gross' => $emp_gross,
                        'deductions' => $emp_deductions,
                        'net_pay' => $validation_result['net_pay'],
                        'shortfall' => $violation['shortfall_amount']
>>>>>>> 337bf56dde9b7eb7e334a7d6f76d5e5591844699
                    ];
                }
            }
            
<<<<<<< HEAD
            // If any violations found, block the entire save
            if (!empty($gaa_violations)) {
                http_response_code(422);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'GAA Compliance Violation: ' . count($gaa_violations) . ' employee(s) below PHP 5,000.00 threshold',
                    'gaa_blocked' => true,
                    'violations' => $gaa_violations
                ]);
                exit;
            }
            // ═══════════════════════════════════════════════════════════
=======
            // If any violations, BLOCK save and return error
            if (!empty($validation_errors)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'validation_failed',
                    'message' => 'Cannot save payroll. ' . count($validation_errors) . ' employee(s) below GAA threshold.',
                    'validation_errors' => $validation_errors,
                    'affected_count' => count($validation_errors),
                    'threshold' => 5000.00
                ]);
                exit; // STOP - don't save
            }
            // ========== END GAA VALIDATION ==========
>>>>>>> 337bf56dde9b7eb7e334a7d6f76d5e5591844699

            //pass payroll data and payroll period id
            $Payroll->setPayrollData($payroll_data);
            $Payroll->setPayrollPeriodID($payroll_period_id);
            $Payroll->setPayrollDepartmentID($department);
            $Payroll->setPayrollEmploymentType($employment_type);

            //save to db, 
            $returned_data = [];
            if ($returned_data = $Payroll->SavePayrollEntry()) {
                // ========== NEW: Log GAA validation to audit trail ==========
                $db_conn = new DB_conn();  // Create local DB connection for audit queries
                
                foreach ($payroll_data as $emp) {
                    // Query to get the saved payroll_entry_id
                    $emp_id = $emp['employee_id'];
                    $audit_query = "SELECT payroll_entry_id FROM payroll_entries 
                                    WHERE employee_id = '$emp_id' 
                                    AND payroll_period_id = '$payroll_period_id'
                                    ORDER BY created_at DESC LIMIT 1";
                    $audit_result = $db_conn->query($audit_query);
                    if ($audit_result && $db_conn->num_rows($audit_result) > 0) {
                        $audit_row = $db_conn->fetch_array($audit_result);
                        $payroll_entry_id = $audit_row['payroll_entry_id'];
                        
                        $validation = $validation_results[$emp_id];
                        $shortfall = empty($validation['violations']) ? 0 : $validation['violations'][0]['shortfall_amount'];
                        
                        // Save GAA status to payroll_gaa_status
                        $GAAValidator->saveGAAStatus($payroll_entry_id, false, 0); // Mark as PASS
                        
                        // Log to audit trail
                        $GAAValidator->logValidationAudit(
                            $payroll_entry_id,
                            $emp_id,
                            'PAYROLL_SAVE',
                            $validation['net_pay'],
                            $shortfall,
                            'PASS',
                            'Payroll saved successfully'
                        );
                    }
                }
                // ========== END AUDIT LOGGING ==========
                
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

        case 'update_payroll_status_bulk':
            // BULK UPDATE: Update status for multiple payroll entries with workflow validation.
            // SECURITY: Requires session validation, workflow rule compliance, AND explicit
            // payroll_period_id / dept_id / emp_type_stamp context so that the update is
            // strictly scoped to the currently selected period + department + employment type.
            session_start();
            
            if (!isset($_SESSION['uid'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized - please login']);
                exit;
            }
            
            $Payroll = new Payroll();
            $payroll_entry_ids = isset($_POST['payroll_entry_ids']) ? 
                array_map('intval', (array)$_POST['payroll_entry_ids']) : [];
            $new_status        = $_POST['new_status'] ?? '';
            $reason            = isset($_POST['reason']) ? $_POST['reason'] : null;
            $user_id           = intval($_SESSION['uid']);

            // --- Context fields (REQUIRED) ---
            // These three values scope the operation to the exact combination that the
            // user has selected in the UI.  The Payroll class will enforce them at the
            // SQL level, so even a crafted POST cannot touch records from other periods,
            // departments, or employment types.
            $payroll_period_id = intval($_POST['payroll_period_id'] ?? 0);
            $dept_id           = intval($_POST['dept_id']           ?? 0);
            $emp_type_stamp    = $_POST['emp_type_stamp'] ?? 'Regular';
            $emp_type_stamp    = ($emp_type_stamp === 'Casual') ? 'Casual' : 'Regular'; // whitelist

            // SECURITY: Validate inputs
            if (empty($payroll_entry_ids)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'No payroll entries provided']);
                exit;
            }
            
            $valid_statuses = ['DRAFT', 'REVIEW', 'APPROVED', 'PAID'];
            if (!in_array($new_status, $valid_statuses)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Invalid status: $new_status"]);
                exit;
            }

            // Validate context fields
            if ($payroll_period_id <= 0 || $dept_id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid or missing payroll period / department. Please re-select your filters and try again.'
                ]);
                exit;
            }
            
            // Call bulk update method with full context
            $result = $Payroll->BulkUpdatePayrollStatus(
                $payroll_entry_ids,
                $new_status,
                $user_id,
                $reason,
                $payroll_period_id,
                $dept_id,
                $emp_type_stamp
            );
            
            header('Content-Type: application/json');
            http_response_code($result['status'] === 'success' ? 200 : 400);
            echo json_encode($result);
            break;

        case 'get_payroll_status_counts':
            // Get status distribution for dashboard/summary
            session_start();
            
            if (!isset($_SESSION['uid'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized - please login']);
                exit;
            }
            
            $Payroll = new Payroll();
            $payroll_period_id = intval($_POST['payroll_period_id'] ?? 0);
            $dept_id = intval($_POST['dept_id'] ?? 0);
            $emp_type = $_POST['emp_type'] ?? 'Regular';
            
            if ($payroll_period_id <= 0 || $dept_id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid period or department ID']);
                exit;
            }
            
            $counts = $Payroll->GetPayrollStatusCounts($payroll_period_id, $dept_id, $emp_type);
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'counts' => $counts]);
            break;

        case 'get_transition_history':
            // Get workflow transition history for a payroll entry
            session_start();
            
            if (!isset($_SESSION['uid'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized - please login']);
                exit;
            }
            
            $Payroll = new Payroll();
            $payroll_entry_id = intval($_POST['payroll_entry_id'] ?? 0);
            $limit = intval($_POST['limit'] ?? 50);
            
            if ($payroll_entry_id <= 0) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid payroll entry ID']);
                exit;
            }
            
            if ($limit <= 0 || $limit > 100) {
                $limit = 50;
            }
            
            $transitions = $Payroll->GetTransitionHistory($payroll_entry_id, $limit);
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'transitions' => $transitions]);
            break;

        case 'validate_batch_approval':
<<<<<<< HEAD
            require_once('../includes/class/GAANetPayValidator.php');
            
            $payroll_period_id = isset($_POST['payroll_period_id']) ? intval($_POST['payroll_period_id']) : 0;
            
            if (!$payroll_period_id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing payroll_period_id']);
                break;
            }
            
            $gaa_validator = new GAANetPayValidator($db, $_SESSION['admin_id'] ?? $_SESSION['user_id'] ?? 0);
            $validation_result = $gaa_validator->validatePayrollBatch($payroll_period_id);
            
            if (!$validation_result['can_approve']) {
                http_response_code(422);
            }
            
            header('Content-Type: application/json');
            echo json_encode($validation_result);
            break;
=======
            // ========== NEW: STAGE 3 - BATCH APPROVAL VALIDATION ==========
            // Validate all payroll entries for a period before final approval
            
            session_start();
            if (!isset($_SESSION['uid'])) {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized - please login']);
                exit;
            }
            
            require_once '../includes/class/GAANetPayValidator.php';
            $GAAValidator = new GAANetPayValidator();
            
            $payroll_period_id = isset($_POST['payroll_period_id']) ? intval($_POST['payroll_period_id']) : 0;
            $dept_id = isset($_POST['dept_id']) ? intval($_POST['dept_id']) : null;
            
            // Security: Validate input
            if ($payroll_period_id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid payroll period ID.'
                ]);
                exit;
            }
            
            // Get validation result
            $batch_validation = $GAAValidator->validatePayrollBatch($payroll_period_id, $dept_id);
            
            // Log the batch validation attempt to audit trail
            if (!$batch_validation['is_valid']) {
                foreach ($batch_validation['violations'] as $violation) {
                    $GAAValidator->logValidationAudit(
                        $violation['payroll_entry_id'],
                        $violation['employee_id'],
                        'BATCH_APPROVAL',
                        $violation['net_pay'],
                        $violation['shortfall_amount'],
                        'FAIL',
                        'Batch approval validation blocked due to GAA threshold violation'
                    );
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode($batch_validation);
            break;
        // ========== END BATCH APPROVAL VALIDATION ==========
>>>>>>> 337bf56dde9b7eb7e334a7d6f76d5e5591844699

        default:
            # code...
            break;
    }
}



?>