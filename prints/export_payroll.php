<?php
require_once('../includes/PhpXlsxGenerator/PhpXlsxGenerator.php');
require_once('../includes/view/functions.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Department.php');
require_once('../includes/class/Employee.php');
require_once('../includes/class/Employment.php');

$Payroll = new Payroll();
$Department = new Department();
$Employee = new Employee();
$Employment = new Employment();

// GET parameters
$period = $_GET['period'] ?? '';
$department = $_GET['department'] ?? '';

if (!$period || !$department) {
    die("Missing required parameters.");
}

if (strpos($period, '_') !== false) {
    list($start_date, $end_date) = explode('_', $period);
    $payroll_period_id = $Payroll->GetPayrollPeriodByDates($start_date, $end_date)['payroll_period_id'];
} else {
    $payroll_period_id = $period;
    $period_dates = $Payroll->GetPeriodDatesByID($payroll_period_id);
    if ($period_dates) {
        $start_date = $period_dates['date_start'];
        $end_date = $period_dates['date_end'];
    } else {
        die("Invalid payroll period ID.");
    }
}

$results = $Payroll->FetchPayrollByPayPeriodAndDept($payroll_period_id, $department);

$department_name = $Department->GetDepartmentDetails($department)['dept_name'];
$pay_period_start = OutputDate($start_date);
$pay_period_end = OutputDate($end_date);

// 1. Collect all unique earning, deduction, govshare labels
$earning_labels = [];
$deduction_labels = [];
$govshare_labels = [];

foreach ($results as $row) {
    // Earnings
    $earnings = is_array($row['earnings_breakdown']) 
        ? $row['earnings_breakdown'] 
        : json_decode($row['earnings_breakdown'], true);
    if ($earnings) {
        foreach ($earnings as $e) {
            $earning_labels[$e['label']] = true;
        }
    }
    // Deductions
    $deductions = is_array($row['deductions_breakdown']) 
        ? $row['deductions_breakdown'] 
        : json_decode($row['deductions_breakdown'], true);
    if ($deductions) {
        foreach ($deductions as $d) {
            $deduction_labels[$d['label']] = true;
        }
    }
    // Govshares
    $govshares = is_array($row['govshares_breakdown']) 
        ? $row['govshares_breakdown'] 
        : json_decode($row['govshares_breakdown'], true);
    if ($govshares) {
        foreach ($govshares as $g) {
            $govshare_labels[$g['label']] = true;
        }
    }
}
$earning_labels = array_keys($earning_labels);
$deduction_labels = array_keys($deduction_labels);
$govshare_labels = array_keys($govshare_labels);

// 2. Build headers
$headers = [
    'EIDN', 'Employee', 'Position'
];
foreach ($earning_labels as $label) $headers[] = $label;
$headers[] = 'Gross Pay';
foreach ($deduction_labels as $label) $headers[] = $label;
$headers[] = 'Total Deductions';
foreach ($govshare_labels as $label) $headers[] = $label;
$headers[] = 'Net Pay';
$headers[] = 'Signature';

// 2.1 Build parent headers row
$parent_headers = [];
$parent_headers[] = ''; // EIDN
$parent_headers[] = ''; // Employee
$parent_headers[] = ''; // Position

// Earnings parent header
if (count($earning_labels) > 0) {
    $parent_headers = array_merge($parent_headers, array_fill(0, count($earning_labels), 'Earnings'));
}
$parent_headers[] = ''; // Gross Pay

// Deductions parent header
if (count($deduction_labels) > 0) {
    $parent_headers = array_merge($parent_headers, array_fill(0, count($deduction_labels), 'Deductions'));
}
$parent_headers[] = ''; // Total Deductions

// Gov Shares parent header
if (count($govshare_labels) > 0) {
    $parent_headers = array_merge($parent_headers, array_fill(0, count($govshare_labels), 'Gov Shares'));
}
$parent_headers[] = ''; // Net Pay
$parent_headers[] = ''; // Signature

// 3. Prepare data array for PhpXlsxGenerator
$data = [];
$data[] = ['Municipality of Polanco'];
$data[] = ['General Payroll'];
$data[] = ["Department: $department_name | Period: $pay_period_start to $pay_period_end"];
$data[] = [''];
$data[] = ['We acknowledge receipt of the sum shown opposite our names as full compensation for services rendered for the period stated.'];
$data[] = [''];
$data[] = $parent_headers; // <-- Parent headers row
$data[] = $headers;        // <-- Actual column headers

// 4. Data rows
$total_gross = 0;
$total_deductions = 0;
$total_net_pay = 0;
$ctr = 1;

foreach ($results as $row) {
    $position_full = $Employment->FetchEmployeeEmploymentDetailsByIDs($row['employee_id'], $row['employment_id'])['position_title'];
    $position_title = strpos($position_full, '(') !== false ? substr($position_full, 0, strpos($position_full, '(')) : $position_full;

    // Map earnings, deductions, govshares by label for this row
    $earnings = is_array($row['earnings_breakdown']) 
        ? $row['earnings_breakdown'] 
        : json_decode($row['earnings_breakdown'], true);
    $earnings_map = [];
    if ($earnings) foreach ($earnings as $e) $earnings_map[$e['label']] = $e['amount'];

    $deductions = is_array($row['deductions_breakdown']) 
        ? $row['deductions_breakdown'] 
        : json_decode($row['deductions_breakdown'], true);
    $deductions_map = [];
    if ($deductions) foreach ($deductions as $d) $deductions_map[$d['label']] = $d['amount'];

    $govshares = is_array($row['govshares_breakdown']) 
        ? $row['govshares_breakdown'] 
        : json_decode($row['govshares_breakdown'], true);
    $govshares_map = [];
    if ($govshares) foreach ($govshares as $g) $govshares_map[$g['label']] = $g['amount'];

    // Build row
    $row_data = [
        $ctr++,
        $Employee->GetEmployeeFullNameByID($row['employee_id'])['full_name'],
        trim($position_title)
    ];
    // Earnings columns
    foreach ($earning_labels as $label) {
        $row_data[] = isset($earnings_map[$label]) ? number_format($earnings_map[$label], 2) : '';
    }
    // Gross Pay
    $row_data[] = number_format($row['gross'], 2);
    // Deductions columns
    foreach ($deduction_labels as $label) {
        $row_data[] = isset($deductions_map[$label]) ? number_format($deductions_map[$label], 2) : '';
    }
    // Total Deductions
    $row_data[] = number_format($row['total_deductions'], 2);
    // Govshares columns
    foreach ($govshare_labels as $label) {
        $row_data[] = isset($govshares_map[$label]) ? number_format($govshares_map[$label], 2) : '';
    }
    // Net Pay
    $row_data[] = number_format($row['net_pay'], 2);
    // Signature
    $row_data[] = '';

    // Totals
    $total_gross += floatval($row['gross']);
    $total_deductions += floatval($row['total_deductions']);
    $total_net_pay += floatval($row['net_pay']);

    $data[] = $row_data;
}

// 5. Grand Total Row
$grand_total_row = array_fill(0, count($headers), '');
$grand_total_row[0] = 'GRAND TOTAL';
$grand_total_row[array_search('Gross Pay', $headers)] = number_format($total_gross, 2);
$grand_total_row[array_search('Total Deductions', $headers)] = number_format($total_deductions, 2);
$grand_total_row[array_search('Net Pay', $headers)] = number_format($total_net_pay, 2);
$data[] = $grand_total_row;

// Output to browser using PhpXlsxGenerator
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="payroll_register.xlsx"');
header('Cache-Control: max-age=0');

$xlsx = CodexWorld\PhpXlsxGenerator::fromArray($data);

$parentHeaderRow = 7; // Excel row 8 (1-based)
$col = 4; // Start after EIDN, Employee, Position (A=1, B=2, C=3, D=4)

// Earnings
if (count($earning_labels) > 0) {
    $startCol = $col;
    $endCol = $col + count($earning_labels) - 1;
    if ($endCol >= $startCol) {
        $xlsx->mergeCells(
            $xlsx->num2name($startCol) . $parentHeaderRow . ':' . $xlsx->num2name($endCol) . $parentHeaderRow
        );
    }
    $col = $endCol + 1;
}

// Gross Pay (no merge)
$col++;

// Deductions
if (count($deduction_labels) > 0) {
    $startCol = $col;
    $endCol = $col + count($deduction_labels) - 1;
    if ($endCol >= $startCol) {
        $xlsx->mergeCells(
            $xlsx->num2name($startCol) . $parentHeaderRow . ':' . $xlsx->num2name($endCol) . $parentHeaderRow
        );
    }
    $col = $endCol + 1;
}

// Total Deductions (no merge)
$col++;

// Gov Shares
if (count($govshare_labels) > 0) {
    $startCol = $col;
    $endCol = $col + count($govshare_labels) - 1;
    if ($endCol >= $startCol) {
        $xlsx->mergeCells(
            $xlsx->num2name($startCol) . $parentHeaderRow . ':' . $xlsx->num2name($endCol) . $parentHeaderRow
        );
    }
    $col = $endCol + 1;
}

// Set the width of the first column (A) to a smaller value, e.g., 5
$xlsx->setColWidth(1, 10); // 5 is the width, 1 is column A
// Download
$xlsx->downloadAs('payroll_register.xlsx');
exit;

?>