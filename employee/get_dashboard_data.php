<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

include_once '../includes/class/Employee.php';
include_once '../includes/class/Payroll.php';
include_once '../includes/class/Leave.php';
include_once '../includes/class/Payslip.php';
include_once '../includes/class/Employment.php';

$LoggedInUserEmployee = new Employee();
$user_id = $LoggedInUserEmployee->getSessionUID();

// Verify user is logged in
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$employee = new Employee();

// Get employee ID from user ID
$emp_result = $employee->getEmployeeIDByUserId($user_id);
if (!$emp_result) {
    echo json_encode(['error' => 'Employee not found']);
    exit;
}

$employee_id = $emp_result;  // emp_result is already the employee_id STRING
$action = isset($_GET['action']) ? $_GET['action'] : 'all';

// Get profile if requested
if ($action === 'profile' || $action === 'all') {
    $profile = $employee->GetEmployeeDetails($employee_id);
}

// Get payrolls if requested
if ($action === 'payrolls' || $action === 'all') {
    $payroll = new Payroll();
    $payrolls = $payroll->getEmployeePayrollRecords($employee_id, 5);
}

// Get payslips if requested
if ($action === 'payslips' || $action === 'all') {
    $payslip = new Payslip();
    $payslips = $payslip->getEmployeePayslipHistory($employee_id, 6);
}

// Get leaves if requested
if ($action === 'leaves' || $action === 'all') {
    $leave = new Leave();
    $leaves_data = $leave->getLeaveApplicationsByEmployee($employee_id);
    $leaves = $leaves_data ? array_slice($leaves_data, 0, 3) : [];
}

// Get employments if requested
if ($action === 'employments' || $action === 'all') {
    $employment = new Employment();
    $employments = $employment->GetEmployeeEmployments($employee_id);
    // ensure array
    $employments = $employments ? $employments : [];
}

// Return response based on action
if ($action === 'profile') {
    echo json_encode([
        'success' => true,
        'data' => $profile
    ]);
} elseif ($action === 'payrolls') {
    echo json_encode([
        'success' => true,
        'data' => $payrolls ? $payrolls : []
    ]);
} elseif ($action === 'payslips') {
    echo json_encode([
        'success' => true,
        'data' => $payslips ? $payslips : []
    ]);
} elseif ($action === 'leaves') {
    echo json_encode([
        'success' => true,
        'data' => $leaves
    ]);
} else {
    // Return all data
    echo json_encode([
        'success' => true,
        'employee_id' => $employee_id,
        'profile' => isset($profile) ? $profile : null,
        'payrolls' => isset($payrolls) ? ($payrolls ? $payrolls : []) : [],
        'leaves' => isset($leaves) ? $leaves : [],
        'payslips' => isset($payslips) ? ($payslips ? $payslips : []) : [],
        'employments' => isset($employments) ? $employments : []
    ]);
}
?>