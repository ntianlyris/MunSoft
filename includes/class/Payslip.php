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
        $start_date = explode('_', $payroll_period)[0] ?: '';
        $end_date = explode('_', $payroll_period)[1] ?: '';
        $start = DateTime::createFromFormat('Y-m-d', $start_date);
        $end   = DateTime::createFromFormat('Y-m-d', $end_date);

        $period_coverage = $start->format('m-d-Y') . '|' . $end->format('m-d-Y');

        $basic = 0;
        $gross = 0;
        $total_other_earnings = 0;
        $net_pay = 0;

        $all_earnings_breakdown = []; // holder for all periods' earnings breakdown
        $all_deductions_breakdown = []; // holder for all periods' deductions breakdown
        
        $period_ids = $this->GetPeriodsByStartDateAndFrequency($start_date, $frequency);    // array of period ids
        foreach ($period_ids as $period_id) {

            $payroll_entry = $this->GetPayrollEntryByEmployeeAndPeriod($employee_id, $period_id['payroll_period_id']);
            
            if (!empty($payroll_entry)) {

                // Take the most recent basic & gross from the LAST payroll period
                $basic = $payroll_entry['locked_basic'];
                $gross = $payroll_entry['gross'];

                // Net pay is always accumulated
                $net_pay += floatval($payroll_entry['net_pay']);

                // Decode earnings and deductions breakdown and add to list
                $earnings_breakdown = json_decode($payroll_entry['earnings_breakdown'], true) ?? [];
                $deductions_breakdown =  json_decode($payroll_entry['deductions_breakdown'], true) ?? [];

                if (is_array($deductions_breakdown)) {
                    $all_deductions_breakdown[] = $deductions_breakdown;
                }
            }
        }
        

        // Now pass ALL period earnings breakdowns
        $final_earnings = $this->SumEarningsBreakdown($earnings_breakdown);
        $total_other_earnings = $final_earnings['total_earnings'] - $basic;
        $final_deductions = $this->SumDeductionsBreakdown($all_deductions_breakdown);
                
       
        

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
        // $earnings_periods = array of earning breakdown arrays for each payroll entry
        $combined = [];
        $total_earnings = 0;

        // Decode JSON if needed
        $earnings = $earnings_breakdown;

        foreach ($earnings as $e) {
            $id = $e['config_earning_id'];

            if (!isset($combined[$id])) {
                $combined[$id] = [
                    'config_earning_id' => $id,
                    'label' => $e['label'],
                    'amount' => 0
                ];
            }
            // Add the amount
            $combined[$id]['amount'] += floatval($e['amount']);
            $total_earnings += floatval($e['amount']);
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
    
}

?>