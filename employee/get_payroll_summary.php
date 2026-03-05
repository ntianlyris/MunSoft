<?php
session_start();
header('Content-Type: application/json');

include_once '../includes/class/Employee.php';
include_once '../includes/class/Payroll.php';

// Get user_id from session using proper method
$LoggedInUserEmployee = new Employee();
$user_id = $LoggedInUserEmployee->getSessionUID();

// Verify user is logged in
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Get employee_id from user_id
$employee = new Employee();
$employee_id = $employee->getEmployeeIDByUserId($user_id);

if (!$employee_id) {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
    exit;
}

// Get current payroll summary (latest entry, not accumulated)
$payroll = new Payroll();
$summary = $payroll->getPayrollSummaryByEmployee($employee_id);

if ($summary) {
    echo json_encode([
        'success' => true,
        'data' => $summary
    ]);
} else {
    // Return zero values if no payroll records found
    echo json_encode([
        'success' => true,
        'data' => [
            'total_gross' => 0,
            'total_deductions' => 0,
            'total_net_pay' => 0,
            'period_label' => 'No payroll data'
        ]
    ]);
}
?>
