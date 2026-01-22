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

			$position_refnum = $_POST['positionRefNum'];
			$position_itemnum = $_POST['positionItemNum'];
            $position_title = $_POST['positionItemTitle'];
            $dept_id = $_POST['dept_id'];
			$salary_grade = $_POST['salaryGrade'];
			$position_type = $_POST['positionType'];
			$position_status = $_POST['positionStatus'];

            $position_data = ['position_refnum' => $position_refnum,
								'position_itemnum' => $position_itemnum,
                            	'position_title' => $position_title,
                            	'dept_id' => $dept_id,
								'salary_grade' => $salary_grade,
								'position_type' => $position_type,
								'position_status' => $position_status
                            ];
            
            $json_data = "";

            $MyPosition->setPositionItemData($position_data);
            if ($MyPosition->SavePositionItem()) {
                $json_data = '{"result":"success"}';
            }
            else {
                $json_data = '{"result":"xxx"}';
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
	}
}






?>