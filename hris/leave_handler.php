<?php
include_once '../includes/class/Leave.php';

if($action = isset($_POST['action'])?$_POST['action']:'') {
    switch ($action) {
        case 'save_leave_type':
            $Leave = new Leave();  
            $leaveType_data = [];

            // Get POST data
            $leave_type_id = isset($_POST['leave_type_id']) && $_POST['leave_type_id'] != "" ? intval($_POST['leave_type_id']) : 0;
            $leave_code = trim($_POST['code']);
            $leave_name = trim($_POST['name']);
            $yearly_allotment = intval($_POST['yearly_allotment']);
            $monthly_accrual = intval($_POST['monthly_accrual']);
            $is_accumulative = intval($_POST['is_accumulative']);
            $max_accumulation = isset($_POST['max_accumulation']) ? intval($_POST['max_accumulation']) : NULL;
            $gender_restriction = $_POST['gender_restriction'];
            $reset_policy = $_POST['reset_policy'];
            $requires_attachment = intval($_POST['requires_attachment']);
            $active = intval($_POST['active']);
            $frequency_limit = isset($_POST['frequency_limit']) ? trim($_POST['frequency_limit']) : NULL;
            $description = isset($_POST['description']) ? trim($_POST['description']) : NULL;

            $leaveType_data = [
                'leave_type_id' => $leave_type_id,
                'leave_code' => $leave_code,
                'leave_name' => $leave_name,
                'yearly_allotment' => $yearly_allotment,
                'monthly_accrual' => $monthly_accrual,
                'is_accumulative' => $is_accumulative,
                'max_accumulation' => $max_accumulation,
                'gender_restriction' => $gender_restriction,
                'reset_policy' => $reset_policy,
                'requires_attachment' => $requires_attachment,
                'active' => $active,
                'frequency_limit' => $frequency_limit,
                'description' => $description
            ];

            $Leave->setLeaveTypeData($leaveType_data);
            $result = $leave_type_id > 0 ? $Leave->UpdateLeaveType() : $Leave->AddLeaveType();
            // Return JSON response for AJAX
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result ? true : false,
                'message' => $result ? 'Leave type saved successfully.' : 'Failed to save leave type.'
            ]);
            exit;

            break;

        case 'get_leave_type_details':
            $Leave = new Leave();  
            
            $leave_type_id = $_POST['leave_type_id'];
            
            $leave_type_details = $Leave->GetLeaveTypeDetails($leave_type_id);
            if ($leave_type_details) {
                echo json_encode($leave_type_details);
            }
            else {
                return false;
            }
            break;

        case 'delete_leave_type':
			$Leave = new Leave();

			$json_data = "";
			$leave_type_id = $_POST['leave_type_id'];
			if ($Leave->DeleteLeaveType($leave_type_id)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
            break;

        case 'initialize_balances':
            $Leave = new Leave();
            if (isset($_POST['employee_id'], $_POST['year'])) {
                $empId = intval($_POST['employee_id']);
                $year = intval($_POST['year']);

                try {
                    // Assuming initializeEmployeeLeaveBalances is a method of Leave class
                    $Leave->initializeEmployeeLeaveBalances($empId, $year);
                    echo json_encode(["status" => "success"]);
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid request."]);
            }
            break;
        
        case 'fetch_employee_balances':
            $Leave = new Leave();
            if (isset($_POST['employee_id'], $_POST['year'])) {
                $empId = intval($_POST['employee_id']);
                $year = intval($_POST['year']);

                $result = $Leave->FetchEmployeeLeaveBalances($empId, $year);

                if ($result !== null) {
                    echo json_encode([
                    "status" => "success",
                    "message" => "Leave balances loaded successfully.",
                    "data" => $result
                    ]);
                } 
                else {
                    echo json_encode([
                    "status" => "error",
                    "message" => "Failed to load leave balances.",
                    "data" => null
                    ]);
                }
            } 
            else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid request.",
                    "data" => null
                ]);
            }
            exit;
            break;

        case 'approve_leave_application':
            $Leave = new Leave();
            if (isset($_POST['leave_application_id'])) {
                $leaveAppId = intval($_POST['leave_application_id']);

                // ApproveLeaveApplication returns an array with 'success' and optionally 'error'
                $result = $Leave->ApproveLeaveApplication($leaveAppId);

                if (is_array($result) && isset($result['success']) && $result['success'] === true) {
                    echo json_encode(["status" => "success"]);
                } else {
                    // Map error codes to messages
                    $errorMessages = [
                        'application_not_found' => 'Leave application not found.',
                        'uninitialized_balances' => 'Leave balances are not initialized for the current year. Please initialize them first.',
                        'leave_type_not_initialized' => 'Leave type not initialized. Please initialize them first.',
                        'insufficient_balance' => 'Insufficient leave balance to approve this application.',
                        'update_failed' => 'Failed to update leave application status.'
                    ];
                    $errorMsg = "Failed to approve leave application.";
                    if (is_array($result) && isset($result['error']) && isset($errorMessages[$result['error']])) {
                        $errorMsg = $errorMessages[$result['error']];
                    }
                    echo json_encode([
                        "status" => "error",
                        "message" => $errorMsg
                    ]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid request."]);
            }
            break;

        case 'disapprove_leave_application':
            $Leave = new Leave();
            if (isset($_POST['leave_application_id'])) {
                $leaveAppId = intval($_POST['leave_application_id']);

                try {
                    // Assuming disapproveLeaveApplication is a method of Leave class
                    $Leave->DisapproveLeaveApplication($leaveAppId);
                    echo json_encode(["status" => "success"]);
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid request."]);
            }
            break;

        default:
            # code...
            break;
    }
}

?>