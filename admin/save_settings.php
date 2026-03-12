<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
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
function CheckModifyPermission($action = 'modify')
{
	global $Admin;

	if ($action === 'delete') {
		// Deletion remains an administrative privilege
		if (!$Admin->hasPrivilege('Manage System')) {
			return false;
		}
	} else {
		// Modification requires at least Update Data permission
		if (!$Admin->hasPrivilege('Update Data')) {
			return false;
		}
	}
	return true;
}

if (isset($_POST['submit'])) {
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

			if ($MyDepartment->AddDepartment()) {
				$json_data = '{"result":"success"}';
			} else {
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
			} else {
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'SavePositionItem':
			// Security check for modify operations
			if (!CheckModifyPermission('modify')) {
				echo '{"result":"error", "message":"Access Denied"}';
				exit;
			}
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

			$position_data = [
				'position_refnum' => $position_refnum,
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
			} else {
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;

			break;

		case 'delete_position':
			// Security check for delete operations
			if (!CheckModifyPermission('delete')) {
				echo '{"result":"error", "message":"Access Denied"}';
				exit;
			}
			include_once '../includes/class/Position.php';
			$MyPosition = new Position();
			$json_data = "";
			$position_id = $_POST['posid'];
			if ($MyPosition->DeletePosition($position_id)) {
				$json_data = '{"result":"success"}';
			} else {
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'SaveSystemUser':
			// Security check: Only 'Manage System' can save users
			if (!$Admin->hasPrivilege('Manage System')) {
				header("Location: user_management.php?access=denied");
				exit;
			}
			include_once('../includes/class/Admin.php');
			include_once('../includes/class/Role.php'); // Include Role class once

			$UserAdmin = new Admin();

			$user_id = $_POST["userID"];
			$employee_id = $_POST["employeeID"];
			$roles = $_POST["UserRoles"];

			// Synchronize user roles: Delete existing and add new selection
			Role::deleteUserRoles($user_id);

			if (!empty($roles)) { // Use $roles directly as it's the new selection
				foreach ($roles as $value) {
					Role::AddUserRoles($user_id, $value);
				}
			}
			$UserAdmin->setUserID($user_id);

			if ($UserAdmin->AddAdminUser($employee_id)) {
				header("Location: user_management.php?add=1");
				return true;

			} else {
				header("Location: user_management.php?add=0");
				return false;
			}
			break;

		case 'AddUserRole':
			// Security check: Only 'Manage System' can modify roles
			if (!$Admin->hasPrivilege('Manage System')) {
				header("Location: user_management.php?access=denied");
				exit;
			}
			include_once('../includes/class/Role.php');

			$role_name = $_POST["role_name"];
			$role_perms = isset($_POST["role_perms"]) ? $_POST["role_perms"] : null;
			$role_id_input = isset($_POST["role_id"]) ? $_POST["role_id"] : '';

			//new selected permissions for role
			$added_perms = $role_perms;

			// If role_id is provided, we are editing
			if ($role_id_input != '') {
				$role_id = $role_id_input;
				Role::updateRoleName($role_id, $role_name);
			} else {
				// Otherwise we are adding (or fetching ID by name)
				$role_id = Role::insertRole($role_name);
			}

			if ($role_id) {

				// Synchronize permissions: Clear existing and add new selection
				Role::deleteRolePerms($role_id);

				if ($added_perms) {
					foreach ($added_perms as $value) {
						include_once('../includes/class/PrivilegedUser.php');
						PrivilegedUser::insertPerm($role_id, $value);
					}
				}

				header("Location: user_management.php?add_role=1");
			} else {
				header("Location: user_management.php?add_role=0");
			}

			break;

		case 'delete_user':
			// Security check: Only 'Manage System' can delete users
			if (!$Admin->hasPrivilege('Manage System')) {
				echo '{"result":"error", "message":"Access Denied"}';
				exit;
			}
			include_once('../includes/class/Admin.php');
			$DeletedUser = new Admin();

			$Data = isset($_POST["Data"]) ? $_POST["Data"] : "";
			$json_data = '';
			if ($DeletedUser->RemoveAdminUser($Data)) {
				$json_data = '{"result":"deleted"}';
			} else {
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;

		case 'delete_role':
			// Security check: Only 'Manage System' can delete roles
			if (!$Admin->hasPrivilege('Manage System')) {
				echo '{"result":"error", "message":"Access Denied"}';
				exit;
			}
			include_once('../includes/class/Role.php');
			$role_id = $_POST["role_id"];
			$json_data = '';
			if (Role::deleteRole($role_id)) {
				$json_data = '{"result":"deleted"}';
			} else {
				$json_data = '{"result":"xxx"}';
			}
			echo $json_data;
			break;
	}
}






?>