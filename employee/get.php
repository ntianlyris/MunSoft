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

        case 'get_employment_details':
            include_once '../includes/class/Employment.php';
            $MyEmployment = new Employment();
            
            $employment_id = $_GET['employment_id'];
            
            $employment_details = $MyEmployment->GetEmployeeEmploymentDetails($employment_id);
            if ($employment_details) {
                echo json_encode($employment_details);
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

        case 'get_earning_config_details':
            include_once '../includes/class/Earning.php';
            $EarningConfig = new Earning();
            
            $config_earning_id = $_GET['config_earning_id'];
            
            $config_earning_details = $EarningConfig->getConfigEarningByID($config_earning_id);
            if ($config_earning_details) {
                echo json_encode($config_earning_details);
            }
            else {
                return false;
            }
            break;

        case 'get_deduction_config_details':
            include_once '../includes/class/Deduction.php';
            $DeductionConfig = new Deduction();
            
            $config_deduction_id = $_GET['config_deduction_id'];
            
            $config_deduction_details = $DeductionConfig->getConfigDeductionByID($config_deduction_id);
            if ($config_deduction_details) {
                echo json_encode($config_deduction_details);
            }
            else {
                return false;
            }
            break;

        case 'get_employee_earnings':
            include_once '../includes/class/Employee.php';
            include_once '../includes/class/Earning.php';

            $Employee = new Employee();
            $Earning= new Earning();

            $employee_id = $_POST['employee_id'];
            
            $employee_details = $Employee->GetEmployeeDetails($employee_id);

            $full_name = $employee_details['lastname'].', '.$employee_details['firstname'];
            $earnings = $Earning->GetAllEarningsByEmployee($employee_id); // includes inactive ones
        
            echo json_encode([
                'status' => 'success',
                'data' => $earnings,
                'full_name' => $full_name
            ]);
            exit;
            break;
        
        case 'get_employee_deductions':
            include_once '../includes/class/Employee.php';
            include_once '../includes/class/Deduction.php';

            $Employee = new Employee();
            $Deduction= new Deduction();

            $employee_id = $_POST['employee_id'];
            
            $employee_details = $Employee->GetEmployeeDetails($employee_id);

            $full_name = $employee_details['lastname'].', '.$employee_details['firstname'];
            $deductions = $Deduction->GetAllDeductionsByEmployee($employee_id); // includes inactive ones
        
            echo json_encode([
                'status' => 'success',
                'data' => $deductions,
                'full_name' => $full_name
            ]);
            exit;
            break;

        case 'get_gov_numbers':
            include_once '../includes/class/Employee.php';
            $Employee = new Employee();

            $employee_id = $_GET['employee_id'];
            $gov_numbers = $Employee->GetEmployeeGovNumbers($employee_id);
            if ($gov_numbers) {
                echo json_encode($gov_numbers);
            }
            else {
                return false;
            }
            break;
    }
}
?>