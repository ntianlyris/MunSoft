<?php
include_once("DB_conn.php");

class Remittance {
    protected $db;

    public function __construct() {
        $this->db = new DB_conn();	
    }

    public function GetRemittanceByID($remittance_id) {
        $remittance_id = $this->db->escape_string(trim($remittance_id));
        $sql = "SELECT * FROM remittances WHERE remittance_id = '$remittance_id' LIMIT 1";
        $result = $this->db->query($sql) or die($this->db->error);
        if ($this->db->num_rows($result) == 0) {
            return null; // No remittance found
        }
        else {
            $row = $this->db->fetch_array($result);
            return $row;
        }
    }

    public function GetRemittancePhilHealth($year, $period) {        //  public function GetRemittancePhilHealth($year, $period, $dept)
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";       --> in case department field is added in payroll_entries table in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    CONCAT(
                        b.lastname, ', ', b.firstname, ' ',
                        IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), 
                        ' ', b.extension
                    ) AS employee,
                    a.locked_basic,
                    b.employee_id,
                    b.philhealth_no,
                    emp.employment_status,
                    pos.position_title,
                    COALESCE(ded.deduction_type_id, gov.deduction_type_id) AS deduction_type_id, -- << include this
                    ded.config_deduction_id,  -- Add this line
                    gov.govshare_id,         -- Add this line
                    SUM(IFNULL(ded.employee_share,0)) AS employee_share,
                    SUM(IFNULL(gov.employer_share,0)) AS employer_share,
                    SUM(IFNULL(ded.employee_share,0) + IFNULL(gov.employer_share,0)) AS total
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id

                -- Employee share subquery
                LEFT JOIN (
                    SELECT 
                        c.payroll_entry_id, 
                        f.deduction_type_id, -- << capture deduction_type_id here
                        e.config_deduction_id, -- Added config_deduction_id
                        SUM(c.payroll_deduct_amount) AS employee_share
                    FROM payroll_deductions c
                    LEFT JOIN employee_deductions_components d 
                        ON c.deduction_component_id = d.deduction_component_id
                    LEFT JOIN config_deductions e 
                        ON d.config_deduction_id = e.config_deduction_id AND e.is_employee_share = 1
                    LEFT JOIN deduction_types f 
                        ON e.deduction_type_id = f.deduction_type_id
                    WHERE f.deduction_type_code = 'PHIC'
                    GROUP BY c.payroll_entry_id, f.deduction_type_id, e.config_deduction_id
                ) ded ON ded.payroll_entry_id = a.payroll_entry_id


                -- Employer share subquery
                LEFT JOIN (
                    SELECT 
                        g.payroll_entry_id, 
                        k.deduction_type_id, -- << capture deduction_type_id here
                        i.govshare_id, -- Added govshare_id
                        SUM(g.payroll_govshare_amount) AS employer_share
                    FROM payroll_govshares g
                    LEFT JOIN employee_govshares h ON g.employee_govshare_id = h.employee_govshare_id
                    LEFT JOIN govshares i ON h.govshare_id = i.govshare_id
                    LEFT JOIN deduction_types k ON i.deduction_type_id = k.deduction_type_id
                    WHERE k.deduction_type_code = 'PHIC'
                    GROUP BY g.payroll_entry_id, k.deduction_type_id, i.govshare_id
                ) gov ON gov.payroll_entry_id = a.payroll_entry_id

                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'

                GROUP BY b.employee_id
                ORDER BY b.lastname ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $remittances_phic = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_phic[] = $row;
        }
        return $remittances_phic;
    }

    public function GetRemittanceBIRTax($year, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    CONCAT(
                        b.lastname, ', ', b.firstname, ' ',
                        IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), 
                        ' ', b.extension
                    ) AS employee,
                    a.locked_basic,
                    b.employee_id,
                    b.tin,
                    pos.position_title,
                    ded.deduction_type_id, -- << include deduction_type_id
                    ded.config_deduction_id,  -- Add this line
                    SUM(IFNULL(ded.employee_share,0)) AS amount
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id

                LEFT JOIN (
                    SELECT 
                        c.payroll_entry_id, 
                        f.deduction_type_id, -- << capture deduction_type_id
                        e.config_deduction_id, -- Added config_deduction_id
                        SUM(c.payroll_deduct_amount) AS employee_share
                    FROM payroll_deductions c
                    LEFT JOIN employee_deductions_components d 
                        ON c.deduction_component_id = d.deduction_component_id
                    LEFT JOIN config_deductions e 
                        ON d.config_deduction_id = e.config_deduction_id
                    LEFT JOIN deduction_types f 
                        ON e.deduction_type_id = f.deduction_type_id
                    WHERE f.deduction_type_code = 'BIR'
                    GROUP BY c.payroll_entry_id, f.deduction_type_id
                ) ded ON ded.payroll_entry_id = a.payroll_entry_id

                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'

                GROUP BY b.employee_id
                ORDER BY b.lastname ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $remittances_tax = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_tax[] = $row;
        }
        return $remittances_tax;
    }

    // GSIS Remittance (Employee Share - Statutory, Employer Share) per employee with total per share and total amount
    public function GetRemittanceGSIS($year, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    b.employee_id,
                    CONCAT(
                        b.lastname, ', ', b.firstname, ' ',
                        IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), 
                        ' ', b.extension
                    ) AS employee,
                    a.locked_basic,
                    b.employee_id,
                    b.gsis_bp,
                    pos.position_title,
                    ded.deduction_type_id, -- include deduction_type_id
                    ded.config_deduction_id,  -- Add this line
                    gov.govshare_id,         -- Add this line
                    SUM(IFNULL(ded.employee_share,0)) AS employee_share,
                    SUM(IFNULL(gov.employer_share,0)) AS employer_share,
                    SUM(IFNULL(ded.employee_share,0) + IFNULL(gov.employer_share,0)) AS total_amount
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id
                
                -- Employee Share (GSIS Statutory only)
                LEFT JOIN (
                    SELECT 
                        c.payroll_entry_id, 
                        f.deduction_type_id, -- capture deduction_type_id
                        e.config_deduction_id, -- Added config_deduction_id
                        SUM(c.payroll_deduct_amount) AS employee_share
                    FROM payroll_deductions c
                    LEFT JOIN employee_deductions_components d 
                        ON c.deduction_component_id = d.deduction_component_id
                    LEFT JOIN config_deductions e 
                        ON d.config_deduction_id = e.config_deduction_id
                    LEFT JOIN deduction_types f 
                        ON e.deduction_type_id = f.deduction_type_id
                    WHERE f.deduction_type_code = 'GSIS'
                    AND e.deduct_category = 'STATUTORY'
                    AND e.is_employee_share = 1
                    GROUP BY c.payroll_entry_id, f.deduction_type_id
                ) ded ON ded.payroll_entry_id = a.payroll_entry_id

                -- Employer Share (GSIS only)
                LEFT JOIN (
                    SELECT 
                        g.payroll_entry_id, 
                        i.govshare_id, -- Added govshare_id
                        SUM(g.payroll_govshare_amount) AS employer_share
                    FROM payroll_govshares g
                    LEFT JOIN employee_govshares h 
                        ON g.employee_govshare_id = h.employee_govshare_id
                    LEFT JOIN govshares i 
                        ON h.govshare_id = i.govshare_id
                    LEFT JOIN deduction_types k 
                        ON i.deduction_type_id = k.deduction_type_id
                    WHERE k.deduction_type_code = 'GSIS'
                    AND i.govshare_code = 'L_R'
                    GROUP BY g.payroll_entry_id
                ) gov ON gov.payroll_entry_id = a.payroll_entry_id

                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'
                
                GROUP BY b.employee_id
                ORDER BY b.lastname ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $remittances_gsis = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_gsis[] = $row;
        }
        return $remittances_gsis;
    }

    // GSIS Remittance (Employee Share - Statutory, Employer Share) per employee with total per share and total amount
    public function GetRemittanceGSISECC($year, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    b.employee_id,
                    CONCAT(
                        b.lastname, ', ', b.firstname, ' ',
                        IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), 
                        ' ', b.extension
                    ) AS employee,
                    a.locked_basic,
                    b.employee_id,
                    b.gsis_bp,
                    pos.position_title,
                    gov.deduction_type_id, -- include deduction_type_id
                    gov.govshare_id,         -- Add this line
                    SUM(IFNULL(gov.employer_share,0)) AS employer_share
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id

                -- Employer Share (GSIS ECC only)
                LEFT JOIN (
                    SELECT 
                        g.payroll_entry_id, 
                        k.deduction_type_id, -- capture deduction_type_id
                        i.govshare_id, -- Added govshare_id
                        SUM(g.payroll_govshare_amount) AS employer_share
                    FROM payroll_govshares g
                    LEFT JOIN employee_govshares h 
                        ON g.employee_govshare_id = h.employee_govshare_id
                    LEFT JOIN govshares i 
                        ON h.govshare_id = i.govshare_id
                    LEFT JOIN deduction_types k 
                        ON i.deduction_type_id = k.deduction_type_id
                    WHERE k.deduction_type_code = 'GSIS'
                    AND i.govshare_code = 'ECC'
                    GROUP BY g.payroll_entry_id, k.deduction_type_id
                ) gov ON gov.payroll_entry_id = a.payroll_entry_id

                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'
                
                GROUP BY b.employee_id
                ORDER BY b.lastname ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $remittances_gsis = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_gsis[] = $row;
        }
        return $remittances_gsis;
    }

    public function GetRemittancePagibig($year, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    b.employee_id,
                    CONCAT(
                        b.lastname, ', ', b.firstname, ' ',
                        IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), 
                        ' ', b.extension
                    ) AS employee,
                    a.locked_basic,
                    b.employee_id,
                    b.pagibig_mid,
                    pos.position_title,
                    ded.deduction_type_id, -- include deduction_type_id
                    ded.config_deduction_id,  -- Add this line
                    gov.govshare_id,         -- Add this line
                    SUM(IFNULL(ded.employee_share,0)) AS employee_share,
                    SUM(IFNULL(gov.employer_share,0)) AS employer_share,
                    SUM(IFNULL(ded.employee_share,0) + IFNULL(gov.employer_share,0)) AS total
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id

                -- Employee Share (Pag-IBIG)
                LEFT JOIN (
                    SELECT 
                        c.payroll_entry_id, 
                        f.deduction_type_id, -- capture deduction_type_id
                        e.config_deduction_id, -- Added config_deduction_id
                        SUM(c.payroll_deduct_amount) AS employee_share
                    FROM payroll_deductions c
                    LEFT JOIN employee_deductions_components d 
                        ON c.deduction_component_id = d.deduction_component_id
                    LEFT JOIN config_deductions e 
                        ON d.config_deduction_id = e.config_deduction_id AND e.is_employee_share = 1
                    LEFT JOIN deduction_types f 
                        ON e.deduction_type_id = f.deduction_type_id
                    WHERE f.deduction_type_code = 'PAGIBIG'
                    GROUP BY c.payroll_entry_id, f.deduction_type_id
                ) ded ON ded.payroll_entry_id = a.payroll_entry_id

                -- Employer Share (Pag-IBIG)
                LEFT JOIN (
                    SELECT 
                        g.payroll_entry_id, 
                        k.deduction_type_id, -- capture deduction_type_id
                        i.govshare_id, -- Added govshare_id
                        SUM(g.payroll_govshare_amount) AS employer_share
                    FROM payroll_govshares g
                    LEFT JOIN employee_govshares h 
                        ON g.employee_govshare_id = h.employee_govshare_id
                    LEFT JOIN govshares i 
                        ON h.govshare_id = i.govshare_id
                    LEFT JOIN deduction_types k 
                        ON i.deduction_type_id = k.deduction_type_id
                    WHERE k.deduction_type_code = 'PAGIBIG'
                    GROUP BY g.payroll_entry_id, k.deduction_type_id
                ) gov ON gov.payroll_entry_id = a.payroll_entry_id

                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'
                
                GROUP BY b.employee_id
                ORDER BY b.lastname ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $remittances_pagibig = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_pagibig[] = $row;
        }
        return $remittances_pagibig;
    }

    public function GetRemittanceSSS($year, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    b.employee_id,
                    CONCAT(
                        b.lastname, ', ', b.firstname, ' ',
                        IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), 
                        ' ', b.extension
                    ) AS employee,
                    a.locked_basic,
                    b.employee_id,
                    b.sss_no,
                    pos.position_title,
                    ded.deduction_type_id, -- include deduction_type_id
                    ded.config_deduction_id,  -- Add this line
                    SUM(IFNULL(ded.employee_share,0)) AS employee_share
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id

                -- Employee Share (SSS from OTHERS category but tagged as SSS)
                LEFT JOIN (
                    SELECT 
                        c.payroll_entry_id, 
                        f.deduction_type_id, -- capture deduction_type_id
                        e.config_deduction_id, -- Added config_deduction_id
                        SUM(c.payroll_deduct_amount) AS employee_share
                    FROM payroll_deductions c
                    LEFT JOIN employee_deductions_components d 
                        ON c.deduction_component_id = d.deduction_component_id
                    LEFT JOIN config_deductions e 
                        ON d.config_deduction_id = e.config_deduction_id
                    LEFT JOIN deduction_types f 
                        ON e.deduction_type_id = f.deduction_type_id
                    WHERE f.deduction_type_code = 'OTHERS'
                    AND e.deduct_code LIKE '%SSS%'
                    GROUP BY c.payroll_entry_id, f.deduction_type_id
                ) ded ON ded.payroll_entry_id = a.payroll_entry_id

                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'
                
                GROUP BY b.employee_id
                ORDER BY b.lastname ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $remittances_sss = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_sss[] = $row;
        }
        return $remittances_sss;
    }

    public function GetRemittanceLoans($year, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    d.config_deduction_id AS loan_id,
                    f.deduction_type_id, -- include deduction_type_id
                    e.deduct_code,
                    e.deduct_title,
                    e.config_deduction_id,
                    SUM(IFNULL(c.payroll_deduct_amount,0)) AS total_loan_amount
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN payroll_deductions c ON c.payroll_entry_id = a.payroll_entry_id
                INNER JOIN employee_deductions_components d 
                    ON c.deduction_component_id = d.deduction_component_id
                INNER JOIN config_deductions e 
                    ON d.config_deduction_id = e.config_deduction_id
                INNER JOIN deduction_types f 
                    ON e.deduction_type_id = f.deduction_type_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id
                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'
                AND e.deduct_category = 'LOAN'
                
                GROUP BY d.config_deduction_id, f.deduction_type_id, e.deduct_code, e.deduct_title
                ORDER BY e.deduct_code ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $remittances_loans = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_loans[] = $row;
        }
        return $remittances_loans;
    }

    public function GetRemittanceOthers($year, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    e.config_deduction_id,
                    e.deduct_code,
                    e.deduct_title,
                    SUM(IFNULL(c.payroll_deduct_amount,0)) AS total_amount
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN payroll_deductions c ON c.payroll_entry_id = a.payroll_entry_id
                INNER JOIN employee_deductions_components d 
                    ON c.deduction_component_id = d.deduction_component_id
                INNER JOIN config_deductions e 
                    ON d.config_deduction_id = e.config_deduction_id
                INNER JOIN deduction_types f 
                    ON e.deduction_type_id = f.deduction_type_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id
                WHERE emp.employment_status = '1'
                AND YEAR(pp.date_start) = '$year'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'
                AND e.deduct_category = 'OTHER'
                AND f.deduction_type_code = 'OTHERS'
                AND e.deduct_code NOT LIKE '%SSS%'  -- Exclude SSS tagged under OTHERS
                
                GROUP BY e.config_deduction_id, e.deduct_code, e.deduct_title
                ORDER BY e.deduct_code ASC";

        $result = $this->db->query($sql) or die($this->db->error);
        $remittances_others = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances_others[] = $row;
        }
        return $remittances_others;
    }

    // Loan Deduction Breakdown per employee for a specific loan type
    public function GetRemittanceLoanBreakdown($loanId, $period) {
        //$deptCondition = $dept !== '' ? "AND a.dept_id = '$dept'" : "";   --> in case department field is added for query in the future
        list($start_date, $end_date) = explode('_', $period); // Split to get start and end
        $sql = "SELECT 
                    b.employee_id,
                    CONCAT(b.lastname, ', ', b.firstname, ' ' ,IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), ' ', b.extension) AS employee_name,
                    e.deduct_code,
                    e.deduct_title,
                    pos.position_title,
                    SUM(IFNULL(c.payroll_deduct_amount,0)) AS total_deduction
                FROM payroll_entries a
                JOIN employees_tbl b ON a.employee_id = b.employee_id
                INNER JOIN employee_employments_tbl emp ON b.employee_id = emp.employee_id
                INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
                INNER JOIN payroll_deductions c ON c.payroll_entry_id = a.payroll_entry_id
                INNER JOIN employee_deductions_components d ON c.deduction_component_id = d.deduction_component_id
                INNER JOIN config_deductions e ON d.config_deduction_id = e.config_deduction_id
                INNER JOIN payroll_periods pp ON a.payroll_period_id = pp.payroll_period_id
                WHERE emp.employment_status = '1'
                AND pp.date_start >= '$start_date'
                AND pp.date_end <= '$end_date'
                AND e.config_deduction_id = '$loanId'
                
                GROUP BY b.employee_id, employee_name, e.deduct_code, e.deduct_title
                ORDER BY b.lastname ASC";

        $result = $this->db->query($sql) or die($this->db->error);

        $loan_breakdown = [];
        while ($row = $this->db->fetch_array($result)) {
            $loan_breakdown[] = $row;
        }
        return $loan_breakdown;
    }

    public function SaveRemittances($remittance_arr, $status, $orNo = null, $refNo = null) {
        $period = $this->db->escape_string(trim($remittance_arr['period']));
        $period_parts = explode("_", $period);
        $period_start = $this->db->escape_string(trim($period_parts[0]));
        $period_end   = $this->db->escape_string(trim($period_parts[1]));

        foreach ($remittance_arr['deductions'] as $remit_type => $total_amount) {
            $remittance_type = $remit_type;
            $total_amount = $this->db->escape_string(trim($total_amount));
            $employee_total = isset($remittance_arr['employee_totals'][$remit_type]) 
                ? $this->db->escape_string(trim($remittance_arr['employee_totals'][$remit_type])) 
                : '0';
            $employer_total = isset($remittance_arr['employer_totals'][$remit_type]) 
                ? $this->db->escape_string(trim($remittance_arr['employer_totals'][$remit_type])) 
                : '0';
            $status       = $this->db->escape_string(trim($status));
            $or_number    = $this->db->escape_string(trim($orNo));
            $reference_no = $this->db->escape_string(trim($refNo));

            // Save summary to remittances table
            $sql = "
                INSERT INTO remittances (
                    remittance_type, period_start, period_end, total_amount, employee_totals, employer_totals, status, or_number, reference_no
                )
                VALUES (
                    '".$remittance_type."','".$period_start."','".$period_end."','".$total_amount."','".$employee_total."','".$employer_total."','".$status."','".$or_number."','".$reference_no."'
                )
                ON DUPLICATE KEY UPDATE
                    total_amount = VALUES(total_amount),
                    employee_totals = VALUES(employee_totals),
                    employer_totals = VALUES(employer_totals),
                    status = VALUES(status),
                    or_number = VALUES(or_number),
                    reference_no = VALUES(reference_no),
                    updated_at = NOW();
            ";

            $query = $this->db->query($sql) or die($this->db->error);

            // Get remittance_id (for new or updated row)
            $remittance_id = $this->db->last_id();
            if ($remittance_id == 0) {
                // If not a new insert, fetch the remittance_id
                $id_sql = "SELECT remittance_id FROM remittances WHERE remittance_type = '$remittance_type' AND period_start = '$period_start' AND period_end = '$period_end' LIMIT 1";
                $id_result = $this->db->query($id_sql) or die($this->db->error);
                $id_row = $this->db->fetch_array($id_result);
                $remittance_id = $id_row['remittance_id'];
            }

            // Check if remittance_id is valid before inserting details
            if ($remittance_id === null) {
                // Log error or skip this remittance_type
                continue;
            }

            // Save details for each employee
            if (isset($remittance_arr['details'][$remit_type]) && is_array($remittance_arr['details'][$remit_type])) {
                foreach ($remittance_arr['details'][$remit_type] as $employee_detail) {
                    $employee_id = $this->db->escape_string(trim($employee_detail['employee_id']));
                    $config_deduction_id = isset($employee_detail['config_deduction_id']) 
                        ? $this->db->escape_string(trim($employee_detail['config_deduction_id'])) 
                        : null;
                    $govshare_id = isset($employee_detail['govshare_id']) 
                        ? $this->db->escape_string(trim($employee_detail['govshare_id']))
                        : null;
                    $amount = $this->db->escape_string(trim($employee_detail['amount']));
                    $govshare_amt = isset($employee_detail['employer_share']) 
                        ? $this->db->escape_string(trim($employee_detail['employer_share'])) 
                        : null;

                    $detail_sql = "
                        INSERT INTO remittance_details (
                            remittance_id, employee_id, config_deduction_id, govshare_id, remittance_type, amount, govshare_amt
                        )
                        VALUES (
                            '$remittance_id', '$employee_id', '$config_deduction_id', '$govshare_id', '$remittance_type', '$amount', '$govshare_amt'
                        )
                        ON DUPLICATE KEY UPDATE
                            amount = VALUES(amount)
                            , govshare_amt = VALUES(govshare_amt)
                            , config_deduction_id = VALUES(config_deduction_id)
                            , govshare_id = VALUES(govshare_id)
                    ";
                    $this->db->query($detail_sql) or die($this->db->error);
                }
            }

            // Handle loan breakdowns here
            if (isset($remittance_arr['loans']) && is_array($remittance_arr['loans'])) {
                foreach ($remittance_arr['loans'] as $loan) {
                    $config_deduction_id = $loan['config_deduction_id'];
                    $loan_id = $loan['loan_id'];
                    $deduct_code = $loan['deduct_code'];
                    // Get remittance_id for this loan type
                    $id_sql = "SELECT remittance_id FROM remittances WHERE remittance_type = 'loans' AND period_start = '$period_start' AND period_end = '$period_end' LIMIT 1";
                    $id_result = $this->db->query($id_sql) or die($this->db->error);
                    $id_row = $this->db->fetch_array($id_result);
                    $loan_remittance_id = $id_row ? $id_row['remittance_id'] : null;
                    if ($loan_remittance_id === null) {
                        // Log error or skip this loan type
                        continue;
                    }

                    // Get breakdown for this loan type
                    $loan_breakdown = $this->GetRemittanceLoanBreakdown($loan_id, $remittance_arr['period']);
                    foreach ($loan_breakdown as $employee_loan) {
                        $employee_id = $this->db->escape_string(trim($employee_loan['employee_id']));
                        $amount = $this->db->escape_string(trim($employee_loan['total_deduction']));

                        $detail_sql = "
                            INSERT INTO remittance_details (
                                remittance_id, employee_id, config_deduction_id, remittance_type, amount
                            )
                            VALUES (
                                '$loan_remittance_id', '$employee_id', '$config_deduction_id', '$deduct_code', '$amount'
                            )
                            ON DUPLICATE KEY UPDATE
                                amount = VALUES(amount)
                                , config_deduction_id = VALUES(config_deduction_id)
                        ";
                        $this->db->query($detail_sql) or die($this->db->error);
                    }
                }
            }

            if(isset($remittance_arr['other_deductions'] ) && is_array($remittance_arr['other_deductions'])) {
                foreach ($remittance_arr['other_deductions'] as $other) {
                    $config_deduction_id = $other['config_deduction_id'];
                    $deduct_code = $other['deduct_code'];
                    // Get remittance_id for this other deduction type
                    $id_sql = "SELECT remittance_id FROM remittances WHERE remittance_type = 'others' AND period_start = '$period_start' AND period_end = '$period_end' LIMIT 1";
                    $id_result = $this->db->query($id_sql) or die($this->db->error);
                    $id_row = $this->db->fetch_array($id_result);
                    $other_remittance_id = $id_row ? $id_row['remittance_id'] : null;
                    if ($other_remittance_id === null) {
                        // Log error or skip this other deduction type
                        continue;
                    }

                    // Get breakdown for this other deduction type
                    $other_breakdown = $this->GetRemittanceLoanBreakdown($config_deduction_id, $remittance_arr['period']);
                    foreach ($other_breakdown as $employee_other) {
                        $employee_id = $this->db->escape_string(trim($employee_other['employee_id']));
                        $amount = $this->db->escape_string(trim($employee_other['total_deduction']));

                        $detail_sql = "
                            INSERT INTO remittance_details (
                                remittance_id, employee_id, config_deduction_id, remittance_type, amount
                            )
                            VALUES (
                                '$other_remittance_id', '$employee_id', '$config_deduction_id', '$deduct_code', '$amount'
                            )
                            ON DUPLICATE KEY UPDATE
                                amount = VALUES(amount)
                                , config_deduction_id = VALUES(config_deduction_id)
                        ";
                        $this->db->query($detail_sql) or die($this->db->error);
                    }
                }
            }
            
        }

        return true;
    }

    public function getRemittancesByYearAndType($year, $type) {
        $query = "SELECT *, CONCAT(period_start, '-', period_end) AS period_code
                FROM remittances
                WHERE YEAR(period_start) = '$year'
                AND remittance_type = '$type'
                ORDER BY period_start ASC";
        $result = $this->db->query($query) or die($this->db->error);

        $remittances = [];
        while ($row = $this->db->fetch_array($result)) {
            $remittances[] = $row;
        }
        return $remittances;
    }

    public function getRemittanceDetails($remittance_id) {
        $rid = $this->db->escape_string($remittance_id);

        $query = "
            SELECT
                rd.remittance_id,
                rd.remittance_type,
                e.employee_id,
                CONCAT(
                    e.lastname, ', ', e.firstname, ' ',
                    IF(LENGTH(e.middlename) > 0, CONCAT(LEFT(e.middlename,1),'. '), ''),
                    IFNULL(e.extension,'')
                ) AS employee_name,
                rd.config_deduction_id,
                cd.deduct_code,
                cd.deduct_title,
                rd.govshare_id, 
                gs.govshare_code,
                gs.govshare_name,
                rd.amount AS employee_share,
                rd.govshare_amt AS employer_share,
                COALESCE(cd.deduct_title, gs.govshare_name) AS remittance_title
            FROM remittance_details rd
            INNER JOIN employees_tbl e ON rd.employee_id = e.employee_id
            LEFT JOIN config_deductions cd ON rd.config_deduction_id = cd.config_deduction_id
            LEFT JOIN govshares gs ON rd.govshare_id = gs.govshare_id
            WHERE rd.remittance_id = '$rid'
            AND (
                COALESCE(rd.amount, 0) > 0 
                OR COALESCE(rd.govshare_amt, 0) > 0
            )
            ORDER BY e.lastname ASC, e.firstname ASC, cd.deduct_code ASC
        ";

        $result = $this->db->query($query) or die($this->db->error);

        $data = [];
        while ($row = $this->db->fetch_array($result)) {
            // normalize numeric amounts and keep ids nullable as needed
            $row['employee_share'] = isset($row['employee_share']) ? (float)$row['employee_share'] : 0.0;
            $row['employer_share'] = isset($row['employer_share']) ? (float)$row['employer_share'] : 0.0;
            $row['config_deduction_id'] = $row['config_deduction_id'] ?: null;
            $row['govshare_id'] = $row['govshare_id'] ?: null;

            $data[] = $row;
        }

        return $data;
    }

    public function getLoansRemittanceDetails($remittance_id) {
        $rid = $this->db->escape_string($remittance_id);

        $query = "
            SELECT
                rd.remittance_id,
                rd.remittance_type,
                e.employee_id,
                CONCAT(
                    e.lastname, ', ', e.firstname, ' ',
                    IF(LENGTH(e.middlename) > 0, CONCAT(LEFT(e.middlename,1),'. '), ''),
                    IFNULL(e.extension,'')
                ) AS employee_name,
                pos.position_title,
                rd.config_deduction_id,
                cd.deduct_code,
                cd.deduct_title,
                rd.amount AS loan_deduction
            FROM remittance_details rd
            INNER JOIN employees_tbl e ON rd.employee_id = e.employee_id
            INNER JOIN employee_employments_tbl emp ON e.employee_id = emp.employee_id
            INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
            INNER JOIN config_deductions cd ON rd.config_deduction_id = cd.config_deduction_id
            WHERE rd.remittance_id = '$rid'
            AND cd.deduct_category = 'LOAN'
            AND COALESCE(rd.amount, 0) > 0
            GROUP BY rd.config_deduction_id, e.employee_id
            ORDER BY cd.deduct_code ASC, e.lastname ASC, e.firstname ASC";

        $result = $this->db->query($query) or die($this->db->error);

        $data = [];
        while ($row = $this->db->fetch_array($result)) {
            // normalize numeric amounts and keep ids nullable as needed
            $row['loan_deduction'] = isset($row['loan_deduction']) ? (float)$row['loan_deduction'] : 0.0;
            $row['config_deduction_id'] = $row['config_deduction_id'] ?: null;

            // Group by loan type
            $loan_type = $row['deduct_code'];
            if (!isset($data[$loan_type])) {
                $data[$loan_type] = [
                    'loan_type' => $loan_type,
                    'loan_title' => $row['deduct_title'],
                    'config_deduction_id' => $row['config_deduction_id'],
                    'total_amount' => 0,
                    'employees' => []
                ];
            }

            // Add employee details and update total
            $data[$loan_type]['employees'][] = [
                'employee_id' => $row['employee_id'],
                'employee_name' => $row['employee_name'],
                'position_title' => $row['position_title'],
                'amount' => $row['loan_deduction']
            ];
            $data[$loan_type]['total_amount'] += $row['loan_deduction'];
        }

        return array_values($data);
    }

    public function getOthersRemittanceDetails($remittance_id) {
        $rid = $this->db->escape_string($remittance_id);

        $query = "
            SELECT
                rd.remittance_id,
                rd.remittance_type,
                e.employee_id,
                CONCAT(
                    e.lastname, ', ', e.firstname, ' ',
                    IF(LENGTH(e.middlename) > 0, CONCAT(LEFT(e.middlename,1),'. '), ''),
                    IFNULL(e.extension,'')
                ) AS employee_name,
                pos.position_title,
                rd.config_deduction_id,
                cd.deduct_code,
                cd.deduct_title,
                rd.amount AS other_deduction
            FROM remittance_details rd
            INNER JOIN employees_tbl e ON rd.employee_id = e.employee_id
            INNER JOIN employee_employments_tbl emp ON e.employee_id = emp.employee_id
            INNER JOIN positions_tbl pos ON emp.position_id = pos.position_id
            INNER JOIN config_deductions cd ON rd.config_deduction_id = cd.config_deduction_id
            WHERE rd.remittance_id = '$rid'
            AND cd.deduct_category = 'OTHER'
            AND COALESCE(rd.amount, 0) > 0
            GROUP BY rd.config_deduction_id, e.employee_id
            ORDER BY cd.deduct_code ASC, e.lastname ASC, e.firstname ASC";

        $result = $this->db->query($query) or die($this->db->error);

        $data = [];
        while ($row = $this->db->fetch_array($result)) {
            // normalize numeric amounts and keep ids nullable as needed
            $row['other_deduction'] = isset($row['other_deduction']) ? (float)$row['other_deduction'] : 0.0;
            $row['config_deduction_id'] = $row['config_deduction_id'] ?: null;

            // Group by other deduction type
            $other_type = $row['deduct_code'];
            if (!isset($data[$other_type])) {
                $data[$other_type] = [
                    'other_type' => $other_type,
                    'deduct_title' => $row['deduct_title'],
                    'config_deduction_id' => $row['config_deduction_id'],
                    'total_amount' => 0,
                    'employees' => []
                ];
            }
            // Add employee details and update total
            $data[$other_type]['employees'][] = [
                'employee_id' => $row['employee_id'],
                'employee_name' => $row['employee_name'],
                'position_title' => $row['position_title'],
                'amount' => $row['other_deduction']
            ];
            $data[$other_type]['total_amount'] += $row['other_deduction'];
        }
        return array_values($data);
    }
}
?>