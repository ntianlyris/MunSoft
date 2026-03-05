<?php
include_once("DB_conn.php");
include_once("Employee.php");
include_once("Employment.php");
include_once("Payroll.php");

class Payslip extends Payroll {
    protected $db;

    public function __construct(){
        parent::__construct();
    }

    public function GeneratePayslip($employee_id, $year, $payroll_period){
        $Employee = new Employee();
        $Employment = new Employment();

        $payslip = [];

        // Get Current Active Frequency
        $active_frequency = $this->GetCurrentActiveFrequency();
        $frequency = $active_frequency['freq_code'];

        // Fetch Employee and Employment Details
        $employee_details = $Employee->GetEmployeeDetails($employee_id) ?: [];
        $employment_details = $Employment->FetchActiveEmploymentDetails($employee_id) ?: [];
        $employee_num = $employee_details['employee_id_num'] ?: '';
        $full_name = $Employee->GetEmployeeFullNameByID($employee_id)['full_name'] ?: '';

        if (!empty($employment_details['position_title'])) {
            $posTitle = $employment_details['position_title'];
            $pos = strpos($posTitle, '(');

            // If "(" exists, cut before it. Otherwise return full title.
            $position = ($pos !== false) ? substr($posTitle, 0, $pos) : $posTitle;
        }
        
        $department = $employment_details['dept_name'] ?: '';

        // Fetch Payslip Details from Payroll Entry Details
        // Parse payroll_period in format "YYYY-MM-DD_YYYY-MM-DD"
        $period_parts = explode('_', $payroll_period);
        
        // Validate that period has both start and end dates
        if (count($period_parts) < 2) {
            throw new Exception('Invalid payroll period format. Expected format: YYYY-MM-DD_YYYY-MM-DD');
        }
        
        $start_date = trim($period_parts[0]) ?: '';
        $end_date = trim($period_parts[1]) ?: '';
        
        // Validate date parsing
        $start = DateTime::createFromFormat('Y-m-d', $start_date);
        $end   = DateTime::createFromFormat('Y-m-d', $end_date);
        
        // Check if dates parsed successfully
        if ($start === false || $end === false) {
            throw new Exception('Invalid date format in payroll period. Expected Y-m-d format.');
        }

        $period_coverage = $start->format('m-d-Y') . '|' . $end->format('m-d-Y');

        $basic = 0;
        $gross = 0;
        $total_other_earnings = 0;
        $net_pay = 0;

        $all_earnings_breakdown = []; // holder for all periods' earnings breakdown
        $all_deductions_breakdown = []; // holder for all periods' deductions breakdown
        $earnings_breakdown = []; // Initialize to avoid undefined variable warning
        
        $period_ids = $this->GetPeriodsByStartDateAndFrequency($start_date, $frequency);    // array of period ids
        foreach ($period_ids as $period_id) {

            $payroll_entry = $this->GetPayrollEntryByEmployeeAndPeriod($employee_id, $period_id['payroll_period_id']);
            
            if (!empty($payroll_entry)) {

                // Take the most recent basic & gross from the LAST payroll period
                $basic = $payroll_entry['locked_basic'];
                $gross = $payroll_entry['gross'];

                // Net pay is always accumulated
                //$net_pay += floatval($payroll_entry['net_pay']);

                // Decode earnings and deductions breakdown and add to list
                $earnings_breakdown = json_decode($payroll_entry['earnings_breakdown'], true) ?? [];
                $deductions_breakdown =  json_decode($payroll_entry['deductions_breakdown'], true) ?? [];

                // Add to all_earnings_breakdown array
                if (is_array($earnings_breakdown)) {
                    $all_earnings_breakdown[] = $earnings_breakdown;
                }

                if (is_array($deductions_breakdown)) {
                    $all_deductions_breakdown[] = $deductions_breakdown;
                }
            }
        }
        

        // Now pass ALL period earnings breakdowns
        $final_earnings = $this->SumEarningsBreakdown($all_earnings_breakdown);
        $total_other_earnings = $final_earnings['total_earnings'] - $basic;
        $final_deductions = $this->SumDeductionsBreakdown($all_deductions_breakdown);
        $net_pay = $gross - $final_deductions['total_deductions'];
                      
        

        // Logic to generate payslip for the given employee, year, and payroll period
        // This is a placeholder implementation
        $payslip = [
            'employee_num' => $employee_num,
            'employee_name' => $full_name,
            'position' => $position,
            'department' => $department,
            'coverage' => $period_coverage,
            'basic' => $basic,
            'gross' => $gross,
            'other_earnings' => $total_other_earnings,
            'deductions' => $final_deductions,
            'net_pay' => $net_pay
        ];
        return $payslip;
    }

    public function SumEarningsBreakdown($earnings_breakdown) {
        // $earnings_breakdown = array of earning breakdown arrays for each payroll entry
        $combined = [];
        $total_earnings = 0;

        // Handle empty or null input
        if (!is_array($earnings_breakdown) || empty($earnings_breakdown)) {
            return ['total_earnings' => 0];
        }

        // Check if this is an array of arrays (multiple periods) or single array (single period)
        $first_element = reset($earnings_breakdown);
        $is_multi_period = is_array($first_element) && isset($first_element[0]);

        // If single period, wrap in array to process uniformly
        if (!$is_multi_period && isset($first_element['config_earning_id'])) {
            $earnings_breakdown = [$earnings_breakdown];
        }

        // Process each period's earnings breakdown
        foreach ($earnings_breakdown as $period_earnings) {
            if (!is_array($period_earnings)) {
                continue;
            }

            foreach ($period_earnings as $e) {
                if (!isset($e['config_earning_id'])) {
                    continue;
                }

                $id = $e['config_earning_id'];
                if (!isset($combined[$id])) {
                    $combined[$id] = [
                        'config_earning_id' => $id,
                        'label' => isset($e['label']) ? $e['label'] : '',
                        'amount' => 0
                    ];
                }
                // Add the amount
                $combined[$id]['amount'] += floatval($e['amount']);
                $total_earnings += floatval($e['amount']);
            }
        }

        // Re-index as normal indexed array
        $final = array_values($combined);
        // Append total earnings
        $final['total_earnings'] = $total_earnings;
        return $final;
    }

    public function SumDeductionsBreakdown($deductions_breakdown) {
        // $deductions_periods = array of deduction breakdown arrays for each payroll entry

        $combined = [];
        $total_deductions = 0;

        foreach ($deductions_breakdown as $entry) {

            // Decode JSON if needed
            $deductions = $entry;
            if (!is_array($deductions)) {
                $deductions = json_decode($deductions, true);
            }

            // Skip if still invalid
            if (!is_array($deductions)) {
                continue;
            }

            foreach ($deductions as $d) {

                $id = $d['config_deduction_id'];

                if (!isset($combined[$id])) {
                    $combined[$id] = [
                        'config_deduction_id' => $id,
                        'label' => $d['label'],
                        'amount' => 0
                    ];
                }
                // Add the amount
                $combined[$id]['amount'] += floatval($d['amount']);
                $total_deductions += floatval($d['amount']);
            }
        }
        // Re-index as normal indexed array
        $final = array_values($combined);

        // Append total deductions
        $final['total_deductions'] = $total_deductions;
        return $final;
    }

    /**
     * Get employee payslip history
     */
    function getEmployeePayslipHistory($employee_id, $limit = 6){
        include_once('../includes/class/DB_conn.php');
        $db = new DB_conn();
        
        $employee_id = $db->escape_string($employee_id);
        $query = "SELECT pe.payroll_entry_id, pe.employee_id, pe.gross, pe.total_deductions, pe.net_pay,
                pp.period_label, pp.date_start, pp.date_end, YEAR(pp.date_start) as year
                FROM payroll_entries pe
                INNER JOIN payroll_periods pp ON pe.payroll_period_id = pp.payroll_period_id
                WHERE pe.employee_id = '$employee_id'
                ORDER BY pp.date_start DESC
                LIMIT $limit";
        
        $result = $db->query($query);
        if ($result && $result->num_rows > 0) {
            $payslips = [];
            while ($row = $db->fetch_array($result)) {
                $payslips[] = $row;
            }
            return $payslips;
        }
        return null;
    }
    
}

?>