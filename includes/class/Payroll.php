<?php
include_once("DB_conn.php");

class Payroll {
    protected $db;
    private $PayrollData = [];
    private $PayrollEntryID = '';
    private $PayrollPeriodID = '';
    private $PayrollDepartmentID= '';
    private $PayrollEmploymentType = '';

    public function __construct() {
        $this->db = new DB_conn();	
    }

    public function setPayrollData($newValue){ $this->PayrollData = $newValue; }
    public function setPayrollEntryID($newValue){ $this->PayrollEntryID = $newValue; }
    public function setPayrollPeriodID($newValue){ $this->PayrollPeriodID = $newValue; }
    public function setPayrollDepartmentID($newValue){ $this->PayrollDepartmentID = $newValue; }
    public function setPayrollEmploymentType($newValue){ $this->PayrollEmploymentType = $newValue; }

    public function getPayrollEntryID(){ return $this->PayrollEntryID; }

    public function UpdatePayFrequency($freq_id,$is_active){
        $sql = "UPDATE payroll_frequencies
					SET is_active = '".$is_active."'
					WHERE payroll_freq_id ='".$freq_id."'";
		$query = $this->db->query($sql) or die($this->db->error);
		if($query){
			return true;
		}
		else {return false;}
    }

    public function GetCurrentActiveFrequency(){
        $query = "SELECT * FROM payroll_frequencies
					  WHERE is_active = '1' LIMIT 1";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if($count_row == 1){
			return $active_frequency = $this->db->fetch_array($result);
		}
		else {return false;}
    }

    public function GetPayFrequencies(){
        $query = "SELECT * FROM payroll_frequencies ORDER BY payroll_freq_id DESC";
        $result = $this->db->query($query) or die($this->db->error);
        
        $frequecies = [];
        while ($row = $this->db->fetch_array($result)) {
            $frequecies[] = $row;
        }
        return $frequecies;
    }

    public function ClosePayrollYear($year){
        $year_to_close = $year;
        $sql = "INSERT INTO payroll_year_controls (year, is_closed)
					VALUES ('".$year_to_close."', 1)
					ON DUPLICATE KEY UPDATE is_closed = 1";
		$query = $this->db->query($sql) or die($this->db->error);
		if($query){
			return true;
		}
		else {return false;}
    }

    public function CheckLastYearIsClosed($lastYear){
        $query = "SELECT is_closed FROM payroll_year_controls WHERE year = '$lastYear'";
        $result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if($count_row == 1){
			return $last_year = $this->db->fetch_array($result);
		}
		else {return false;}
    }

    public function GetEmployedEmployeesByDept($dept_id){         //--- fetch all employees that has current employment set with their position and departments needed for payroll computation
        $employees = [];
        $query = "SELECT a.employee_id, a.employee_id_num,
                    CONCAT(a.lastname, ', ', a.firstname, ' ' ,IF(LENGTH(a.middlename) > 0, CONCAT(LEFT(a.middlename, 1), '. '), ''), ' ', a.extension) AS full_name,
                    c.position_title, d.dept_id, d.dept_title
                    FROM employees_tbl a
                    INNER JOIN employee_employments_tbl b
                    ON a.employee_id = b.employee_id
                    INNER JOIN positions_tbl c
                    ON b.position_id = c.position_id
                    INNER JOIN departments_tbl d
                    ON c.dept_id = d.dept_id
                    WHERE b.employment_status = 1 
                    AND c.dept_id = '$dept_id'
                    ORDER BY c.salary_grade DESC";
		$result = $this->db->query($query) or die($this->db->error);
		if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $employees[] = $row;
            }
        }
        return $employees;
    }

    public function GetEmployeeListForPayrollByDept($dept_id,$emp_type){         //--- fetch all employees that has current employment set with their position and departments needed for payroll computation
        $employees = [];
        $query = "SELECT a.employee_id, a.employee_id_num,
                    CONCAT(a.lastname, ', ', a.firstname, ' ' ,IF(LENGTH(a.middlename) > 0, CONCAT(LEFT(a.middlename, 1), '. '), ''), ' ', a.extension) AS full_name,
                    b.employment_id,
                    c.position_title, d.dept_id, d.dept_title
                    FROM employees_tbl a
                    INNER JOIN employee_employments_tbl b
                    ON a.employee_id = b.employee_id
                    INNER JOIN positions_tbl c
                    ON b.position_id = c.position_id
                    INNER JOIN departments_tbl d
                    ON c.dept_id = d.dept_id
                    WHERE a.include_in_payroll = '1'    /*--- only include employees marked for payroll ---*/
                    AND b.employment_status = 1 
                    AND c.dept_id = '$dept_id'
                    AND b.employment_type = '$emp_type'
                    ORDER BY c.salary_grade DESC";
		$result = $this->db->query($query) or die($this->db->error);
		if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $employees[] = $row;
            }
        }
        return $employees;
    }

    public function GetEmployeeListForPayroll(){         //--- fetch all employees that has current employment set with their position and departments needed for payroll computation
        $employees = [];
        $query = "SELECT a.employee_id, a.employee_id_num, a.include_in_payroll,
                    CONCAT(a.lastname, ', ', a.firstname, ' ' ,IF(LENGTH(a.middlename) > 0, CONCAT(LEFT(a.middlename, 1), '. '), ''), ' ', a.extension) AS full_name,
                    c.position_title, d.dept_id, d.dept_title
                    FROM employees_tbl a
                    INNER JOIN employee_employments_tbl b
                    ON a.employee_id = b.employee_id
                    INNER JOIN positions_tbl c
                    ON b.position_id = c.position_id
                    INNER JOIN departments_tbl d
                    ON c.dept_id = d.dept_id
                    WHERE b.employment_status = 1 
                    ORDER BY full_name ASC";
		$result = $this->db->query($query) or die($this->db->error);
		if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $employees[] = $row;
            }
        }
        return $employees;
    }

    public function GetEmployeeEarnings($employee_id, $start_date, $end_date) {
        $query = "
            SELECT a.*, b.*, c.*
            FROM employee_earnings a
            INNER JOIN employee_earnings_components b 
                ON a.employee_earning_id = b.employee_earning_id
            INNER JOIN config_earnings c 
                ON b.config_earning_id = c.config_earning_id
            INNER JOIN (
                SELECT earning_code, MAX(a2.effective_date) AS latest_effective
                FROM employee_earnings a2
                INNER JOIN employee_earnings_components b2 
                    ON a2.employee_earning_id = b2.employee_earning_id
                INNER JOIN config_earnings c2 
                    ON b2.config_earning_id = c2.config_earning_id
                WHERE a2.employee_id = $employee_id 
                AND a2.effective_date <= '$end_date'
                AND (a2.end_date IS NULL OR a2.end_date >= '$start_date')
                GROUP BY c2.earning_code
            ) latest ON c.earning_code = latest.earning_code 
                    AND a.effective_date = latest.latest_effective
            WHERE a.employee_id = $employee_id 
            AND a.effective_date <= '$end_date'
            AND (a.end_date IS NULL OR a.end_date >= '$start_date')
            ORDER BY c.config_earning_id ASC
        ";

        $result = $this->db->query($query) or die($this->db->error);

        $earnings = [];
        while ($row = $this->db->fetch_array($result)) {
            $earnings[] = $row;
        }
        return $earnings;
    }

    public function GetEmployeeDeductions($employee_id, $start_date, $end_date) {
        $query = "
            SELECT a.*, b.*, c.*
            FROM employee_deductions a
            INNER JOIN employee_deductions_components b 
                ON a.employee_deduction_id = b.employee_deduction_id
            INNER JOIN config_deductions c 
                ON b.config_deduction_id = c.config_deduction_id
            INNER JOIN (
                SELECT deduct_code, MAX(a2.effective_date) AS latest_effective
                FROM employee_deductions a2
                INNER JOIN employee_deductions_components b2 
                    ON a2.employee_deduction_id = b2.employee_deduction_id
                INNER JOIN config_deductions c2 
                    ON b2.config_deduction_id = c2.config_deduction_id
                WHERE a2.employee_id = $employee_id 
                AND a2.effective_date <= '$end_date'
                AND (a2.end_date IS NULL OR a2.end_date >= '$start_date')
                GROUP BY c2.deduct_code
            ) latest ON c.deduct_code = latest.deduct_code 
                    AND a.effective_date = latest.latest_effective
            WHERE a.employee_id = $employee_id 
            AND a.effective_date <= '$end_date'
            AND (a.end_date IS NULL OR a.end_date >= '$start_date')
            ORDER BY c.config_deduction_id ASC
        ";

        $result = $this->db->query($query) or die($this->db->error);

        $deductions = [];
        while ($row = $this->db->fetch_array($result)) {
            $deductions[] = $row;
        }
        return $deductions;
    }

    public function GetEmployeeGovShares($employee_id, $start_date, $end_date) {
        $employee_id = (int)$employee_id; // sanitize

        $start_date = $this->db->escape_string($start_date);
        $end_date   = $this->db->escape_string($end_date);

        $query = "
            SELECT a.*, b.*
            FROM employee_govshares a
            INNER JOIN govshares b 
                ON a.govshare_id = b.govshare_id
            INNER JOIN (
                SELECT b2.deduction_type_id, MAX(a2.effective_start) AS latest_effective
                FROM employee_govshares a2
                INNER JOIN govshares b2 
                    ON a2.govshare_id = b2.govshare_id
                WHERE a2.employee_id = {$employee_id}
                AND a2.effective_end IS NULL
                AND a2.effective_start <= '{$end_date}'
                AND (a2.effective_end IS NULL OR a2.effective_end >= '{$start_date}')
                GROUP BY b2.deduction_type_id
            ) latest 
                ON b.deduction_type_id = latest.deduction_type_id 
            AND a.effective_start = latest.latest_effective
            WHERE a.employee_id = {$employee_id}
            AND a.effective_end IS NULL
            AND a.effective_start <= '{$end_date}'
            AND (a.effective_end IS NULL OR a.effective_end >= '{$start_date}')
            AND b.active = 1
            ORDER BY b.govshare_id ASC
        ";

        $result = $this->db->query($query) or die($this->db->error);

        $govshares = [];
        while ($row = $this->db->fetch_array($result)) {
            $govshares[] = $row;
        }
        return $govshares;
    }

    // ==========================================================
    // Payroll Period Functions
    // ==========================================================

    public function generateAndInsertPayPeriodsToDB() {
        $temp = false;
        $active_frequency = $this->GetCurrentActiveFrequency();
        $frequency = $active_frequency['freq_code'];

        $currentYear = date('Y');
        $currentMonth = date('n');

        $lastYear_isClosed = '';
        $lastYear = $currentYear - 1;
        $useYear = $currentYear;

        if ($currentMonth == 1) {
            $lastYear_isClosed = $this->CheckLastYearIsClosed($lastYear);
            if (!$lastYear_isClosed || $lastYear_isClosed['is_closed'] == 0) {
                $useYear = $lastYear;
            }
        }

        for ($month = 1; $month <= 12; $month++) {
            $monthText = date("F", mktime(0, 0, 0, $month, 1));
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $useYear);

            if ($frequency == 'monthly') {
                $start = "$useYear-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
                $end = "$useYear-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-$daysInMonth";
                $label = "$monthText 1–$daysInMonth, $useYear";

                $stmt = "INSERT IGNORE INTO payroll_periods (date_start, date_end, period_label, frequency) 
                            VALUES ('".$start."', '".$end."', '".$label."', '".$frequency."')";

                $query = $this->db->query($stmt) or die($this->db->error);

                $temp = true;

            } else { // semi-monthly
                $start1 = "$useYear-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
                $end1 = "$useYear-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-15";
                $label1 = "$monthText 1–15, $useYear";

                $start2 = "$useYear-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-16";
                $end2 = "$useYear-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-$daysInMonth";
                $label2 = "$monthText 16–$daysInMonth, $useYear";

                $stmt1 = "INSERT IGNORE INTO payroll_periods (date_start, date_end, period_label, frequency) 
                            VALUES ('".$start1."', '".$end1."', '".$label1."', '".$frequency."')";
                $query1 = $this->db->query($stmt1) or die($this->db->error);

                $stmt2 = "INSERT IGNORE INTO payroll_periods (date_start, date_end, period_label, frequency) 
                            VALUES ('".$start2."', '".$end2."', '".$label2."', '".$frequency."')";
                $query2 = $this->db->query($stmt2) or die($this->db->error);

                $temp = true;
            }
        }
        return $temp;
    }

    public function GetPayPeriods($frequency){
        $currentYear = date('Y');

        $lastYear_isClosed = '';
        $lastYear = $currentYear - 1;
        $useYear = $currentYear;

        $lastYear_isClosed = $this->CheckLastYearIsClosed($lastYear);
        if (!$lastYear_isClosed || $lastYear_isClosed['is_closed'] == 0) {
            $useYear = $lastYear;
        }

        $query = "SELECT * FROM payroll_periods 
                    WHERE frequency = '$frequency' 
                    AND (YEAR(date_start) = $useYear OR YEAR(date_end) = $useYear)
                    ORDER BY date_start ASC";
        $result = $this->db->query($query) or die($this->db->error);
        
        $periods = [];
        while ($row = $this->db->fetch_array($result)) {
            $periods[] = $row;
        }
        return $periods;
    }

    public function GetPayrollPeriodByDates($start, $end){
        $stmt = "SELECT payroll_period_id FROM payroll_periods WHERE date_start = '$start' AND date_end = '$end' LIMIT 1";
        $result = $this->db->query($stmt) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if($count_row == 1){
			return $row = $this->db->fetch_array($result);
		}
		else {return false;}
    }

    public function GetPayrollPeriodsByYear($year,$frequency) {
        $query = "SELECT payroll_period_id, period_label, date_start, date_end 
                    FROM payroll_periods 
                    WHERE YEAR(date_start) = '$year' 
                    AND frequency = '$frequency'
                    ORDER BY date_start ASC";
        $result = $this->db->query($query) or die($this->db->error);
        $periods = [];
        while ($row = $this->db->fetch_array($result)) {
            $periods[] = $row;
        }
        return $periods;
    }

    public function GetPeriodDatesByID($payroll_period_id) {
        $query = "SELECT date_start, date_end FROM payroll_periods WHERE payroll_period_id = '$payroll_period_id' LIMIT 1";
        $result = $this->db->query($query) or die($this->db->error);
        if ($this->db->num_rows($result) == 1) {
            return $this->db->fetch_array($result);
        } else {
            return false;
        }
    }

    /*public function LockPayrollPeriod($payroll_period_id){
        $sql = "UPDATE payroll_periods
                    SET is_locked = '1'
                    WHERE payroll_period_id ='".$payroll_period_id."'";
        $query = $this->db->query($sql) or die($this->db->error);
        if($query){
            return true;
        }
        else {return false;}
    }*/ //For deletion

    public function GetLastLockedPayrollPeriodByEmployee($employee_id){
        $query = "SELECT * FROM payroll_periods a
                        INNER JOIN payroll_entries b
                        ON a.payroll_period_id = b.payroll_period_id
                        WHERE b.locked_period = '1' 
                        AND b.employee_id = '$employee_id' 
                        ORDER BY a.payroll_period_id DESC LIMIT 1";
        $result = $this->db->query($query) or die($this->db->error);
        $count_row = $this->db->num_rows($result);
        if($count_row == 1){
            return $last_locked_period = $this->db->fetch_array($result);
        }
        else {return false;}
    }

    public function IsSecondHalfOfMonth($date_start){
        $active_frequency = $this->GetCurrentActiveFrequency();
        $payroll_frequency = $active_frequency['freq_code'];

        $period_day = date('d', strtotime($date_start));
        $is_second_half = ($payroll_frequency == 'semi-monthly' && intval($period_day) > 15);

        return $is_second_half;
    }

    public function GetPeriodsByStartDateAndFrequency($start_date, $frequency) {

        // Extract month and year from the given date
        $stmt = "SELECT MONTH('$start_date') AS m, YEAR('$start_date') AS y";
        $result = $this->db->query($stmt) or die($this->db->error);
        $row = $this->db->fetch_array($result);
        $month = $row['m'];
        $year  = $row['y'];

        // Now fetch periods based on frequency
        $stmt2 = "
            SELECT payroll_period_id, period_label, date_start, date_end, frequency
            FROM payroll_periods
            WHERE frequency = '$frequency'
            AND MONTH(date_start) = '$month'
            AND YEAR(date_end) = '$year'
            ORDER BY date_start
        ";
        $result2 = $this->db->query($stmt2) or die($this->db->error);

        $rows = [];
        while ($row2 = $this->db->fetch_array($result2)) {
            $rows[] = $row2;
        }
        return $rows;
    }

    // ==========================================================
    // Payroll Computation Functions
    // ==========================================================

    public function ComputePayrollOfEmployees($employees_arr, $start_date, $end_date){
        $employeeData = [];

        // Determine payroll frequency
        $active_frequency = $this->GetCurrentActiveFrequency();
        $payroll_frequency = $active_frequency['freq_code'];

        // Detect if this is second half of the month
        $is_second_half = $this->IsSecondHalfOfMonth($start_date);

        foreach ($employees_arr as $employee) {
            $employee_id = $employee['employee_id'];
            $employee_id_num = $employee['employee_id_num'];
            $employment_id = $employee['employment_id'];
            $full_name = $employee['full_name'];
            $position = $employee['position_title'];

            // Filter earnings, deductions, govshares for this pay period
            $earnings = $this->GetEmployeeEarnings($employee_id, $start_date, $end_date);
            $deductions = $this->GetEmployeeDeductions($employee_id, $start_date, $end_date);
            // Force $end_date to the last day of its month
            $govshare_end_date = date("Y-m-t", strtotime($end_date));
            $govshares = $this->GetEmployeeGovShares($employee_id, $start_date, $govshare_end_date);

            $earnings_list = [];
            $deductions_list = [];
            $govshares_list = [];
            
            $gross = 0;
            $total_deductions = 0;
            $display_total_deductions = 0; 
            $net = 0;   
            $locked_rate = 0;

            if (!empty($earnings)) {
                $locked_rate = floatval($earnings[0]['locked_rate']);
                foreach ($earnings as $earn) {
                    $amount = floatval($earn['earning_comp_amt']);
                    $earnings_list[] = [
                        'config_earning_id' => $earn['config_earning_id'],          // <-- include config earning ID as reference to what current active earning it was based
                        'earning_comp_id' => $earn['earning_component_id'],         // <-- include employee earning component ID as reference to what current active earning it was based
                        'label' => $earn['earning_code'],
                        'amount' => $amount
                    ];
                    $gross += $amount;
                }
            }

            // Apply deductions only if NOT second half of semi-monthly
            if (!empty($deductions)) {
                foreach ($deductions as $deduct) {
                    $amount = floatval($deduct['deduction_comp_amt']);
                    $total_deductions += $amount;
                    if (!$is_second_half) {
                        $deductions_list[] = [
                            'config_deduction_id' => $deduct['config_deduction_id'],    // <-- include config deduction ID as reference to what current active deduction it was based
                            'deduct_comp_id' => $deduct['deduction_component_id'],      // <-- include employee deduction ID as reference to what current active deduction it was based
                            'label' => $deduct['deduct_code'],
                            'amount' => $amount
                        ];
                        $display_total_deductions += $amount;   // display total deductions during the 1st half only
                    }
                    else{
                        $deductions_list = []; // empty array, no deductions applied
                        $display_total_deductions = 0; // no deductions total deductions must be displayed during 2nd half
                    } 
                }
            }

            if (!empty($govshares)) {
                foreach ($govshares as $gs) {
                    $amount = floatval($gs['govshare_amount']);
                    if (!$is_second_half) {
                        $govshares_list[] = [
                            'govshare_id' => $gs['govshare_id'],                    // <-- include govshare ID as reference to what current active govshare it was based
                            'emp_govshare_id' => $gs['employee_govshare_id'],       // <-- include employee govshare ID as reference to what current active govshare it was based
                            'label' => $gs['govshare_code'],
                            'amount' => $amount
                        ];
                    }
                    else{
                        $govshares_list = []; // empty array, no govshares applied
                    }
                }
            }

            // Compute net pay
            if ($payroll_frequency == 'semi-monthly') {
                $net = ($gross - $total_deductions) / 2;
            } else {
                $net = $gross - $total_deductions;
            }

            $employeeData[] = [
                'employee_id' => $employee_id,
                'id_num' => $employee_id_num,
                'employment_id' => $employment_id,
                'name' => $full_name,
                'position' => $position,
                'locked_rate' => $locked_rate,
                'gross' => $gross,
                'deductions' => $display_total_deductions,
                'net' => $net,
                'earnings' => $earnings_list,
                'deductions_list' => $deductions_list,
                'govshares_list' => $govshares_list
            ];
        }
        return $employeeData;
    }

    public function SavePayrollEntry(){
        include_once("Department.php");
        $PayrollDepartment = new Department();

        $department_name = '';
        $return_arr = [];
        $payroll_data = $this->PayrollData;
        $payroll_period_id = $this->PayrollPeriodID;
        $payroll_dept_id = $this->PayrollDepartmentID;
        $department_name = $PayrollDepartment->GetDepartmentDetails($payroll_dept_id)['dept_name'];
        $payroll_employment_type = $this->PayrollEmploymentType;
        
        $success_count = 0;

        //--- Check if payroll period already exist ---//
        $query = "SELECT * FROM payroll_entries a
                    INNER JOIN employee_employments_tbl b
                    ON a.employment_id = b.employment_id
                    WHERE a.payroll_period_id = '$payroll_period_id'
                    AND a.dept_id = '$payroll_dept_id'
                    AND b.employment_type = '$payroll_employment_type'";
        $result = $this->db->query($query) or die($this->db->error);
        $count_row = $this->db->num_rows($result);
        
        if($count_row == 0){
            if (is_array($payroll_data) || $payroll_data !== null) {
                foreach ($payroll_data as $entry) {
                    $employee_id = $entry['employee_id'];
                    $employment_id = $entry['employment_id'];
                    $locked_rate = floatval($entry['locked_rate']);
                    $gross = floatval($entry['gross']);
                    $deductions = floatval($entry['deductions']);
                    $net = floatval($entry['net']);
                    $earnings_json = json_encode($entry['earnings']);
                    $deductions_json = json_encode($entry['deductions_list']);
                    $govshares_json = json_encode($entry['govshares_list']);

                    $deductions_list = $entry['deductions_list'];   
                    $govshares_list = $entry['govshares_list'];

                    $earnings_json = $this->db->escape_string($earnings_json);
                    $deductions_json = $this->db->escape_string($deductions_json);

                    
                        $query = "INSERT INTO payroll_entries 
                                (employee_id, employment_id, payroll_period_id, locked_period, dept_id, locked_basic, gross, total_deductions, net_pay, earnings_breakdown, deductions_breakdown, govshares_breakdown, emp_type_stamp, created_at) 
                                VALUES (
                                    $employee_id, 
                                    $employment_id,
                                    $payroll_period_id, 
                                    1,
                                    $payroll_dept_id,
                                    $locked_rate, 
                                    $gross,
                                    $deductions, 
                                    $net, 
                                    '$earnings_json', 
                                    '$deductions_json', 
                                    '$govshares_json',
                                    '$payroll_employment_type',
                                    NOW()
                                )";
                        $result = $this->db->query($query) or die($this->db->error);
                        if ($result) {
                            $payroll_entry_id = $this->db->last_id();
                            $this->SavePayrollDeductions($payroll_entry_id, $deductions_list);
                            $this->SavePayrollGovShares($payroll_entry_id, $govshares_list);
                            $success_count++;
                        }              
                }
                $return_arr = [
                            'status' => 'success',
                            'message' => "$success_count payroll records saved.",
                            'saved' => $success_count
                        ];
            }
            else{
                $return_arr = [
                            'status' => 'not_set',
                            'message' => "Payroll Data not set.",
                            'saved' => $success_count
                        ];
            }
        }
        else{
            $return_arr = ['status' => 'existing',
                            'message' => "Payroll for the payroll period and employment type already exist.",
                            'saved' => $success_count
                        ];
        }      
        return $return_arr;
    }

    
    /**
     * Save deductions/govshares list to payrolls_deductions/govshares table
     * @param int $payroll_entry_id
     * @param array $deductions_list/goshares_list
     */
    public function SavePayrollDeductions($payroll_entry_id, $deductions_list) {
        $temp = false;
        if (!is_array($deductions_list) || empty($deductions_list)) {
            return $temp;
        }

        foreach ($deductions_list as $deduction) {
            $deduct_comp_id = isset($deduction['deduct_comp_id']) ? intval($deduction['deduct_comp_id']) : 0;
            $amount = isset($deduction['amount']) ? floatval($deduction['amount']) : 0;

            $query = "INSERT INTO payroll_deductions 
                        (payroll_entry_id, deduction_component_id, payroll_deduct_amount, created_at)
                        VALUES (
                            '$payroll_entry_id',
                            '$deduct_comp_id',
                            '$amount',
                            NOW()
                        )";
            $result = $this->db->query($query);
            if ($result) {
                $temp = true;
            }
        }
        return $temp;
    }

    public function SavePayrollGovShares($payroll_entry_id, $govshares_list) {
        $temp = false;
        if (!is_array($govshares_list) || empty($govshares_list)) {
            return $temp;
        }

        foreach ($govshares_list as $govshare) {
            $emp_govshare_id = isset($govshare['emp_govshare_id']) ? intval($govshare['emp_govshare_id']) : 0;
            $amount = isset($govshare['amount']) ? floatval($govshare['amount']) : 0;

            $query = "INSERT INTO payroll_govshares 
                        (payroll_entry_id, employee_govshare_id, payroll_govshare_amount, created_at)
                        VALUES (
                            '$payroll_entry_id',
                            '$emp_govshare_id',
                            '$amount',
                            NOW()
                        )";
            $result = $this->db->query($query);
            if ($result) {
                $temp = true;
            }
        }
        return $temp;
    }

    // ==========================================================
    // Printing Payroll Reports
    // ==========================================================

    public function FetchPayrollByPayPeriodAndDept($period_id, $dept_id, $emp_type){
        $stmt = "SELECT * FROM payroll_entries 
                    WHERE payroll_period_id = '$period_id' 
                    AND dept_id = '$dept_id'
                    AND emp_type_stamp = '$emp_type' 
                    ORDER BY payroll_entry_id";
        $result = $this->db->query($stmt) or die($this->db->error);
        
        $payrolls = [];
        while ($row = $this->db->fetch_array($result)) {
            $payrolls[] = $row;
        }
        return $payrolls;
    }

    public function FetchConfigEarningsIDandCode(){
        $query = "SELECT config_earning_id, earning_code FROM config_earnings ORDER BY earning_acct_code ASC";
        $result = $this->db->query($query) or die($this->db->error);
        
        $earnings = [];
        while ($row = $this->db->fetch_array($result)) {
            $earnings[] = $row;
        }
        return $earnings;
    }

    public function FetchConfigDeductionsIDandCode(){
        $query = "SELECT config_deduction_id, deduct_code FROM config_deductions ORDER BY deduct_acct_code ASC";
        $result = $this->db->query($query) or die($this->db->error);
        
        $deductions = [];
        while ($row = $this->db->fetch_array($result)) {
            $deductions[] = $row;
        }
        return $deductions;
    }

    public function FetchGovSharesIDandCode(){
        $query = "SELECT govshare_id, govshare_code FROM govshares ORDER BY govshare_acctcode ASC";
        $result = $this->db->query($query) or die($this->db->error);
        
        $govshares = [];
        while ($row = $this->db->fetch_array($result)) {
            $govshares[] = $row;
        }
        return $govshares;
    }

    // ==========================================================
    // End Printing Payroll Reports
    // ==========================================================

    // Get list of distinct years from payroll periods
    public function GetPayrollYears() {
        $query = "SELECT DISTINCT YEAR(date_start) AS payroll_year
                    FROM payroll_periods
                    ORDER BY payroll_year DESC";
        $result = $this->db->query($query) or die($this->db->error);
        $years = [];
        while ($row = $this->db->fetch_array($result)) {
            $years[] = $row['payroll_year'];
        }
        return $years;
    }

    // ==========================================================
    // Toggle Include in Payroll for Employees
    // ==========================================================

    public function UpdateIncludeInPayroll($employee_id, $include_in_payroll){
        $sql = "UPDATE employees_tbl
                    SET include_in_payroll = '".$include_in_payroll."'
                    WHERE employee_id ='".$employee_id."'";
        $query = $this->db->query($sql) or die($this->db->error);
        if($query){
            return true;
        }
        else {return false;}
    }

    // ==========================================================
    // Get Payroll Entry By Employee and Period
    // ==========================================================

    public function GetPayrollEntryByEmployeeAndPeriod($employee_id, $payroll_period_id){
        $stmt = "SELECT * FROM payroll_entries 
                    WHERE employee_id = '$employee_id' 
                    AND payroll_period_id = '$payroll_period_id' 
                    LIMIT 1";
        $result = $this->db->query($stmt) or die($this->db->error);
        $count_row = $this->db->num_rows($result);
        if($count_row == 1){
            return $row = $this->db->fetch_array($result);
        }
        else {return false;}
    }

}

?>