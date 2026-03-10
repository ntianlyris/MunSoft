<?php
session_start();
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
    
    // Get periods for this year - try 'monthly' first, then fallback to any frequency
    $frequency = 'monthly';
    $periods = $payroll->GetPayrollPeriodsByYear($year, $frequency);
    
    // If no monthly periods found, try without frequency filter
    if (!$periods || count($periods) == 0) {
        error_log("DEBUG: No monthly periods found for year $year, checking all frequencies");
    }
    
    if (!$periods) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No payslips available for ' . $year
        ]);
        exit;
    }
    
    // Transform to month data and fetch payslip financial data
    $payslip = new Payslip();
    $months = [];
    
    foreach ($periods as $period) {
        $payroll_period_str = $period['date_start'] . '_' . $period['date_end'];
        
        // Generate payslip to get financial data
        try {
            $payslip_data = $payslip->GeneratePayslip($employee_id, $year, $payroll_period_str);
            
            $gross = isset($payslip_data['gross']) ? floatval($payslip_data['gross']) : 0;
            $deductions = isset($payslip_data['deductions']['total_deductions']) ? floatval($payslip_data['deductions']['total_deductions']) : 0;
            $net_pay = isset($payslip_data['net_pay']) ? floatval($payslip_data['net_pay']) : 0;
        } catch (Exception $e) {
            error_log("Error generating payslip: " . $e->getMessage());
            $gross = 0;
            $deductions = 0;
            $net_pay = 0;
        }
        
        $months[] = [
            'month' => date('m', strtotime($period['date_start'])),
            'year' => $year,
            'label' => $period['period_label'],
            'date_start' => $period['date_start'],
            'date_end' => $period['date_end'],
            'payroll_period' => $payroll_period_str,
            'gross' => $gross,
            'deductions' => $deductions,
            'net_pay' => $net_pay,
            'has_data' => ($gross > 0 && $net_pay > 0 && $payslip->IsPayslipDownloadable($employee_id, $payroll_period_str)) // Security flag
        ];
    }
    
    error_log("DEBUG: Months fetched = " . count($months));
    
    echo json_encode([
        'success' => true,
        'data' => $months,
        'debug' => ['count' => count($months), 'year' => $year]
    ]);
} elseif ($action === 'get_available_years') {
    // Get all available years for employee payslips
    $payroll = new Payroll();
    $years = $payroll->GetAvailableYearsByEmployee($employee_id, 'monthly');
    
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

