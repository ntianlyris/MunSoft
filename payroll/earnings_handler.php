<?php 
include_once '../includes/class/Employment.php';
include_once '../includes/class/Earning.php';
include_once '../includes/view/functions.php';

if($action = isset($_REQUEST['action'])?$_REQUEST['action']:'') {
    switch ($action) {
        case 'save_employee_earnings':
            $Employment = new Employment();
            $EmployeeEarning = new Earning();

            $employee_id = $_POST['employee_id'];
            $employment_id = $_POST['employment_id'];
            $earning_particulars = $_POST['earning_particulars'];
            $effective_date = $_POST['effective_date'];
            $locked_rate = $_POST['basic_rate'];
            $earning_code_ids = $_POST['earning_code_ids'] ?? [];   ## array of earning codes
            $emp_earnings_amts = $_POST['emp-earnings-amt'] ?? [];  ## array of earning code amounts
            $gross = 0;
            $date_created = DateToday();
            $json_data = '';
            
            if (!empty($earning_code_ids) && !empty($emp_earnings_amts)) {        
                for ($i=0; $i <count($earning_code_ids) ; $i++) { 
                    $earning_code_id = htmlspecialchars($earning_code_ids[$i]);
                    $emp_earnings_amt = htmlspecialchars($emp_earnings_amts[$i]);

                    $employee_earnings_arr[] = array($earning_code_id,$emp_earnings_amt);   ## array of earning code with the corresponding amounts
                    $gross = $gross + $emp_earnings_amt;
                }

                if($Employment->isEmployeeEmployed($employee_id)){              ## checks if whether employee has employment
                    $EmployeeEarning->setEmployeeID($employee_id);
                    $EmployeeEarning->setEmploymentID($employment_id);
                    $EmployeeEarning->setEarningParticulars($earning_particulars);
                    $EmployeeEarning->setEffectiveDate($effective_date);
                    $EmployeeEarning->setLockedRate($locked_rate);
                    $EmployeeEarning->setGross($gross);
                    $EmployeeEarning->setDateCreated($date_created);
                    
                    if($EmployeeEarning->SaveEarning() && $EmployeeEarning->SaveEmployeeEarningComponents($employee_earnings_arr)){
                        $json_data = '{"result":"success"}';
                    }
                    else{
                        $json_data = '{"result":"xxx"}';
                    }
                }
                else{
                    $json_data = '{"result":"not_employed"}';
                }
            } 
            else {
                $json_data = '{"result":"xxx"}';
            }
            echo $json_data;
            break;

        case 'edit_employee_earnings':
            $EmployeeEarning = new Earning();

            $employee_earning_id = $_POST['employee_earning_id'];
            //$employee_id = $_POST['employee_id'];                     //---not to be included since its already saved in db and will be permanent data of the employee
            //$earning_particulars = $_POST['earning_particulars'];     //---not to be included since its already saved in db and will be permanent data of the employee
            //$effective_date = $_POST['effective_date'];               //---not to be included since its already saved in db and will be permanent data of the employee
            $earning_code_ids = $_POST['earning_code_ids'] ?? [];   ## array of earning codes
            $emp_earnings_amts = $_POST['emp-earnings-amt'] ?? [];  ## array of earning code amounts
            $gross = 0;
            $date_updated = DateToday();
            $json_data = '';
            
            if (!empty($earning_code_ids) && !empty($emp_earnings_amts)) {        
                for ($i=0; $i <count($earning_code_ids) ; $i++) { 
                    $earning_code_id = htmlspecialchars($earning_code_ids[$i]);
                    $emp_earnings_amt = htmlspecialchars($emp_earnings_amts[$i]);

                    $employee_earnings_arr[] = array($earning_code_id,$emp_earnings_amt);   ## array of earning code with the corresponding amounts
                    $gross = $gross + $emp_earnings_amt;
                }

                //$EmployeeEarning->setEmployeeID($employee_id);
                //$EmployeeEarning->setEarningParticulars($earning_particulars);
                //$EmployeeEarning->setEffectiveDate($effective_date);
                $EmployeeEarning->setGross($gross);
                $EmployeeEarning->setDateUpdated($date_updated);
                $EmployeeEarning->setEmployeeEarningID($employee_earning_id);

                if($EmployeeEarning->EditEarning() && $EmployeeEarning->DeleteEmployeeEarningComponents($employee_earning_id) && $EmployeeEarning->SaveEmployeeEarningComponents($employee_earnings_arr)){
                    $json_data = '{"result":"success"}';
                }
                else{
                    $json_data = '{"result":"xxx"}';
                }
            }
            echo $json_data;
            break;

        case 'get_emp_earning':
            $EmployeeEarning = new Earning();

            $employee_earning_id = $_GET['emp_earning_id'];

            $employee_earning = $EmployeeEarning->GetEmployeeEarningLinkedToEmployment($employee_earning_id,);
            if ($employee_earning) {
                echo json_encode($employee_earning);
            }
            else {
                return false;
            }
            break;
        
        case 'get_emp_earning_comps':
            $EmployeeEarning = new Earning();

            $employee_earning_id = $_GET['emp_earning_id'];

            $earning_comps = $EmployeeEarning->GetEmployeeEarningComps($employee_earning_id);
            if ($earning_comps) {
                echo json_encode($earning_comps);
            }
            else {
                return false;
            }
            break;

        case 'delete_emp_earning':
            include_once '../includes/class/Earning.php';

			$EmployeeEarning = new Earning();

			$json_data = "";
			$emp_earning_id = $_POST['emp_earning_id'];
			if ($EmployeeEarning->DeleteEmployeeEarning($emp_earning_id) && $EmployeeEarning->DeleteEmployeeEarningComponents($emp_earning_id)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
            break;

        case 'get_employee_current_employment':
            include_once '../includes/class/Employment.php'; 

            $employee_id = $_POST['employee_id'];
            $Employment = new Employment();

            $employment = $Employment->FetchActiveEmploymentDetails($employee_id);

            if ($employment) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'ok',
                    'data' => $employment
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'fail',
                    'message' => 'No active employment record found. Please add employment record.'
                ]);
            }
            exit;
            break;
        
        default:
            # code...
            break;
    }
}

?>