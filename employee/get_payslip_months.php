<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

include_once '../includes/class/Employee.php';
include_once '../includes/class/Payroll.php';
include_once '../includes/class/Payslip.php';

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

$employee_id = $emp_result;
$action = isset($_GET['action']) ? $_GET['action'] : 'fetch_months_by_year';
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

if ($action === 'fetch_months_by_year') {
    // Get monthly payroll periods for the specified year
    $payroll = new Payroll();
    $payslip = new Payslip();

    // EXPLICIT ASSIGNMENT: Force 'monthly' view for employees for now as per requirements.
    $frequency = 'monthly';

    // OPTIMIZED: Fetch all payroll entries for the year in one query from already saved/locked data
    // This avoids heavy re-regeneration of dozens of payslips in a loop
    $history = $payroll->GetEmployeePayrollHistoryByYear($employee_id, $year, $frequency);

    if (!$history || count($history) == 0) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No payslips available for ' . $year
        ]);
        exit;
    }

    $months = [];

    foreach ($history as $entry) {
        $payroll_period_str = $entry['date_start'] . '_' . $entry['date_end'];

        $gross = floatval($entry['gross']);
        $deductions = floatval($entry['total_deductions']);
        $saved_net = floatval($entry['net_pay']);
        
        // CUSTOM REQUIREMENT: Calculate monthly_net manually. 
        // Logic: Stored gross - stored total deductions.
        // Reason: Saved net_pay might be halved for semi-monthly, but dashboard should show full period net.
        $monthly_net = $gross - $deductions;

        $months[] = [
            'month' => date('m', strtotime($entry['date_start'])),
            'year' => $year,
            'label' => $entry['period_label'],
            'date_start' => $entry['date_start'],
            'date_end' => $entry['date_end'],
            'payroll_period' => $payroll_period_str,
            'gross' => $gross,
            'deductions' => $deductions,
            'net_pay' => $saved_net, // Literal DB value
            'monthly_net' => $monthly_net, // Recalculated value for UI display
            'has_data' => ($gross > 0 && $payslip->IsPayslipDownloadable($employee_id, $payroll_period_str))
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $months,
        'debug' => ['count' => count($months), 'year' => $year]
    ]);
} elseif ($action === 'get_available_years') {
    $payroll = new Payroll();

    // Get all available years for employee payslips
    // Get current active frequency to leave it ready for future toggle
    $active_freq = $payroll->GetCurrentActiveFrequency();
    $system_frequency = $active_freq ? $active_freq['freq_code'] : 'monthly';

    // EXPLICIT ASSIGNMENT: Force 'monthly' view for employees for now as per requirements.
    // To revert to dynamic system frequency in the future, change this to: $frequency = $system_frequency;
    $employee_view_frequency = 'monthly';
    $frequency = $employee_view_frequency;

    $years = $payroll->GetAvailableYearsByEmployee($employee_id, $frequency);

    // Debug: Log what we're querying
    error_log("DEBUG: Employee ID = " . $employee_id);
    error_log("DEBUG: Years returned = " . json_encode($years));

    echo json_encode([
        'success' => true,
        'data' => $years,
        'debug' => [
            'employee_id' => $employee_id,
            'count' => count($years)
        ]
    ]);
} elseif ($action === 'debug_payroll_data') {
    // Debug endpoint to check what data exists
    $payroll = new Payroll();

    // Check if employee has any payroll entries
    $debug_data = [];

    // Try to fetch available years
    $years = $payroll->GetAvailableYearsByEmployee($employee_id, 'monthly');
    $debug_data['available_years'] = $years;
    $debug_data['employee_id'] = $employee_id;
    $debug_data['frequency_filter'] = 'monthly';

    echo json_encode([
        'success' => true,
        'debug_info' => $debug_data,
        'message' => 'Debug data retrieved'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid action'
    ]);
}
?>