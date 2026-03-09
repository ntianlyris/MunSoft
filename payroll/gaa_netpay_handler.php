<?php
/**
 * gaa_netpay_handler.php
 * Handles AJAX requests for the GAA Net Pay Status page
 */

session_start();
header('Content-Type: application/json');

include_once '../includes/class/DB_conn.php';
include_once '../includes/class/Payroll.php';
include_once '../includes/class/Employee.php';
include_once '../includes/class/Admin.php';
include_once '../includes/gaa_netpay_module/GAAModule.php';

// Check authentication
if (!isset($_SESSION['uid'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$db = new DB_conn();
$Payroll = new Payroll();
$Employee = new Employee();
$GAAModule = new GAANetPayIntelligence($db, $_SESSION['uid'] ?? 0);

// Helper function to get the current period context mimicking Payroll.php
function getCurrentPeriodContext($Payroll)
{
    global $db;
    $active_freq = $Payroll->GetCurrentActiveFrequency();
    $frequency = $active_freq['freq_code'] ?? 'monthly';

    // 1. Better approach: Find the most recent period that actually has payroll entries
    // This allows the GAA monitor to "follow" where the work is currently being done
    $q = "SELECT pp.* FROM payroll_periods pp
          INNER JOIN (
              SELECT DISTINCT payroll_period_id FROM payroll_entries
          ) pe ON pp.payroll_period_id = pe.payroll_period_id
          WHERE pp.frequency = '" . $db->escape_string($frequency) . "'
          ORDER BY pp.date_end DESC LIMIT 1";
          
    $res = $db->query($q);
    if ($res && $res->num_rows > 0) {
        $period = $res->fetch_assoc();
        return [
            'start_date' => $period['date_start'],
            'end_date' => $period['date_end'],
            'frequency' => $period['frequency'],
            'is_second_half' => $Payroll->IsSecondHalfOfMonth($period['date_start']),
            'period_label' => $period['period_label']
        ];
    }

    // 2. Fallback to calculating based on real-time if no active period is found
    $today = date('Y-m-d');
    $is_second_half = false;

    if ($frequency == 'semi-monthly') {
        $day = intval(date('d'));
        if ($day <= 15) {
            $start = date('Y-m-01');
            $end = date('Y-m-15');
            $is_second_half = false;
            $period_label = date('M 01-15, Y');
        } else {
            $start = date('Y-m-16');
            $end = date('Y-m-t');
            $is_second_half = true;
            $period_label = date('M 16-t, Y');
        }
    } else {
        $start = date('Y-m-01');
        $end = date('Y-m-t');
        $is_second_half = false;
        $period_label = date('F Y');
    }

    return [
        'start_date' => $start,
        'end_date' => $end,
        'frequency' => $frequency,
        'is_second_half' => $is_second_half,
        'period_label' => $period_label
    ];
}

// Helper to compute a single employee's projected net pay replicating Payroll.php logic
function computeEmployeeProjectedNetPay($Payroll, $employee_id, $period_ctx)
{
    // For Monthly Projection, we ALWAYS fetch data for the entire month
    // regardless of whether the current period is 1st or 2nd half.
    $start_date = date('Y-m-01', strtotime($period_ctx['start_date']));
    $end_date = date('Y-m-t', strtotime($period_ctx['end_date']));
    $frequency = $period_ctx['frequency'];
    $is_second_half = $period_ctx['is_second_half'];

    $earnings = $Payroll->GetEmployeeEarnings($employee_id, $start_date, $end_date);
    $deductions = $Payroll->GetEmployeeDeductions($employee_id, $start_date, $end_date);

    // Force govshare end date to the last day of its month
    $govshare_end_date = date("Y-m-t", strtotime($end_date));
    $govshares = $Payroll->GetEmployeeGovShares($employee_id, $start_date, $govshare_end_date);

    $gross = 0;
    $total_deductions = 0;
    $display_total_deductions = 0;
    $breakdown = [];

    if (!empty($earnings)) {
        foreach ($earnings as $earn) {
            $amount = floatval($earn['earning_comp_amt']);
            $gross += $amount;
            $breakdown[] = [
                'type' => 'earning',
                'label' => $earn['earning_code'],
                'amount' => $amount
            ];
        }
    }

    if (!empty($deductions)) {
        foreach ($deductions as $deduct) {
            $amount = floatval($deduct['deduction_comp_amt']);
            $total_deductions += $amount;

            $breakdown[] = [
                'type' => 'deduction',
                'label' => $deduct['deduct_code'],
                'amount' => $amount
            ];
        }
    }

    if (!empty($govshares)) {
        foreach ($govshares as $gs) {
            $amount = floatval($gs['govshare_amount']);
            // FIX: DO NOT add govshares to $total_deductions! As per Payroll.php, employer shares don't deduct from employee gross.

            $breakdown[] = [
                'type' => 'deduction',
                'label' => $gs['govshare_code'],
                'amount' => $amount
            ];
        }
    }

    // Call the Single Source of Truth from Payroll Engine
    // This dynamically handles semi-monthly vs monthly computations.
    $net = $Payroll->CalculateNetPay($gross, $total_deductions, $frequency);
    $monthly_net = $Payroll->CalculateNetPay($gross, $total_deductions, 'monthly');

    return [
        'gross' => $gross,
        'total_deductions' => $total_deductions,
        'net_pay' => $net,
        'monthly_net' => $monthly_net,
        'breakdown' => $breakdown
    ];
}

// Helper to get last saved entry
function getLastSavedPayrollEntry($db, $employee_id)
{
    // Get the most recent payroll entry for this employee
    $query = "SELECT p.gross, p.total_deductions, p.net_pay, p.payroll_period_id, pp.period_label, pp.frequency
              FROM payroll_entries p 
              LEFT JOIN payroll_periods pp ON p.payroll_period_id = pp.payroll_period_id
              WHERE p.employee_id = '" . $db->escape_string($employee_id) . "' 
              ORDER BY p.payroll_entry_id DESC LIMIT 1";
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Always calculate full monthly net for GAA comparison
        // We do this by calling CalculateNetPay and forcing the 'monthly' flag manually
        $dummyPayroll = new Payroll();
        $monthly_net = $dummyPayroll->CalculateNetPay(floatval($row['gross']), floatval($row['total_deductions']), 'monthly');
        
        if ($row['frequency'] === 'semi-monthly') {
            // However, verify that it doesn't conflict with net_pay*2 for old records
            $monthly_net = $row['net_pay'] * 2;
        }

        return [
            'gross' => floatval($row['gross']),
            'total_deductions' => floatval($row['total_deductions']),
            'net_pay' => $row['net_pay'], // This is the period-specific net stored in DB
            'monthly_net' => $monthly_net, // Calculated full monthly net
            'payroll_period_id' => $row['payroll_period_id'],
            'frequency' => $row['frequency'],
            'period_label' => $row['period_label']
        ];
    }
    return null;
}

// Helper to calculate trend
function calculateTrend($current_status, $last_status)
{
    if (!$last_status)
        return 'SAME';

    $tiers = [
        'CRITICAL' => 1,
        'DANGER' => 2,
        'WARNING' => 3,
        'CAUTION' => 4,
        'STABLE' => 5,
        'SAFE' => 6
    ];

    $curr_val = $tiers[$current_status] ?? 0;
    $last_val = $tiers[$last_status] ?? 0;

    if ($curr_val > $last_val)
        return 'IMPROVED';
    if ($curr_val < $last_val)
        return 'WORSENED';
    return 'SAME';
}

if ($action === 'fetch_department_status') {
    $department_id = $_POST['department_id'] ?? '';
    $employment_type = $_POST['employment_type'] ?? 'Regular';

    if (empty($department_id)) {
        echo json_encode(['success' => false, 'message' => 'Department is required']);
        exit;
    }

    $period_ctx = getCurrentPeriodContext($Payroll);

    // Get employees using equivalent query to payroll list logic
    $query = "SELECT e.employee_id, e.employee_id_num, e.firstname, e.middlename, e.lastname, e.extension, pos.position_title
              FROM employees_tbl e
              INNER JOIN employee_employments_tbl ee ON e.employee_id = ee.employee_id
              LEFT JOIN positions_tbl pos ON ee.position_id = pos.position_id
              WHERE e.include_in_payroll = 1 
              AND ee.dept_assigned = '" . $db->escape_string($department_id) . "'
              AND ee.employment_type = '" . $db->escape_string($employment_type) . "'
              AND e.employee_status IN ('Active', 'On Leave')
              AND ee.employment_status = 1";

    $result = $db->query($query);

    $employees_data = [];
    $summary_stats = [
        'CRITICAL' => 0, 'DANGER' => 0, 'WARNING' => 0,
        'CAUTION' => 0, 'STABLE' => 0, 'SAFE' => 0
    ];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employee_id = $row['employee_id'];
            $full_name = trim($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['extension'] . ' ' . $row['middlename']);

            // 1. Compute current projected net pay (Always full monthly)
            $computed = computeEmployeeProjectedNetPay($Payroll, $employee_id, $period_ctx);
            $current_status_data = $GAAModule->classifyNetPayStatus($computed['monthly_net']);
            $current_status = $current_status_data['status'];

            // Increment summary stats
            if (isset($summary_stats[$current_status])) {
                $summary_stats[$current_status]++;
            }

            // 2. Query last saved payroll record
            $last_record = getLastSavedPayrollEntry($db, $employee_id);
            $last_status = null;
            $last_net_pay_display = null;
            $last_monthly_net = null;

            if ($last_record) {
                // For UI display: Use the ACTUAL net pay stored in the record (frequency-based)
                $last_net_pay_display = floatval($last_record['net_pay']);

                // For Status Classification: Always use the FULL monthly net pay
                $last_monthly_net = $last_record['monthly_net'];

                $last_status_data = $GAAModule->classifyNetPayStatus($last_monthly_net);
                $last_status = $last_status_data['status'];
            }

            // 3. Determine trend
            $trend = calculateTrend($current_status, $last_status);

            $employees_data[] = [
                'employee_id' => $employee_id,
                'employee_id_num' => $row['employee_id_num'],
                'full_name' => $full_name,
                'position' => $row['position_title'],
                'gross' => $computed['gross'],
                'total_deductions' => $computed['total_deductions'],
                'net_pay' => $computed['net_pay'],
                'monthly_net' => $computed['monthly_net'],
                'current_status' => $current_status,
                'last_status' => $last_status,
                'last_net_pay' => $last_net_pay_display,
                'last_monthly_net' => $last_monthly_net,
                'trend' => $trend
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $employees_data,
        'summary' => $summary_stats,
        'period_context' => $period_ctx
    ]);
    exit;
}

if ($action === 'fetch_employee_status') {
    $employee_id = $_POST['employee_id'] ?? 0;

    if (empty($employee_id)) {
        echo json_encode(['success' => false, 'message' => 'Employee ID required.']);
        exit;
    }

    // Verify user owns this employee ID if they are Employee role
    $roles = (new Admin())->initRoles($_SESSION['uid']);
    if (isset($roles['Employee'])) {
        $emp_id_check = $Employee->getEmployeeIDByUserId($_SESSION['uid']);
        if ($emp_id_check != $employee_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access to this employee profile.']);
            exit;
        }
    }

    $period_ctx = getCurrentPeriodContext($Payroll);
    $computed = computeEmployeeProjectedNetPay($Payroll, $employee_id, $period_ctx);

    $current_status_data = $GAAModule->classifyNetPayStatus($computed['monthly_net']);
    $current_status = $current_status_data['status'];

    $last_record = getLastSavedPayrollEntry($db, $employee_id);
    $last_status = null;
    $last_net_pay_display = null;
    $last_monthly_net = null;
    $last_period_label = null;

    if ($last_record) {
        // For UI display
        $last_net_pay_display = floatval($last_record['net_pay']);

        // For GAA Classification
        $last_monthly_net = $last_record['monthly_net'];

        $last_status_data = $GAAModule->classifyNetPayStatus($last_monthly_net);
        $last_status = $last_status_data['status'];
        $last_period_label = $last_record['period_label'];
    }

    $trend = calculateTrend($current_status, $last_status);

    echo json_encode([
        'success' => true,
        'data' => [
            'gross' => $computed['gross'],
            'total_deductions' => $computed['total_deductions'],
            'net_pay' => $computed['net_pay'],
            'monthly_net' => $computed['monthly_net'],
            'current_status' => $current_status,
            'last_status' => $last_status,
            'last_net_pay' => $last_net_pay_display,
            'last_monthly_net' => $last_monthly_net,
            'last_period_label' => $last_period_label,
            'trend' => $trend,
            'period_context' => $period_ctx,
            'breakdown' => $computed['breakdown']
        ]
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid Action']);
