<?php 
include_once '../includes/class/Employment.php';
include_once '../includes/class/Deduction.php';
include_once '../includes/class/Payroll.php';
include_once '../includes/view/functions.php';

if($action = isset($_REQUEST['action'])?$_REQUEST['action']:'') {
    switch ($action) {
        case 'save_employee_deductions':
            $Employment = new Employment();
            $EmployeeDeduction = new Deduction();

            $employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : '';
            $deduction_particulars = $_POST['deduction_particulars'];
            $effective_date = $_POST['effective_date'];
            $config_deduction_ids = $_POST['config_deduction_ids'] ?? [];   ## array of earning codes
            $emp_deductions_amts = $_POST['emp-deductions-amt'] ?? [];  ## array of earning code amounts
            $total_deductions = 0;
            $date_created = DateToday();
            $json_data = '';
            
            if (!empty($config_deduction_ids) && !empty($config_deduction_ids)) {        
                for ($i=0; $i <count($config_deduction_ids) ; $i++) { 
                    $config_deduction_id = htmlspecialchars($config_deduction_ids[$i]);
                    $emp_deductions_amt = htmlspecialchars($emp_deductions_amts[$i]);

                    $employee_deductions_arr[] = array($config_deduction_id,$emp_deductions_amt);   ## array of earning code with the corresponding amounts
                    $total_deductions = $total_deductions + $emp_deductions_amt;
                }

                // ========== GAA THRESHOLD VALIDATION FOR NEW DEDUCTIONS (Stage 1) ==========
                if($Employment->isEmployeeEmployed($employee_id)) {
                    include_once '../includes/class/GAANetPayValidator.php';
                    $GAAValidator = new GAANetPayValidator();
                    
                    // Validate proposed deduction
                    $gaa_validation = $GAAValidator->validateDeductionEntry(
                        intval($employee_id),
                        $total_deductions,
                        null  // Will fetch latest gross automatically
                    );
                    
                    // If validation fails, return warning
                    if (!$gaa_validation['is_valid']) {
                        $json_data = json_encode([
                            'result' => 'warning',
                            'reason' => 'gaa_threshold',
                            'is_valid' => false,
                            'current_gross' => $gaa_validation['current_gross'],
                            'proposed_deduction' => $gaa_validation['proposed_deduction'],
                            'net_after' => $gaa_validation['net_after'],
                            'shortfall' => $gaa_validation['shortfall'],
                            'threshold' => $gaa_validation['threshold'],
                            'message' => $gaa_validation['message']
                        ]);
                        echo $json_data;
                        exit;
                    }
                }
                // ========== END GAA VALIDATION ==========

                if($Employment->isEmployeeEmployed($employee_id)){              ## checks if whether employee has employment
                    $EmployeeDeduction->setEmployeeID($employee_id);
                    $EmployeeDeduction->setDeductionParticulars($deduction_particulars);
                    $EmployeeDeduction->setEffectiveDate($effective_date);
                    $EmployeeDeduction->setTotalDeduction($total_deductions);
                    $EmployeeDeduction->setDateCreated($date_created);
                    
                    if($EmployeeDeduction->SaveEmployeeDeduction() && $EmployeeDeduction->SaveEmployeeDeductionComponents($employee_deductions_arr)){
                        $json_data = '{"result":"success"}';
                    }
                    else{
                        $json_data = '{"result":"xxx"}';
                    }
                }
                else{
                    $json_data = '{"result":"not_employed"}';
                }
            } else {
                $json_data = '{"result":"xxx"}';
            }
            echo $json_data;
            break;

        case 'edit_employee_deductions':
            $EmployeeDeduction = new Deduction();
            $Payroll = new Payroll();

            $employee_deduction_id = $_POST['employee_deduction_id'];
            $employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : '';
            $deduction_particulars = $_POST['deduction_particulars'];
            $effective_date = $_POST['effective_date'];
            $config_deduction_ids = $_POST['config_deduction_ids'] ?? [];   ## array of earning codes
            $emp_deductions_amts = $_POST['emp-deductions-amt'] ?? [];  ## array of earning code amounts
            $total_deductions = 0;
            $date_updated = DateToday();
            $json_data = '';

            // ========== TWO-LAYER BLOCKING SYSTEM ==========
            // Layer 1: Period-based (1st/2nd half semi-monthly consistency)
            // Layer 2: Status-based (workflow state protection)
            
            $last_locked_period = $Payroll->GetLastLockedPayrollPeriodByEmployee($employee_id);
            $payroll_status = $last_locked_period['status'] ?? null;
            $locked_start_date = $last_locked_period['date_start'] ?? null;
            
            // LAYER 2: Check status-based blocking (workflow state protection)
            // Additional checkpoint: Block non-DRAFT status regardless of period
            if($last_locked_period && $payroll_status !== 'DRAFT'){
                $json_data = '{"result":"block_edit","reason":"status","status":"'.$payroll_status.'","message":"Payroll is in '.$payroll_status.' status. Please backtrack to DRAFT status first to make changes to deductions."}';
                echo $json_data;
                exit;
            }

            // LAYER 1: Check period-based blocking (1st/2nd half semi-monthly consistency)
            // Block edits to deductions for 2nd half of semi-monthly payroll to ensure consistency
            // Deductions should only be edited in the 1st half of the month for semi-monthly frequencies
            if($last_locked_period && $Payroll->IsSecondHalfOfMonth($locked_start_date)){
                $json_data = '{"result":"block_edit","reason":"period","period_type":"2nd_half_semi_monthly","message":"Deductions cannot be edited for the 2nd half of semi-monthly payroll period but on the 1st half of the payroll cycle."}';
                echo $json_data;
                exit;
            }

            // ========== LAYER 3: NEW - GAA THRESHOLD VALIDATION (Stage 1) ==========
            // Check if proposed deduction amounts would violate GAA minimum net pay threshold
            if (!empty($config_deduction_ids) && !empty($emp_deductions_amts)) {
                // Calculate proposed total deductions
                $proposed_total_deductions = 0;
                for ($i = 0; $i < count($emp_deductions_amts); $i++) {
                    $proposed_total_deductions += floatval($emp_deductions_amts[$i]);
                }
                
                // Get employee's current gross earnings
                include_once '../includes/class/GAANetPayValidator.php';
                $GAAValidator = new GAANetPayValidator();
                
                $gaa_validation = $GAAValidator->validateDeductionEntry(
                    intval($employee_id),
                    $proposed_total_deductions,
                    null  // Will fetch latest gross automatically
                );
                
                // If validation fails, block the edit and return warning
                if (!$gaa_validation['is_valid']) {
                    $json_data = json_encode([
                        'result' => 'block_edit',
                        'reason' => 'gaa_threshold',
                        'is_valid' => false,
                        'current_gross' => $gaa_validation['current_gross'],
                        'proposed_deduction' => $gaa_validation['proposed_deduction'],
                        'net_after' => $gaa_validation['net_after'],
                        'shortfall' => $gaa_validation['shortfall'],
                        'threshold' => $gaa_validation['threshold'],
                        'message' => $gaa_validation['message']
                    ]);
                    echo $json_data;
                    exit;
                }
                
                // NOTE: Audit logging happens at Stage 2 (payroll save) and Stage 3 (batch approval)
                // At Stage 1, payroll_entry_id doesn't exist yet, so we skip audit logging here
            }
            // ========== END GAA VALIDATION ==========

            if (!empty($config_deduction_ids) && !empty($config_deduction_ids)) {        
                for ($i=0; $i <count($config_deduction_ids) ; $i++) { 
                    $config_deduction_id = htmlspecialchars($config_deduction_ids[$i]);
                    $emp_deductions_amt = htmlspecialchars($emp_deductions_amts[$i]);

                    $employee_deductions_arr[] = array($config_deduction_id,$emp_deductions_amt);   ## array of earning code with the corresponding amounts
                    $total_deductions = $total_deductions + $emp_deductions_amt;
                }

                $EmployeeDeduction->setEmployeeID($employee_id);
                $EmployeeDeduction->setDeductionParticulars($deduction_particulars);
                $EmployeeDeduction->setEffectiveDate($effective_date);
                $EmployeeDeduction->setTotalDeduction($total_deductions);
                $EmployeeDeduction->setDateCreated($date_updated);
                $EmployeeDeduction->setEmployeeDeductionID($employee_deduction_id);
                
                if($EmployeeDeduction->EditEmployeeDeduction() && $EmployeeDeduction->DeleteEmployeeDeductionComponents($employee_deduction_id) && $EmployeeDeduction->SaveEmployeeDeductionComponents($employee_deductions_arr)){
                    $json_data = '{"result":"success"}';
                }
                else{
                    $json_data = '{"result":"xxx"}';
                }
            } else {
                $json_data = '{"result":"xxx"}';
            }
            echo $json_data;
            break;

        case 'get_emp_deduction':
            $EmployeeDeduction = new Deduction();

            $employee_deduction_id = $_GET['employee_deduction_id'];

            $employee_deduction = $EmployeeDeduction->GetEmployeeDeduction($employee_deduction_id);
            if ($employee_deduction) {
                echo json_encode($employee_deduction);
            }
            else {
                return false;
            }
            break;

        case 'get_emp_deduction_comps':
            $EmployeeDeduction = new Deduction();

            $employee_deduction_id = $_GET['employee_deduction_id'];

            $deduction_comps = $EmployeeDeduction->GetEmployeeDeductionComps($employee_deduction_id);
            if ($deduction_comps) {
                echo json_encode($deduction_comps);
            }
            else {
                return false;
            }
            break;
        
        case 'delete_emp_deduction':
            include_once '../includes/class/Deduction.php';

			$EmployeeDeduction = new Deduction();

			$json_data = "";
			$emp_deduction_id = $_POST['emp_deduction_id'];
			if ($EmployeeDeduction->DeleteEmployeeDeduction($emp_deduction_id) && $EmployeeDeduction->DeleteEmployeeDeductionComponents($emp_deduction_id)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
            break;
        
        default:
            # code...
            break;
    }
}

?>