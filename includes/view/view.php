<?php 
    include_once '../includes/view/functions.php';
    include_once('../includes/class/Admin.php');    
    include_once('../includes/class/Role.php'); 

    $MyAdmin = new Admin();
    $user_id = $MyAdmin->getSessionUID();
    $roles = $MyAdmin->initRoles($user_id);
    $role = "";
    
    foreach ($roles as $key => $value) {
        $role = $key;
        foreach ($value as $k => $v) {
          $perms[] = $v;
        }
    }
    
    $admin_perms = $perms;
    $manage_system = false;
    $update_data = false;

    if(in_array('Manage System', $admin_perms, true)){ $manage_system = true; }
    if(in_array('Update Data', $admin_perms, true)){ $update_data = true; }

##-----Render View of Sidebar links according to user permissions-----##
function ViewSideBarLink($link_name){
    $user_id = $GLOBALS['user_id'];                                 //access the global variable $user_id
    $user_role = $GLOBALS['role'];                                 //access the global variable $role
    $manage_system = $GLOBALS['manage_system'];         //access the global variable $manage_system
    $update_data = $GLOBALS['update_data']; 
    $link_text = '';
    switch ($link_name) {
        case 'users':
                if($user_role == "Administrator"){
                    $link_text = '<li class="nav-item">
                                    <a href="users.php" class="nav-link" id="users">
                                    <i class="nav-icon fas fa-user-lock"></i>
                                    <p>
                                        User Accounts
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'profile':
                if($user_role == "Employee"){
                    include_once '../includes/class/Employee.php';
                    $MyEmployee = new Employee();
                    $employee_id = "";
                    $employee_id = $MyEmployee->getEmployeeIDByUserId($user_id);
                    $link_text = '<li class="nav-item">
                                    <a href="profile.php?emp_id='.$employee_id.'" class="nav-link" id="profile">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>
                                        Profile
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'leave_application':
                if($user_role == "Employee"){
                    include_once('../includes/class/Employee.php'); 
                    $MyEmployee = new Employee();
                    $employee_id = $MyEmployee->getEmployeeIDByUserId($user_id);
                    $link_text = '<li class="nav-item">
                                    <a href="leave_application.php" class="nav-link" id="leave_application">
                                    <i class="nav-icon fas fa-calendar-plus"></i>
                                    <p>
                                        Leave Application
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'employees':
                if($manage_system){
                    $link_text = '<li class="nav-item">
                                    <a href="employees.php" class="nav-link" id="employees">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        Employees
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'departments':
                if($manage_system){        //if($user_role == "Administrator" || $user_role == "HR")
                    $link_text = '<li class="nav-item">
                                            <a href="settings_departments.php" class="nav-link" id="settings_departments">
                                            <i class="nav-icon fas fa-cog"></i>
                                            <p>
                                                Departments
                                            </p>
                                            </a>
                                        </li>';
                }
            break;
        case 'positions':
                if($manage_system){
                    $link_text = '<li class="nav-item">
                                            <a href="settings_positions.php" class="nav-link" id="settings_positions">
                                            <i class="fas fa-cog nav-icon"></i>
                                            <p>
                                                Position Items
                                                </p>
                                            </a>
                                        </li>';
                }
            break;
        case 'admin_settings':
                if($user_role == "Administrator"){
                    $link_text = '<li class="nav-item">
                                    <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-cogs"></i>
                                    <p>
                                        Settings
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="user_management.php" class="nav-link" id="user_management">
                                            <i class="nav-icon fas fa-user-check"></i>
                                            <p>
                                                User Management
                                            </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>';
                }
            break;
        case 'employee_earnings':
                if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="employee_earnings.php" class="nav-link" id="employee_earnings">
                                    <i class="nav-icon fas fa-money-bill-wave"></i>
                                    <p>
                                        Employee Earnings
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'employee_deductions':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="employee_deductions.php" class="nav-link" id="employee_deductions">
                                    <i class="nav-icon fas fa-user-minus"></i>
                                    <p>
                                        Employee Deductions
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'employee_govshares':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="employee_govshares.php" class="nav-link" id="employee_govshares">
                                    <i class="nav-icon fas fa-user-check"></i>
                                    <p>
                                        Government Shares
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'config_earnings':
                if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="config_earnings.php" class="nav-link" id="config_earnings">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Earnings
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        case 'config_deductions':
                if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="config_deductions.php" class="nav-link" id="config_deductions">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Deductions
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'payrolls':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="payrolls.php" class="nav-link" id="payrolls">
                                    <i class="nav-icon fas fa-file-invoice"></i>
                                    <p>
                                        Payrolls
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'payroll_records':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="payroll_records.php" class="nav-link" id="payroll_records">
                                    <i class="nav-icon fas fa-folder-open"></i>
                                    <p>
                                        Payroll Records
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'remittance':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="remittance.php" class="nav-link" id="remittance">
                                    <i class="nav-icon fas fa-paper-plane"></i>
                                    <p>
                                        Remittance
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'govshares':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="govshares.php" class="nav-link" id="govshares">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Government Shares
                                    </p>
                                    </a>
                                </li>';
                }
            break;
        
        case 'journal_entry':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="journal_entry.php" class="nav-link" id="journal_entry">
                                    <i class="nav-icon fas fa-file-invoice"></i>
                                    <p>
                                        Journal Entry
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'payslip':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="payslip.php" class="nav-link" id="payslip">
                                    <i class="nav-icon fas fa-file-invoice"></i>
                                    <p>
                                        Payslip
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'payroll_settings':
            if($user_role == "Payroll Master"){
                    $link_text = '<li class="nav-item">
                                    <a href="settings_payroll.php" class="nav-link" id="settings_payroll">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Payroll Settings
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'manage_leave_credits':
            if($user_role == "HR" || $user_role == "Administrator"){
                    $link_text = '<li class="nav-item">
                                    <a href="manage_leave_credits.php" class="nav-link" id="manage_leave_credits">
                                    <i class="nav-icon fas fa-piggy-bank"></i>
                                    <p>
                                        Leave Credits
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'leave_applications':
            if($user_role == "HR" || $user_role == "Administrator"){
                    $link_text = '<li class="nav-item">
                                    <a href="leave_applications.php" class="nav-link" id="leave_applications">
                                    <i class="nav-icon fas fa-calendar-minus"></i>
                                    <p>
                                        Leave Applications
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'config_leave_types':
            if($user_role == "HR" || $user_role == "Administrator"){
                    $link_text = '<li class="nav-item">
                                    <a href="config_leave_types.php" class="nav-link" id="config_leave_types">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Leave Types
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'change_password':
            if($update_data){
                    $link_text = '<li class="nav-item">
                                    <a href="#" class="nav-link" id="change_password">
                                    <i class="nav-icon fas fa-lock"></i>
                                    <p>
                                        Change Password
                                    </p>
                                    </a>
                                </li>';
                }
            break;

        case 'signatories':
            if($user_role == "Payroll Master" || $user_role == "HR" || $manage_system){
                    $link_text = '<li class="nav-item">
                                    <a href="signatories.php" class="nav-link" id="signatories">
                                        <i class="nav-icon fas fa-signature"></i>
                                        <p>
                                            Signatories
                                        </p>
                                    </a>
                                </li>';
                }
            break;
    }
    return $link_text;
}

##-----Render View of Tables-----##

function ViewDepartments(){
    include_once('../includes/class/Department.php');
    $MyDepartment = new Department();
    $departments = $MyDepartment->GetDepartments();

    if($departments != null){
        $count = 0;
        foreach($departments as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['dept_code'] . '</td>
                    <td>' . $value['dept_title'] . '</td>
                    <td>' . $value['dept_name'] . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" onclick="GetDepartmentDetails('.$value["dept_id"].')" data-toggle="modal" data-target="#department_modal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="DeleteDepartment('.$value["dept_id"].')" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewPositions(){
    include_once('../includes/class/Position.php');
    $MyPosition = new Position();
    $positions = $MyPosition->GetPositions();

    if($positions != null){
        $count = 0;
        foreach($positions as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['position_refnum'] . '</td>
                    <td>' . $value['position_itemnum'] . '</td>
                    <td>' . $value['position_title'] . '</td>
                    <td>' . $value['salary_grade'] . '</td>
                    <td>' . $MyPosition->returnPositionType($value['position_type']) . '</td>
                    <td>' . $value['dept_name'] . '</td>
                    <td>' . $MyPosition->returnPositionStatus($value['position_status']) . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" onclick="GetPositionDetails('.$value["position_id"].')" data-toggle="modal" data-target="#position_modal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="DeletePosition('.$value["position_id"].')" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewEmployees(){
    include_once('../includes/class/Employee.php');
    $MyEmployee = new Employee();
    $employees = $MyEmployee->GetEmployees();

    if($employees != null){
        $count = 0;
        foreach($employees as $key => $value) {
            $count++;
            echo '<tr class="clickable-row" data-href="employee_profile.php?emp_id='.$value["employee_id"].'">
                    <td>' . $count . '</td>
                    <td>' . $value['employee_id_num'] . '</td>
                    <td>' . $value['lastname'] . ", " . $value['firstname'] . " " . $value['extension'] . " " . $value['middlename'] . '</td>
                    <td>' . OutputShortDate($value['birthdate']) . '</td>
                    <td>' . $value['gender'] . '</td>
                    <td>' . $value['civil_status'] . '</td>
                    <td>' . $value['address'] . '</td>
                </tr>';
        }   
    }
}

function ViewEmployeeEmployments($employee_id){
    include_once('../includes/class/Employment.php');
    include_once('../includes/class/Department.php');

    $MyEmployment = new Employment();
    $MyDepartment = new Department();

    $employee_employments = $MyEmployment->GetEmployeeEmployments($employee_id);
    $employment_end = "";
    $department_assigned = "";

    if($employee_employments != null){
        $count = 0;
        foreach($employee_employments as $key => $value) {
            $count++;
            if($value['employment_end'] != "0000-00-00"){
                $employment_end = $value['employment_end'];
            }

            $department_assigned = $MyDepartment->GetDepartmentDetails($value['dept_assigned'])['dept_title'];
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['employment_refnum'] . '</td>
                    <td>' . $value['position_title'] . '</td>
                    <td>' . $value['employment_type'] . '</td>
                    <td>' . OutputShortDate($value['employment_start']) . '</td>
                    <td>' . OutputShortDate($employment_end) . '</td>
                    <td>' . $value['dept_title'] . '</td>
                    <td>' . $department_assigned . '</td>
                    <td>' . $value['designation'] . '</td>
                    <td>' . $value['employment_particulars'] . '</td>
                    <td>' . OutputMoney($value['rate']) . '</td>
                    <td>' . ($value['employment_status'] == 1 ? 'Active' : 'Inactive') . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm" onclick="GetEmploymentDetails('.$value["employee_id"].','.$value["employment_id"].')" data-toggle="modal" data-target="#employment_modal">
                                <span data-toggle="tooltip" title="Edit" data-placement="bottom"><i class="fa fa-edit"></i></span>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="DeleteEmployment('.$value["employment_id"].','.$value["position_id"].')" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewUserEmployees(){
    include_once('../includes/class/Employee.php');
    $MyEmployee = new Employee();
    $employee_users = $MyEmployee->GetEmployeeDataUsers();
    
    if($employee_users != null){
        $count = 0;
        foreach($employee_users as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['username'] . '</td>
                    <td>' . $value['mobile'] . '</td>
                    <td><a href="employee_profile.php?emp_id='.$value['employee_id'].'">' . $value['firstname'] . " " . $value['middlename'] . " " . $value['lastname'] . " " . $value['extension'] . '</a></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm" onclick="UnlinkUser('.$value['employee_id'].','.$value['userID'].')" data-toggle="tooltip" title="Unlink User" data-placement="bottom">
                                <i class="fa fa-user-minus"></i> Unlink
                            </button>
                            <button class="btn btn-success btn-sm" onclick="EditUser('.$value['userID'].')" data-toggle="tooltip" title="Edit" data-placement="bottom">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}


##-----For Payroll-----##

function ViewPayrollMasterlistEmployees(){
    include_once('../includes/class/Payroll.php');
    $Payroll = new Payroll();
    $employees = $Payroll->GetEmployeeListForPayroll();
    
    if($employees != null){
        $count = 0;
        foreach($employees as $employee) {
            $count++;
            // Fixed syntax error in checkbox data-id and checked status
            $checked = $employee['include_in_payroll'] == '1' ? 'checked' : '';
            
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . htmlspecialchars($employee['employee_id_num']) . '</td>
                    <td>' . htmlspecialchars($employee['full_name']) . '</td>
                    <td>' . htmlspecialchars($employee['dept_title']) . '</td>
                    <td>' . htmlspecialchars($employee['position_title']) . '</td>
                    <td class="text-center">
                        <div class="form-check mx-auto text-success">
                            <input type="checkbox" 
                               class="form-check-input toggleIncludePayroll border-success" 
                               data-id="'.$employee['employee_id'].'"
                               '.$checked.' style="width:20px; height:20px; accent-color: #28a745;">
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewEarningConfigs(){
    include_once('../includes/class/Earning.php');
    $EarningConfig = new Earning();
    $earning_configs = $EarningConfig->GetEarningConfigs();

    if($earning_configs != null){
        $count = 0;
        foreach($earning_configs as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['earning_acct_code'] . '</td>
                    <td>' . $value['earning_code'] . '</td>
                    <td>' . $value['earning_title'] . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" onclick="GetEarningConfigDetails('.$value["config_earning_id"].')" data-toggle="modal" data-target="#config_earnings_modal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="DeleteEarningConfig('.$value["config_earning_id"].')" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewDeductionConfigs(){
    include_once('../includes/class/Deduction.php');
    $DeductionConfig = new Deduction();
    $deduction_configs = $DeductionConfig->GetDeductionConfigs();

    if($deduction_configs != null){
        $count = 0;
        foreach($deduction_configs as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['deduction_type_code'] . '</td>
                    <td>' . $value['deduct_acct_code'] . '</td>
                    <td>' . $value['deduct_code'] . '</td>
                    <td>' . $value['deduct_title'] . '</td>
                    <td>' . ($value['is_employee_share'] == 1 ? 'YES' : 'NO') . '</td>
                    <td>' . $value['deduct_category'] . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" onclick="GetDeductionConfigDetails('.$value["config_deduction_id"].')" data-toggle="modal" data-target="#config_deductions_modal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="DeleteDeductionConfig('.$value["config_deduction_id"].')" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewGovShares(){
    include_once('../includes/class/GovShare.php');
    $GovShare = new GovShare();
    $govshares = $GovShare->GetGovShares();

    if($govshares != null){
        $count = 0;
        foreach($govshares as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['deduction_type_name'] . '</td>
                    <td>' . $value['govshare_name'] . '</td>
                    <td>' . $value['govshare_code'] . '</td>
                    <td>' . $value['govshare_acctcode'] . '</td>
                    <td>' . $value['govshare_rate'] . '</td>
                    <td>' . ($value['is_percentage'] == 1 ? 'Percentage' : 'Fixed Amount') . '</td>
                    <td>' . ($value['active'] == 1 ? 'active' : 'inactive') . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm edit-btn" data-govshare-id="'.$value['govshare_id'].'" data-toggle="modal" data-target="#config_deductions_modal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn" data-govshare-id="'.$value['govshare_id'].'" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewAllEmployeesEarnings(){
    include_once('../includes/class/Earning.php');
    $EmployeeEarning = new Earning();
    $employee_earnings = $EmployeeEarning->GetAllEmployeesEarnings();

    if($employee_earnings != null){
        $count = 0;

        foreach($employee_earnings as $key => $value) {
            $count++;

            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['employee_id_num'] . '</td>
                    <td>' . $value['lastname'] . ", " . $value['firstname'] . " " . $value['extension'] . '</td>
                    <td class="text-center">' . OutputShortDate($value['effective_date']) . '</td>
                    <td align="right">' . OutputMoney($value['gross_amount']) . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary btn-view-earnings" data-employee-id="'.$value['employee_id'].'">
                                <span data-toggle="tooltip" title="View Earnings" data-placement="bottom"><i class="fa fa-search"></i> View Earnings
                            </button>
                        </div>
                    </td>
                </tr>';

                /**/
        }   
    }
}

function ViewAllEmployeesDeductions(){
    include_once('../includes/class/Deduction.php');
    $EmployeeDeduction = new Deduction();
    $employee_deductions = $EmployeeDeduction->GetAllEmployeesDeductions();

    if($employee_deductions != null){
        $count = 0;

        foreach($employee_deductions as $key => $value) {
            $count++;

            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['employee_id_num'] . '</td>
                    <td>' . $value['lastname'] . ", " . $value['firstname'] . " " . $value['extension'] . '</td>
                    <td class="text-center">' . OutputShortDate($value['effective_date']) . '</td>
                    <td align="right">' . OutputMoney($value['total_deduction']) . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary btn-view-deductions" data-employee-id="'.$value['employee_id'].'">
                                <span data-toggle="tooltip" title="View Deductions" data-placement="bottom"><i class="fa fa-search"></i> View Deductions
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewEmployeeGovShareRecords(){
    include_once('../includes/class/GovShare.php');
    $GovShare = new GovShare();
    // 1. Fetch all govshare configs in the desired order
    $govshares = $GovShare->FetchGovShares();

    $headers = [];
    foreach ($govshares as $gs) {
        $headers[$gs['govshare_id']] = $gs['govshare_name'];
    }

    // 2. Fetch all employee govshare records
    $records = $GovShare->GetEmployeeGovShareRecords();

    $employees = [];

    if ($records) {
        // Group records by employee
        foreach ($records as $rec) {
            $empId = $rec['employee_id'];
            $employees[$empId]['name'] = $rec['lastname'] . ', ' . $rec['firstname'];
            $employees[$empId]['govshares'][$rec['govshare_id']] = $rec['govshare_amount']; 
        }
    }

    // 3. Build table
    echo "<table id='govShareTable' class='table table-bordered table-hover'>";

    // Table header row
    echo "<thead>";
    echo "<tr>";
    echo "<th>No.</th>";
    echo "<th>Employee Name</th>";
    foreach ($headers as $govName) {
        echo "<th>{$govName}</th>";
    }
    echo "</tr>";
    echo "</thead><tbody>";

    // Table body rows
    if (!empty($employees)) {
        $count = 0;
        foreach ($employees as $emp) {
            $count++;
            echo "<tr>";
            echo "<td>{$count}</td>";
            echo "<td>{$emp['name']}</td>";
            foreach ($headers as $govId => $govName) {
                $amount = isset($emp['govshares'][$govId]) 
                    ? number_format((float)$emp['govshares'][$govId], 2) 
                    : "0.00";
                echo "<td style='text-align:right;'>{$amount}</td>";
            }
            echo "</tr>";
        }
    } else {
        // No employees found, still show empty rows?
        echo "<tr><td colspan='" . (count($headers) + 1) . "' style='text-align:center;'>No records found</td></tr>";
    }

    echo "</tbody></table>";
}

function ViewSignatoriesList(){
    include_once('../includes/class/Signatory.php');
    $Signatory = new Signatory();
    $signatories = $Signatory->FetchAllSignatories();

    if($signatories != null){
        $count = 0;
        foreach($signatories as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['full_name'] . '</td>
                    <td>' . $value['position_title'] . '</td>
                    <td>' . $value['role_type'] . '</td>
                    <td>' . $value['dept_name'] . '</td>
                    <td>' . $value['report_type'] . '</td>
                    <td>' . $value['sign_order'] . '</td>
                    <td>' . $value['sign_particulars'] . '</td>
                    <td class="text-center">' . ($value['is_active'] == 1 ? '<span class="badge badge-success">active</span>' : '<span class="badge badge-danger">inactive</span>') . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" onclick="GetSignatoryDetails('.$value["signatory_id"].')" data-toggle="modal" data-target="#signatory_modal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="DeleteSignatory('.$value["signatory_id"].')" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewAllEmployeeLeaveApplications(){
    include_once('../includes/class/Employee.php');
    include_once('../includes/class/Leave.php');

    $Employee = new Employee();
    $LeaveApplication = new Leave();

    $employee_id = "";

    if(isset($_SESSION['uid']) && $_SESSION['uid'] != ""){
        $employee_id = $Employee->getEmployeeIDByUserId($_SESSION['uid']);
    }
    $employee_leaves = $LeaveApplication->getLeaveApplicationsByEmployee($employee_id);

    if($employee_leaves != null){
        $count = 0;

        foreach($employee_leaves as $key => $value) {
            $count++;

            // Set badge HTML based on status
            $badge = '';
            if ($value['status'] == 'Pending') {
                $badge = '<span class="badge badge-warning">Pending</span>';
            } elseif ($value['status'] == 'Approved') {
                $badge = '<span class="badge badge-success">Approved</span>';
            } elseif ($value['status'] == 'Disapproved') {
                $badge = '<span class="badge badge-danger">Disapproved</span>';
            } else {
                $badge = '<span class="badge badge-secondary">' . htmlspecialchars($value['status']) . '</span>';
            }

            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['leave_name'] . '</td>
                    <td class="text-center">' . OutputShortDate($value['date_filed']) .'</td>
                    <td class="text-center">' . (is_array($value['dates']) ? implode('<br>', array_map('OutputShortDate', $value['dates'])) : OutputShortDate($value['dates'])) . '</td>
                    <td class="text-center">' . count($value['dates']) .'</td>
                    <td class="text-center">' . $badge . '</td>
                </tr>';
        }   
    }
}

function ViewAllLeaveApplications(){
    include_once('../includes/class/Leave.php');

    $LeaveApplication = new Leave();

    $leave_applications = $LeaveApplication->getLeaveApplications();

    if($leave_applications != null){
        $count = 0;

        foreach($leave_applications as $key => $value) {
            $count++;

            // Set badge HTML based on status
            $badge = '';
            if ($value['status'] == 'Pending') {
                $badge = '<span class="badge badge-warning">Pending</span>';
            } elseif ($value['status'] == 'Approved') {
                $badge = '<span class="badge badge-success">Approved</span>';
            } elseif ($value['status'] == 'Disapproved') {
                $badge = '<span class="badge badge-danger">Disapproved</span>';
            } else {
                $badge = '<span class="badge badge-secondary">' . htmlspecialchars($value['status']) . '</span>';
            }

            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['employee'] . '</td>
                    <td>' . $value['leave_name'] . '</td>
                    <td>' . OutputShortDate($value['date_filed']) .'</td>
                    <td>' . (is_array($value['dates']) ? implode('<br>', array_map('OutputShortDate', $value['dates'])) : OutputShortDate($value['dates'])) . '</td>
                    <td class="text-center">' . count($value['dates']) . '</td>
                    <td class="text-center">' . $badge . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-success btn-sm btnApproveLeave" data-leaveApp-id="'.$value['leave_application_id'].'">
                                <i class="fa fa-check"></i> Approve
                            </button>
                            <button class="btn btn-danger btn-sm btnDisapproveLeave" data-leaveApp-id="'.$value['leave_application_id'].'">
                                <i class="fa fa-times"></i> Disapprove
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}

function ViewLeaveTypes(){
    include_once('../includes/class/Leave.php');
    $Leave = new Leave();

    $leave_types = $Leave->FetchAllLeaveTypes();

    if($leave_types != null){
        $count = 0;
        foreach($leave_types as $key => $value) {
            $count++;
            echo '<tr>
                    <td>' . $count . '</td>
                    <td>' . $value['leave_code'] . '</td>
                    <td>' . $value['leave_name'] . '</td>
                    <td>' . $value['yearly_allotment'] . '</td>
                    <td>' . ($value['is_accumulative'] == 1 ? 'Yes' : 'No') . '</td>
                    <td>' . ($value['monthly_accrual'] == 1 ? 'Yes' : 'No') . '</td>
                    <td>' . $value['gender_restriction'] . '</td>
                    <td>' . $value['reset_policy'] . '</td>
                    <td>' . $value['description'] . '</td>
                    <td>' . ($value['active'] == 1 
                                ? '<span class="badge badge-success">Active</span>' 
                                : '<span class="badge badge-secondary">Inactive</span>') . 
                    '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-default btn-sm" onclick="GetLeaveTypeDetails('.$value["leave_type_id"].')" data-toggle="modal" data-target="#leaveTypeModal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="DeleteLeaveType('.$value["leave_type_id"].')" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>';
        }   
    }
}
##-------------------------------##

##-----Render View of Dropdowns-----##

function ViewDepartmentsDropdown(){  
    include_once '../includes/class/Department.php';
    $MyDepartment = new Department();                                    
    $rendered_departments_drpdwn = "";
    $departments = $MyDepartment->GetDepartments();
    if($departments != null || $departments != ""){
        foreach ($departments as $key => $value) {
            $rendered_departments_drpdwn .= "<option value='".$value["dept_id"]."'>".$value["dept_code"] . ' | ' . $value["dept_title"] . "</option>";
        }
    }
    return $rendered_departments_drpdwn;                                            
}

function ViewPositionsDropdown(){  
    include_once '../includes/class/Position.php';
    $MyPosition = new Position();                                    
    $rendered_positions_drpdwn = "";
    $positions = $MyPosition->GetPositions();
    if($positions != null || $positions != ""){
        foreach ($positions as $key => $value) {
            $rendered_positions_drpdwn .= "<option value='".$value["position_id"]."'>". $value["position_itemnum"] . " | " . $value["position_title"] . "</option>";
        }
    }
    return $rendered_positions_drpdwn;                                            
}

function ViewVacantPositionsDropdown(){  
    include_once '../includes/class/Position.php';
    $MyPosition = new Position();                                    
    $rendered_positions_drpdwn = "";
    $positions = $MyPosition->GetVacantPositions();
    if($positions != null || $positions != ""){
        foreach ($positions as $key => $value) {
            $rendered_positions_drpdwn .= "<option value='".$value["position_id"]."'>".$value["position_title"] . "</option>";
        }
    }
    return $rendered_positions_drpdwn;                                            
}

function ViewEmployeesDropdown(){  
    include_once '../includes/class/Employee.php';
    $Employees = new Employee();  

    $rendered_employees_drpdwn = "";
    $employee_name = "";

    $employees = $Employees->GetEmployees();
    if($employees != null || $employees != ""){
        foreach ($employees as $key => $value) {
            $employee_name = $value["lastname"].', '.$value["firstname"].' '.$value["extension"].' '.$value["middlename"][0];
            $rendered_employees_drpdwn .= "<option value='".$value["employee_id"]."'>". $employee_name . "</option>";
        }
    }
    return $rendered_employees_drpdwn;                                            
}

function ViewEmployedEmployeesWithEarningsDropdown(){  
    include_once '../includes/class/Employee.php';
    $Employees = new Employee();  

    $rendered_employees_drpdwn = "";
    $employee_name = "";

    $employees = $Employees->FetchEmployedEmployeesWithEarnings();
    if($employees != null || $employees != ""){
        foreach ($employees as $key => $value) {
            $employee_name = $value["full_name"];
            $rendered_employees_drpdwn .= "<option value='".$value["employee_id"]."'>". $employee_name . "</option>";
        }
    }
    return $rendered_employees_drpdwn;                                            
}

function ViewConfigEarningsDropdown(){  
    include_once '../includes/class/Earning.php';
    $Earnings = new Earning();  

    $rendered_configEarnings_drpdwn = "";

    $earning_configs = $Earnings->GetEarningConfigs();
    if($earning_configs != null || $earning_configs != ""){
        foreach ($earning_configs as $key => $value) {
            $rendered_configEarnings_drpdwn .= "<option value='".$value["config_earning_id"]."'>". $value["earning_code"] . "</option>";
        }
    }
    return $rendered_configEarnings_drpdwn;                                            
}

function ViewConfigDeductionsDropdown(){  
    include_once '../includes/class/Deduction.php';
    $Deductions = new Deduction();  

    $rendered_configDeductions_drpdwn = "";

    $deduction_configs = $Deductions->GetDeductionConfigs();
    if($deduction_configs != null || $deduction_configs != ""){
        foreach ($deduction_configs as $key => $value) {
            $rendered_configDeductions_drpdwn .= "<option value='".$value["config_deduction_id"]."'>". $value["deduction_type_code"] . ' | ' . $value["deduct_code"] . "</option>";
        }
    }
    return $rendered_configDeductions_drpdwn;                                            
}

function ViewDeductionTypesDropdown(){  
    include_once '../includes/class/Deduction.php';
    $DeductionType = new Deduction();  

    $rendered_deductTypes_drpdwn = "";

    $deduct_types = $DeductionType->GetDeductionTypes();
    if($deduct_types != null || $deduct_types != ""){
        foreach ($deduct_types as $key => $value) {
            $rendered_deductTypes_drpdwn .= "<option value='".$value["deduction_type_id"]."'>". $value["deduction_type_name"] . "</option>";
        }
    }
    return $rendered_deductTypes_drpdwn;                                            
}

function ViewPayFrequenciesDropdown(){
    include_once '../includes/class/Payroll.php';
    $PayrollFrequency = new Payroll();  

    $rendered_frequencies_drpdwn = "";
    $pay_frequencies = $PayrollFrequency->GetPayFrequencies();
    $has_active = false;

    if (!empty($pay_frequencies)) {
        foreach ($pay_frequencies as $key => $value) {
            $selected = '';
            if ($value["is_active"] == 1) {
                $selected = 'selected';
                $has_active = true;
            }
            $rendered_frequencies_drpdwn .= "<option value='" . $value["payroll_freq_id"] . "' $selected>" . $value["freq_label"] . "</option>";
        }
    }
    if (!$has_active) {
        // Prepend warning option if no active frequency found
        $rendered_frequencies_drpdwn = "<option selected hidden disabled>⚠ Please set an active payroll frequency</option>" . $rendered_frequencies_drpdwn;
    }
    return $rendered_frequencies_drpdwn;   
}

// Function to populate pay periods
function generatePayPeriodOptionsDropdown() {
    include_once '../includes/class/Payroll.php';
    $PayrollSetting = new Payroll();

    $active_frequency = $PayrollSetting->GetCurrentActiveFrequency();
    $frequency = $active_frequency['freq_code'];

    $options = '';
    $pay_periods = $PayrollSetting->GetPayPeriods($frequency);
    if(!empty($pay_periods)){
        foreach ($pay_periods as $key => $value) {
            $val = $value['date_start'] . '_' . $value['date_end'];
            $options .= "<option value='".$val."'>". $value["period_label"] . "</option>";
        }
    }
    else{
        $options .= "<option value='' hidden selected>Payroll Periods not set...</option>";
    }
    return $options;
}

function ViewPayPeriodYearsDropdown(){
    include_once '../includes/class/Payroll.php';
    $PayrollSetting = new Payroll();

    $currentYear = date("Y"); // get the current year
    $options = '';

    $years = $PayrollSetting->GetPayrollYears();
    if (!empty($years)) {
        foreach ($years as $year) {
            $options .= '<option value="' . $year . '">' . $year . '</option>';
        }
    }
    else{
        $options .= "<option value='' hidden selected>Payroll Years not set...</option>";
    }
    return $options;
}

function BuildPayrollPeriodDropdown($year) {
    include_once '../includes/class/Payroll.php';
    $PayrollSetting = new Payroll();

    $active_frequency = $PayrollSetting->GetCurrentActiveFrequency();
    $frequency = $active_frequency['freq_code'];

    $options = "";
    $periods = $PayrollSetting->GetPayrollPeriodsByYear($year,$frequency);
    
    foreach ($periods as $period) {
        $selected = ($selected_id == $period['payroll_period_id']) ? "selected" : "";
        $options .= "<option value='{$period['payroll_period_id']}' {$selected}>{$period['period_label']}</option>";
    }
    return $options;
}

function BuildLeaveTypesDropdown(){  
    include_once '../includes/class/Leave.php';
    $Leave = new Leave();  

    $rendered_leaveTypes_drpdwn = "";

    $leave_types = $Leave->FetchAllLeaveTypes();
    if($leave_types != null || $leave_types != ""){
        foreach ($leave_types as $key => $value) {
            $rendered_leaveTypes_drpdwn .= "<option value='".$value["leave_type_id"]."'>". $value["leave_name"] . "</option>";
        }
    }
    return $rendered_leaveTypes_drpdwn;                                            
}
##-------------------------------##

// ====================================================================
// PAYROLL DASHBOARD SUMMARY FUNCTIONS
// ====================================================================

/**
 * Get total count of active employees included in payroll
 */
function ViewPayrollActiveEmployeesCount(){
    include_once('../includes/class/DB_conn.php');
    $db = new DB_conn();
    
    $query = "SELECT COUNT(a.employee_id) as total_count
              FROM employees_tbl a
              INNER JOIN employee_employments_tbl b
              ON a.employee_id = b.employee_id
              WHERE b.employment_status = 1 
              AND a.include_in_payroll = 1";
    
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $db->fetch_array($result);
        return $row['total_count'] ?? 0;
    }
    return 0;
}

/**
 * Get count of all processed payrolls for the current year
 */
function ViewProcessedPayrollsCount(){
    include_once('../includes/class/DB_conn.php');
    $db = new DB_conn();
    
    // Get current year
    $current_year = date('Y');
    
    // Count all processed payroll entries for the current year
    $query = "SELECT COUNT(*) as processed_count
              FROM payroll_entries
              WHERE YEAR(created_at) = $current_year";
    
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $db->fetch_array($result);
        return $row['processed_count'] ?? 0;
    }
    return 0;
}

/**
 * Get total payroll cost for current period (sum of net pay)
 */
function ViewTotalPayrollCost(){
    include_once('../includes/class/DB_conn.php');
    $db = new DB_conn();
    
    // Sum of net pay for all payroll entries
    $query = "SELECT COALESCE(SUM(net_pay), 0) as total_cost
              FROM payroll_entries";
    
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $db->fetch_array($result);
        return $row['total_cost'] ?? 0;
    }
    return 0;
}

/**
 * Get total gross pay for current period
 */


/**
 * Get current payroll period information
 */
function GetCurrentPayrollPeriod(){
    include_once('../includes/class/DB_conn.php');
    $db = new DB_conn();
    
    $query = "SELECT * FROM payroll_periods 
              WHERE date_start <= CURDATE() 
              AND date_end >= CURDATE()
              ORDER BY payroll_period_id DESC LIMIT 1";
    
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        return $db->fetch_array($result);
    }
    return null;
}

##-------------------------------##


