<?php

// ===== SECURITY: Check user role for edit/delete operations =====
include_once '../includes/class/Admin.php';
$Admin = new Admin();
$current_user_id = $Admin->getSessionUID();
$user_roles = $Admin->initRoles($current_user_id);
$current_user_role = '';
foreach ($user_roles as $role) {
    $current_user_role = $role >= 0 ? array_key_first($user_roles) : '';
    break;
}

// Function to check if user has permission for data modification
function CheckModifyPermission($action = 'modify') {
    global $current_user_role;
    
    // Employee users cannot modify/delete data
    if ($current_user_role === 'Employee') {
        $json_data = '{"result":"error", "message":"Access Denied: You do not have permission to modify data."}';
        http_response_code(403);
        return false;
    }
    return true;
}

if (isset($_POST['submit'])){
	$setting_name = $_POST['submit'];

	switch ($setting_name) {
		case 'SaveDepartment':
			// Security check for modify operations
			if (!CheckModifyPermission('modify')) {
				echo '{"result":"error", "message":"Access Denied"}';
				exit;
			}
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
			// Security check for delete operations
			if (!CheckModifyPermission('delete')) {
				echo '{"result":"error", "message":"Access Denied"}';
				exit;
			}
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

		case 'AddUserRole':

				include_once('../includes/class/Role.php');

					$role_name = $_POST["role_name"];
					$role_perms = $_POST["role_perms"];

					//new selected permissions for role
					$added_perms = $role_perms;

					//get the roleid for tthe role name
					if($role_id = Role::insertRole($role_name)){
						//existing permissions of role
						if($current_perms = Role::getRolePermID($role_id)){

							//compare the selected and existing perms and get the non-existing perm
							$new_perms = array_diff($added_perms, $current_perms);
							foreach ($new_perms as $value) {
								include_once('../includes/class/PrivilegedUser.php');
								if(PrivilegedUser::insertPerm($role_id, $value)){
									$msg = "New permissions to existing Role have been added.";
								}
								else{ $msg = "Failed to add permissions."; }
							}
						}
						//if role do not contains perms, insert all added permissions
						else{
							foreach ($added_perms as $value) {
								include_once('../includes/class/PrivilegedUser.php');
								if(PrivilegedUser::insertPerm($role_id, $value)){
									$msg = "New Role with permissions have been added.";
								}
								else{ $msg = "Failed to add permissions to Role."; }
							}
						}

						echo $msg;
					}
					else{ echo "Failed to add Role."; }

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