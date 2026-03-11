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
    $payroll = new Payroll();
    $payslip = new Payslip();

    // Fetch history for both frequencies to ensure coverage
    $history_semi = $payroll->GetEmployeePayrollHistoryByYear($employee_id, $year, 'semi-monthly') ?: [];
    $history_monthly = $payroll->GetEmployeePayrollHistoryByYear($employee_id, $year, 'monthly') ?: [];
    $history = array_merge($history_semi, $history_monthly);

    if (empty($history)) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No payslips available for ' . $year
        ]);
        exit;
    }

    // Sort by date start
    usort($history, function($a, $b) {
        return strtotime($a['date_start']) - strtotime($b['date_start']);
    });

    $aggregated = [];
    foreach ($history as $entry) {
        $month_num = date('m', strtotime($entry['date_start']));
        $month_key = $year . '-' . $month_num;

        if (!isset($aggregated[$month_key])) {
            $aggregated[$month_key] = [
                'month' => $month_num,
                'year' => $year,
                'label' => date('F', strtotime($entry['date_start'])) . ' ' . $year,
                'date_start' => date('Y-m-01', strtotime($entry['date_start'])),
                'date_end' => date('Y-m-t', strtotime($entry['date_start'])),
                'gross' => 0,
                'deductions' => 0,
                'net_pay' => 0
            ];
        }

        // Aggregate: MAX for whole-month coverage, SUM for halved net pay
        $aggregated[$month_key]['gross'] = max($aggregated[$month_key]['gross'], floatval($entry['gross']));
        $aggregated[$month_key]['deductions'] = max($aggregated[$month_key]['deductions'], floatval($entry['total_deductions']));
        $aggregated[$month_key]['net_pay'] += floatval($entry['net_pay']);
    }

    $final_months = [];
    foreach ($aggregated as $m) {
        $payroll_period_str = $m['date_start'] . '_' . $m['date_end'];
        $is_downloadable = $payslip->IsPayslipDownloadable($employee_id, $payroll_period_str);

        $final_months[] = [
            'month' => $m['month'],
            'year' => $m['year'],
            'label' => $m['label'],
            'date_start' => $m['date_start'],
            'date_end' => $m['date_end'],
            'payroll_period' => $payroll_period_str,
            'gross' => $m['gross'],
            'deductions' => $m['deductions'],
            'net_pay' => $m['net_pay'],
            'has_data' => ($m['gross'] > 0 && $is_downloadable)
        ];
    }

    // Sort descending by month
    usort($final_months, function($a, $b) {
        return $b['month'] - $a['month'];
    });

    echo json_encode(['success' => true, 'data' => $final_months]);
} elseif ($action === 'get_available_years') {
    $payroll = new Payroll();

    // Fetch available years for both frequencies to ensure coverage
    $years_semi = $payroll->GetAvailableYearsByEmployee($employee_id, 'semi-monthly') ?: [];
    $years_monthly = $payroll->GetAvailableYearsByEmployee($employee_id, 'monthly') ?: [];
    
    // Combine and unique
    $all_years = array_unique(array_merge($years_semi, $years_monthly));
    sort($all_years);
    $years = array_reverse($all_years);

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