<?php

if (isset($_POST['submit'])){
	$setting_name = $_POST['submit'];

	switch ($setting_name) {
		case 'SaveDepartment':
			include_once '../includes/class/Department.php';
			$dept_id = $_POST['dept_id'];
    		$dept_code = $_POST['dept_code'];
			$dept_title = $_POST['dept_title'];
    		$dept_name = $_POST['dept_name'];
			$json_data = "";

    		$MyDepartment = new Department();

    		$MyDepartment->setDeptID($dept_id);
			$MyDepartment->setDeptCode($dept_code);
			$MyDepartment->setDeptTitle($dept_title);
			$MyDepartment->setDeptName($dept_name);

			if($MyDepartment->AddDepartment()){
				$json_data = '{"result":"success"}';
			}
			else{ 
				$json_data = '{"result":"xxx"}'; 
			}
			echo $json_data;
			break;

		case 'delete_dept':
			include_once '../includes/class/Department.php';
            $Department = new Department();
            $json_data = "";
            $dept_id = $_POST['deptid'];
            if ($Department->DeleteDepartment($dept_id)) {
                $json_data = '{"result":"success"}';
            }
            else{
                $json_data = '{"result":"xxx"}';
            }
            echo $json_data;
			break;

		case 'SavePositionItem':
			include_once '../includes/class/Position.php';
			$MyPosition = new Position();
			
            $position_data[] = array();

			$position_id = $_POST['position_id'];
			$position_refnum = $_POST['positionRefNum'];
			$position_itemnum = $_POST['positionItemNum'];
            $position_title = $_POST['positionItemTitle'];
            $dept_id = $_POST['dept_id'];
			$salary_grade = $_POST['salaryGrade'];
			$position_type = $_POST['positionType'];
			$position_status = $_POST['positionStatus'];

            $position_data = [	'position_id' => $position_id,
								'position_refnum' => $position_refnum,
								'position_itemnum' => $position_itemnum,
                            	'position_title' => $position_title,
                            	'dept_id' => $dept_id,
								'salary_grade' => $salary_grade,
								'position_type' => $position_type,
								'position_status' => $position_status
                            ];
            
            $json_data = "";
			
			if($MyPosition->isPositionCurrentlyFilled($position_id)){
				$json_data = '{"result":"filled"}';
			}
			else{
				$MyPosition->setPositionItemData($position_data);
				if ($MyPosition->SavePositionItem()) {
					$json_data = '{"result":"success"}';
				}
				else {
					$json_data = '{"result":"xxx"}';
				}
			}		
            echo $json_data;

			break;
		
		case 'delete_position':
			include_once '../includes/class/Position.php';
			$MyPosition = new Position();
			$json_data = "";
			$position_id = $_POST['posid'];
			if ($MyPosition->DeletePosition($position_id)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'SaveSystemUser':
				include_once('../includes/class/Admin.php');

					$UserAdmin = new Admin();
	
					$user_id = $_POST["userID"];
					$employee_id = $_POST["employeeID"];
					$roles = $_POST["UserRoles"];
					
					//new selected roles for user
					$added_roles = $roles;
	
					//Save added roles to existing roles for user
					if($curr_roles = $UserAdmin->getUserRoles($user_id)){
						$user_roles = array();
						foreach ($curr_roles as $value) {
							$user_roles[] = $value['roleID'];
						}
						//current existing roles of user
						$curr_roles = $user_roles;
	
						$new_roles = array_diff($added_roles, $curr_roles);
	
						foreach ($new_roles as $value) {
							include_once('../includes/class/Role.php');
							$UserRole = new Role();
							$UserRole->AddUserRoles($user_id, $value);		
						}
					}
					//save new added roles
					else{
						foreach ($added_roles as $value) {
							include_once('../includes/class/Role.php');
							$UserRole = new Role();
							$UserRole->UpdateUserRole($user_id, $value);		
						}
					}
					$UserAdmin->setUserID($user_id);
											
						if($UserAdmin->AddAdminUser($employee_id)){
							header("Location: user_management.php?add=1");
							return true;
	
						}
						else{ 
							header("Location: user_management.php?add=0");
							return false;
						}
				break;
	
		case 'delete_user':
					include_once('../includes/class/Admin.php');
					$DeletedUser = new Admin();
	
					$Data =  isset($_POST["Data"])?$_POST["Data"]:"" ;
					$json_data = '';
					if($DeletedUser->RemoveAdminUser($Data)){
						$json_data = '{"result":"deleted"}';
					}
					else{
						$json_data = '{"result":"xxx"}';
					}
					echo $json_data;
				break;

		case 'SaveEarningConfig':
			include_once '../includes/class/Earning.php';
			//extract($_POST);
			$config_earning_id = $_POST['config_earning_id'];
			$earning_acct_code = $_POST['earning_acct_code'];
    		$earning_code = $_POST['earning_code'];
    		$earning_title = $_POST['earning_title'];

    		$ConfigEarning = new Earning();

			$ConfigEarning->setConfigEarningID($config_earning_id);
    		$ConfigEarning->setEarningAcctCode($earning_acct_code);
			$ConfigEarning->setEarningCode($earning_code);
			$ConfigEarning->setEarningTitle($earning_title);

			if($ConfigEarning->AddEarningConfig()){
				$json_data = '{"result":"success"}';
			}
			else{header("Location: settings_earnings.php?addearn=0");
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'delete_earning_config':
			include_once '../includes/class/Earning.php';
			$EarningConfig = new Earning();
	
			$json_data = "";
			$config_earning_id = $_POST['config_earning_id'];
			if ($EarningConfig->DeleteEarningConfig($config_earning_id)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'SaveDeductionConfig':
			include_once '../includes/class/Deduction.php';
			//extract($_POST);
			$config_deduction_id = $_POST['config_deduction_id'];
			$deduction_type = $_POST['deduction_type'];
			$deduction_acct_code = $_POST['deduction_acct_code'];
    		$deduction_code = $_POST['deduction_code'];
    		$deduction_title = $_POST['deduction_title'];
			$is_employee_share = $_POST['is_employee_share'];
			$deduction_category = $_POST['deduction_category'];

    		$ConfigDeduction= new Deduction();

			$ConfigDeduction->setConfigDeductionID($config_deduction_id);
			$ConfigDeduction->setDeductType($deduction_type);
    		$ConfigDeduction->setDeductAcctCode($deduction_acct_code);
			$ConfigDeduction->setDeductCode($deduction_code);
			$ConfigDeduction->setDeductTitle($deduction_title);
			$ConfigDeduction->setIsEmployeeShare($is_employee_share);
			$ConfigDeduction->setDeductCategory($deduction_category);

			if($ConfigDeduction->AddDeductionConfig()){
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'EditDeductionConfig':
			include_once '../includes/class/Deduction.php';
			//extract($_POST);
			$config_deduction_id = $_POST['config_deduction_id'];
			$deduction_type = $_POST['deduction_type'];
			$deduction_acct_code = $_POST['deduction_acct_code'];
    		$deduction_code = $_POST['deduction_code'];
    		$deduction_title = $_POST['deduction_title'];
			$is_employee_share = $_POST['is_employee_share'];
			$deduction_category = $_POST['deduction_category'];

    		$ConfigDeduction= new Deduction();

			$ConfigDeduction->setConfigDeductionID($config_deduction_id);
			$ConfigDeduction->setDeductType($deduction_type);
    		$ConfigDeduction->setDeductAcctCode($deduction_acct_code);
			$ConfigDeduction->setDeductCode($deduction_code);
			$ConfigDeduction->setDeductTitle($deduction_title);
			$ConfigDeduction->setIsEmployeeShare($is_employee_share);
			$ConfigDeduction->setDeductCategory($deduction_category);

			if($ConfigDeduction->EditDeductionConfig()){
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'delete_deduction_config':
			include_once '../includes/class/Deduction.php';
			$DeductionConfig = new Deduction();
	
			$json_data = "";
			$config_deduction_id = $_POST['config_deduction_id'];
			if ($DeductionConfig->DeleteDeductionConfig($config_deduction_id)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'set_pay_frequency':
			include_once '../includes/class/Payroll.php';
			$PayrollFrequency = new Payroll();

			$new_activeFreq_id = $_POST['pay_frequency'];
			$is_active = '1';

			if($curr_active_freq = $PayrollFrequency->GetCurrentActiveFrequency()){
				if(!empty($curr_active_freq) || $curr_active_freq !== null){                                       // see if there is a current active frequency
					$PayrollFrequency->UpdatePayFrequency($curr_active_freq['payroll_freq_id'],'0');       //set the current active=1 to inactive=0
				}        

				$PayrollFrequency->UpdatePayFrequency($new_activeFreq_id, $is_active);
			}

			header("Location: settings_payroll.php"); // Redirect back to page
			exit;

			break;

		case 'close_payroll_year':
			include_once '../includes/class/Payroll.php';
			$PayrollSetting = new Payroll();

			$year = $_POST['year_to_close'];

			if($PayrollSetting->ClosePayrollYear($year)){				//---Close the previous payroll year
				$PayrollSetting->generateAndInsertPayPeriodsToDB();		//---Generate new payroll periods for the current year
			}

			header("Location: settings_payroll.php");
			exit;
			break;
	}
}






?>