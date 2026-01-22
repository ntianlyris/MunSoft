<?php

if (isset($_POST['action'])){
	$action = $_POST['action'];

	switch ($action) {
		case 'SaveEmployee':
			include_once '../includes/class/Employee.php';
			$MyEmployee = new Employee();

			$employee_data[] = array();
			$json_data = "";

			$employee_id = $_POST['employee_id'];
			$employee_id_num = $_POST['employee_id_num'];
    		$firstname = $_POST['firstname'];
			$middlename = $_POST['middlename'];
    		$lastname = $_POST['lastname'];
			$extension = $_POST['extension'];
			$birthdate = $_POST['birthdate'];
			$gender = $_POST['gender'];
			$civil_status = $_POST['civil_status'];
			$address = $_POST['address'];
			$prof_expertise = $_POST['prof_expertise'];
			$date_hired = $_POST['date_hired'];
			$employee_status = $_POST['employee_status'];

    		$employee_data = ['employee_id_num' => $employee_id_num,
                            'firstname' => $firstname,
                            'middlename' => $middlename,
							'lastname' => $lastname,
							'extension' => $extension,
							'birthdate' => $birthdate,
							'gender' => $gender,
							'civil_status' => $civil_status,
							'address' => $address,
							'prof_expertise' => $prof_expertise,
							'date_hired' => $date_hired,
							'employee_status' => $employee_status
                            ];
			
			$MyEmployee->setEmployeeData($employee_data);
			if ($employee_id != "") {
				if($MyEmployee->UpdateEmployee($employee_id)){
					$json_data = '{"result":"success"}';
				}
				else{ 
					$json_data = '{"result":"xxx"}'; 
				}
			}
			else{
				if($MyEmployee->SaveEmployee()){
					$json_data = '{"result":"success"}';
				}
				else{ 
					$json_data = '{"result":"xxx"}'; 
				}
			}
			
			echo $json_data;
			break;

		case 'delete_employee':
			include_once '../includes/class/Employee.php';
			include_once '../includes/class/Employment.php';
			$MyEmployee = new Employee();
			$MyEmployment = new Employment();

			$json_data = "";
			$employee_id = $_POST['employee_id'];
			if ($MyEmployee->DeleteEmployee($employee_id) && $MyEmployment->DeleteEmployeeEmployments($employee_id)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'SaveUserEmployee':
			include_once '../includes/class/Employee.php';
			include_once '../includes/class/Role.php';
			$MyEmployee = new Employee();
			$UserEmployeeRole = new Role();

			$json_data = "";

			# get userid and employee_id
			$user_id = $_POST['user_id'];
			$employee_id = $_POST['employee_id'];
			$role_id = 3;	// role id for employee role

			# update userid table of employees tbl with the user id given # update the role id in roles tbl using the user id given
			if($MyEmployee->UpdateUserIDOfEmployee($employee_id, $user_id)){
				if($UserEmployeeRole->UpdateUserRole($user_id, $role_id)){
					$json_data = '{"result":"success"}';
				}
				else{
					$json_data = '{"result":"xxx"}'; 
				}
			}
			else{
				$json_data = '{"result":"xxx"}'; 
			}
			echo $json_data;
			break;

		case 'UnlinkUserEmployee':
			include_once '../includes/class/Employee.php';
			include_once '../includes/class/Role.php';
			$MyEmployee = new Employee();
			$UserEmployeeRole = new Role();

			$user_id = $_POST['user_id'];
			$employee_id = $_POST['employee_id'];
			$role_id = 0;	// default role id for no role

			# update userid table of employees tbl with the user id given # update the role id in roles tbl using the user id given
			if($MyEmployee->UpdateUserIDOfEmployee($employee_id, "")){
				if($UserEmployeeRole->UpdateUserRole($user_id, $role_id)){
					$json_data = '{"result":"success"}';
				}
				else{
					$json_data = '{"result":"xxx"}'; 
				}
			}
			else{
				$json_data = '{"result":"xxx"}'; 
			}
			echo $json_data;
			break;

		case 'SaveEmployment':
			include_once '../includes/class/Employment.php';
			include_once '../includes/class/Position.php';

			$MyEmployment = new Employment();
			$Position = new Position();
	
			$employment_data[] = array();
			$json_data = "";
			$curr_position_status = "";
			$employment_status = "";
	
			$employee_id = $_POST['employee_id'];
			$employment_refnum = $_POST['employment_refnum'];
			$employment_type = $_POST['employment_type'];
			$employment_start = $_POST['employment_start'];
			$employment_end = $_POST['employment_end'];
			$position = $_POST['position'];
			$department_assigned = $_POST['department_assigned'];
			$designation = $_POST['designation'];
			$work_nature = $_POST['work_nature'];
			$work_specifics = $_POST['work_specifics'];
			$employment_rate = $_POST['employment_rate'];
			$employment_particulars = $_POST['employment_particulars'];
			$employment_status = '1';
	
			$employment_data = ['employee_id' => $employee_id,
							'employment_refnum' => $employment_refnum,
							'employment_type' => $employment_type,
							'employment_start' => $employment_start,
							'employment_end' => $employment_end,
							'position' => $position,
							'department_assigned' => $department_assigned,
							'designation' => $designation,
							'work_nature' => $work_nature,
							'work_specifics' => $work_specifics,
							'employment_rate' => $employment_rate,
							'employment_particulars' => $employment_particulars,
							'employment_status' => $employment_status
							];
			
			$MyEmployment->setEmploymentData($employment_data);

			//---Checks whether position variable is not empty means its a regular/casual position
			if($position){
				$position_id = $position;
				$curr_position_status = $Position->GetPositionDetails($position_id)['position_status'];

				//---Checks whether employee has already an active occupied position
				if($MyEmployment->hasCurrentActiveEmployment($employee_id)){
					$json_data = '{"result":"has_active_emp"}';
				}
				//---No position currently occupied
				else{
					//---Checks whether current position is vacant == 0
					if($curr_position_status == 0){
						//---Save employment then update position status to filled
						if($MyEmployment->SaveEmployment() && $Position->UpdatePositionStatus($position_id, '1')){
							$json_data = '{"result":"success"}';
						}
						else{ 
							$json_data = '{"result":"xxx"}'; 
						}
					}
					//---If current position is already filled = 1
					else{
						//---Handle error
						$json_data = '{"result":"position_filled"}'; 
					}
				}
			}
			//---If in the case of JOs,COS and contractual
			else{
				if($MyEmployment->SaveEmployment()){
					$json_data = '{"result":"success"}';
				}
				else{ 
					$json_data = '{"result":"xxx"}'; 
				}
			}
			echo $json_data;
			break;

		case 'EditEmployment':
			include_once '../includes/class/Employment.php';
			include_once '../includes/class/Position.php';

			$MyEmployment = new Employment();
			$Position = new Position();

			$employment_data[] = array();
			$json_data = "";
			$curr_position_status = "";
			$employment_status = "1";
			$end_of_employment = false;
	
			$employee_id = $_POST['employee_id'];
			$employment_id = $_POST['employment_id'];
			$employment_refnum = $_POST['employment_refnum'];
			$employment_type = $_POST['employment_type'];
			$employment_start = $_POST['employment_start'];
			$employment_end = $_POST['employment_end'];
			$position = $_POST['position'];
			$department_assigned = $_POST['department_assigned'];
			$designation = $_POST['designation'];
			$work_nature = $_POST['work_nature'];
			$work_specifics = $_POST['work_specifics'];
			$employment_particulars = $_POST['employment_particulars'];
			$employment_rate = $_POST['employment_rate'];
	
			$employment_data = ['employee_id' => $employee_id,
							'employment_id' => $employment_id,
							'employment_refnum' => $employment_refnum,
							'employment_type' => $employment_type,
							'employment_start' => $employment_start,
							'employment_end' => $employment_end,
							'position' => $position,
							'department_assigned' => $department_assigned,
							'designation' => $designation,
							'work_nature' => $work_nature,
							'work_specifics' => $work_specifics,
							'employment_particulars' => $employment_particulars,
							'employment_rate' => $employment_rate
							];
			
			$MyEmployment->setEmploymentData($employment_data);

			$end_of_employment = ($employment_end !== '') ? true : false;		//--------checks if employment end is set
			
			//---Checks whether position variable is not empty means its a regular/casual position
			if($position){
				$position_id = $position;

				if ($end_of_employment) {		//------checks if employment end is set, set status to 0 = inactive if employment end is set
					$employment_status = '0';
					
					if ($MyEmployment->EditEmployment() && $MyEmployment->UpdateEmploymentStatus($employment_id, $employment_status)) {
						$Position->UpdatePositionStatus($position_id, '0');
						$json_data = '{"result":"success"}';
					}
					else{
						$json_data = '{"result":"xxx"}'; 
					}
				}
				else{
					if($MyEmployment->EditEmployment() && $MyEmployment->UpdateEmploymentStatus($employment_id, $employment_status)){
						$json_data = '{"result":"success"}';
					}
					else{ 
						$json_data = '{"result":"xxx"}'; 
					}
				}
			}
			//---If in the case of JOs,COS and contractual
			else{
				if($MyEmployment->EditEmployment()){
					$json_data = '{"result":"success"}';
				}
				else{ 
					$json_data = '{"result":"xxx"}'; 
				}
			}
			echo $json_data;
			break;

		case 'delete_employment':
			include_once '../includes/class/Employment.php';
			include_once '../includes/class/Position.php';
			$MyEmployment = new Employment();
			$Position = new Position();
	
			$json_data = "";
			$employment_id = $_POST['employment_id'];
			$position_id = $_POST['position_id'];
			if ($MyEmployment->DeleteEmployment($employment_id) && $Position->UpdatePositionStatus($position_id, '0')) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'save_gov_numbers':
			include_once '../includes/class/Employee.php';
			$MyEmployee = new Employee();
			$json_data = "";
			$employee_id = $_POST['employee_id'];
			$gov_numbers = [
				'bir_tin' => $_POST['bir_tin'],
				'gsis_bp' => $_POST['gsis_bp'],
				'pagibig_mid' => $_POST['pagibig_mid'],
				'philhealth_no' => $_POST['philhealth_no'],
				'sss_no' => $_POST['sss_no']
			];

			if ($MyEmployee->UpdateEmployeeGovNumbers($employee_id, $gov_numbers)) {
				$json_data = '{"result":"success"}';
			}
			else{
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;
	}
}






?>