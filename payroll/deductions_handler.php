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

            $employee_id = $_POST['employee_id'];
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
            $employee_id = $_POST['employee_id'];
            $deduction_particulars = $_POST['deduction_particulars'];
            $effective_date = $_POST['effective_date'];
            $config_deduction_ids = $_POST['config_deduction_ids'] ?? [];   ## array of earning codes
            $emp_deductions_amts = $_POST['emp-deductions-amt'] ?? [];  ## array of earning code amounts
            $total_deductions = 0;
            $date_updated = DateToday();
            $json_data = '';

            $last_locked_period = $Payroll->GetLastLockedPayrollPeriodByEmployee($employee_id);
            $locked_start_date = $last_locked_period['date_start'] ?? null;
            
            $active_frequency = $Payroll->GetCurrentActiveFrequency();
            $frequency = $active_frequency['freq_code'] ?? 'monthly';

            if($frequency == 'semi-monthly' && $locked_start_date){
                $is_second_half = $Payroll->IsSecondHalfOfMonth($locked_start_date);
                if(!$is_second_half){
                    $json_data = '{"result":"block_edit"}';
                    echo $json_data;
                    exit;
                }
            }

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