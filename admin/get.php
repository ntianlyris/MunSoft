<?php
if($action = isset($_REQUEST['action'])?$_REQUEST['action']:''){
    switch ($action) {
        case 'get_dept_details':
            include_once '../includes/class/Department.php';
            $MyDepartment = new Department();
    
            $dept_id = $_GET['deptid'];
    
            $dept_details = $MyDepartment->GetDepartmentDetails($dept_id);
            if ($dept_details) {
                echo json_encode($dept_details);
            }
            else {
                return false;
            }
            break;
        
        case 'get_position_details':
            include_once '../includes/class/Position.php';
            $MyPosition = new Position();
        
            $position_id = $_GET['position_id'];
        
            $position_details = $MyPosition->GetPositionDetails($position_id);
            if ($position_details) {
                echo json_encode($position_details);
            }
            else {
                return false;
            }
            break;

        case 'get_employee_details':
            include_once '../includes/class/Employee.php';
            $MyEmployee = new Employee();
            
            $employee_id = $_GET['employee_id'];
            
            $employee_details = $MyEmployee->GetEmployeeDetails($employee_id);
            if ($employee_details) {
                echo json_encode($employee_details);
            }
            else {
                return false;
            }
            break;

        case 'get_employee_user':
            include_once '../includes/class/Employee.php';
            $MyEmployee = new Employee();
                
            $employee_id = $_GET['employee_id'];
                
            $employee_user = $MyEmployee->GetEmployeeUser($employee_id);
            if ($employee_user) {
                    echo json_encode($employee_user);
            }
            else {
                return false;
            }
            break;
        case 'get_role_details':
            include_once '../includes/class/Role.php';
            $role_id = $_GET['role_id'];
            $role_perms = Role::getRolePermID($role_id);
            // Also need the role name
            $all_roles = Role::getRoles();
            $role_name = '';
            foreach($all_roles as $r){
                if($r['roleID'] == $role_id){
                    $role_name = $r['roleName'];
                    break;
                }
            }
            echo json_encode(['role_id' => $role_id, 'role_name' => $role_name, 'perms' => $role_perms]);
            break;
        
    }
}
?>