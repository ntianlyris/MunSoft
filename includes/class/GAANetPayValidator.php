<?php
/**
 * GAANetPayValidator Class
 * Purpose: Validate employee net pay against PHP 5,000.00 GAA minimum threshold
 * Design: Read-only validation layer - logs to payroll_gaa_status table and audit trail
 * Date: March 7, 2026
 */

include_once("DB_conn.php");

class GAANetPayValidator {
    
    // ===== CONSTANTS =====
    const MIN_NET_PAY = 5000.00;
    const VALIDATOR_VERSION = "1.1";
    
    // ===== PROPERTIES =====
    protected $db;
    
    // ===== CONSTRUCTOR =====
    public function __construct($db = null) {
        $this->db = $db ?? (new DB_conn());
    }
    
    // ===================================================
    // STAGE 1: DEDUCTION ENTRY VALIDATION
    // ===================================================
    
    /**
     * Validate deduction entry in real-time (Stage 1)
     * Called when HR enters/edits deduction amount
     * 
     * @param int $employee_id
     * @param float $proposed_total_deductions - New total deductions after entry
     * @param float $current_gross - Current employee gross earnings
     * @return array ['is_valid', 'net_after', 'shortfall', 'threshold', 'message', 'status']
     */
    public function validateDeductionEntry($employee_id, $proposed_total_deductions, $current_gross = null) {
        
        // Get gross if not provided
        if ($current_gross === null) {
            $current_gross = $this->getEmployeeLatestGross($employee_id);
        }
        
        $current_gross = floatval($current_gross);
        $proposed_total_deductions = floatval($proposed_total_deductions);
        
        // Calculate projected net pay
        $projected_net = $current_gross - $proposed_total_deductions;
        
        // Check against threshold
        $shortfall = max(0, self::MIN_NET_PAY - $projected_net);
        
        if ($projected_net < self::MIN_NET_PAY) {
            return [
                'is_valid' => false,
                'current_gross' => round($current_gross, 2),
                'proposed_deduction' => round($proposed_total_deductions, 2),
                'net_after' => round($projected_net, 2),
                'shortfall' => round($shortfall, 2),
                'threshold' => self::MIN_NET_PAY,
                'message' => sprintf(
                    'Deduction would result in net pay of ₱%.2f. GAA minimum is ₱%.2f. Shortfall: ₱%.2f',
                    $projected_net,
                    self::MIN_NET_PAY,
                    $shortfall
                ),
                'status' => 'BLOCKED'
            ];
        }
        
        return [
            'is_valid' => true,
            'current_gross' => round($current_gross, 2),
            'proposed_deduction' => round($proposed_total_deductions, 2),
            'net_after' => round($projected_net, 2),
            'shortfall' => 0,
            'threshold' => self::MIN_NET_PAY,
            'message' => 'Deduction is within GAA threshold limits.',
            'status' => 'ALLOWED'
        ];
    }
    
    // ===================================================
    // STAGE 2: PAYROLL SAVE VALIDATION
    // ===================================================
    
    /**
     * Validate single payroll entry before save (Stage 2)
     * Called when HR clicks "Save Payroll"
     * 
     * @param int $employee_id
     * @param float $gross
     * @param float $total_deductions
     * @param string $payroll_frequency - Optional
     * @return array ['is_valid', 'violations', 'net_pay', 'gross', 'total_deductions', 'threshold', 'message']
     */
    public function validatePayrollEntry($employee_id, $gross, $total_deductions, $payroll_frequency = 'monthly') {
        
        $gross = floatval($gross);
        $total_deductions = floatval($total_deductions);
        $violations = [];
        
        // Calculate net pay
        $net_pay = $gross - $total_deductions;
        
        // Check threshold violation
        if ($net_pay < self::MIN_NET_PAY) {
            $shortfall = self::MIN_NET_PAY - $net_pay;
            $violations[] = [
                'violation_type' => 'NET_PAY_THRESHOLD',
                'net_pay' => round($net_pay, 2),
                'threshold' => self::MIN_NET_PAY,
                'shortfall_amount' => round($shortfall, 2),
                'severity' => 'CRITICAL',
                'message' => sprintf(
                    'Net Pay (₱%.2f) is below GAA minimum threshold (₱%.2f). Shortfall: ₱%.2f',
                    $net_pay,
                    self::MIN_NET_PAY,
                    $shortfall
                )
            ];
        }
        
        return [
            'is_valid' => empty($violations),
            'violations' => $violations,
            'net_pay' => round($net_pay, 2),
            'gross' => round($gross, 2),
            'total_deductions' => round($total_deductions, 2),
            'threshold' => self::MIN_NET_PAY,
            'message' => empty($violations) 
                ? 'Payroll entry passes GAA validation.' 
                : 'Payroll entry contains GAA violations.'
        ];
    }
    
    // ===================================================
    // STAGE 3: BATCH APPROVAL VALIDATION
    // ===================================================
    
    /**
     * Validate all payroll entries for period before approval (Stage 3)
     * Called before batch approval workflow
     * 
     * @param int $payroll_period_id
     * @param int $dept_id (optional - filter by department)
     * @return array ['is_valid', 'violations', 'violation_count', 'affected_employees', 'threshold', 'message', 'can_approve']
     */
    public function validatePayrollBatch($payroll_period_id, $dept_id = null) {
        
        $payroll_period_id = intval($payroll_period_id);
        $violations = [];
        $affected_employees = [];
        
        // Build query to get all payroll entries below threshold for period
        $query = "SELECT 
                    pe.payroll_entry_id, 
                    pe.employee_id, 
                    CONCAT(e.lastname, ', ', e.firstname) AS employee_name,
                    e.employee_id_num,
                    pe.gross, 
                    pe.total_deductions,
                    (pe.gross - pe.total_deductions) AS calculated_net_pay,
                    pe.net_pay,
                    dp.dept_title
                  FROM payroll_entries pe
                  INNER JOIN employees_tbl e ON pe.employee_id = e.employee_id
                  LEFT JOIN departments_tbl dp ON pe.dept_id = dp.dept_id
                  WHERE pe.payroll_period_id = '$payroll_period_id'";
        
        if ($dept_id !== null) {
            $dept_id = intval($dept_id);
            $query .= " AND pe.dept_id = '$dept_id'";
        }
        
        $query .= " AND (pe.gross - pe.total_deductions) < " . self::MIN_NET_PAY . " ORDER BY (pe.gross - pe.total_deductions) ASC";
        
        $result = $this->db->query($query) or die($this->db->error);
        
        if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $calculated_net = floatval($row['calculated_net_pay']);
                $shortfall = self::MIN_NET_PAY - $calculated_net;
                
                $violations[] = [
                    'payroll_entry_id' => intval($row['payroll_entry_id']),
                    'employee_id' => intval($row['employee_id']),
                    'employee_name' => $row['employee_name'],
                    'employee_id_num' => $row['employee_id_num'],
                    'department' => $row['dept_title'],
                    'net_pay' => $calculated_net,
                    'threshold' => self::MIN_NET_PAY,
                    'shortfall_amount' => round($shortfall, 2),
                    'gross' => floatval($row['gross']),
                    'total_deductions' => floatval($row['total_deductions']),
                    'severity' => 'CRITICAL'
                ];
                
                $affected_employees[] = intval($row['employee_id']);
            }
        }
        
        return [
            'is_valid' => empty($violations),
            'can_approve' => empty($violations), // Aliasing for compatibility
            'violations' => $violations,
            'violation_count' => count($violations),
            'affected_employees' => array_unique($affected_employees),
            'threshold' => self::MIN_NET_PAY,
            'message' => empty($violations) 
                ? 'All payroll entries pass GAA validation.' 
                : count($violations) . ' employee(s) have net pay below GAA threshold.'
        ];
    }
    
    // ===================================================
    // UTILITY: SAVE GAA STATUS
    // ===================================================
    
    /**
     * Save GAA violation status to payroll_gaa_status table
     * 
     * @param int $payroll_entry_id
     * @param bool $has_violation
     * @param float $shortfall_amount
     * @return bool
     */
    public function saveGAAStatus($payroll_entry_id, $has_violation = false, $shortfall_amount = 0) {
        
        $payroll_entry_id = intval($payroll_entry_id);
        $violation_flag = $has_violation ? 1 : 0;
        $shortfall_amount = floatval($shortfall_amount);
        
        $query = "INSERT INTO payroll_gaa_status 
                    (payroll_entry_id, gaa_violation_flag, gaa_shortfall_amount, gaa_validated_date)
                  VALUES ('$payroll_entry_id', '$violation_flag', '$shortfall_amount', NOW())
                  ON DUPLICATE KEY UPDATE 
                    gaa_violation_flag = '$violation_flag',
                    gaa_shortfall_amount = '$shortfall_amount',
                    gaa_validated_date = NOW()";
        
        $result = $this->db->query($query);
        return $result ? true : false;
    }
    
    /**
     * Log validation attempt to audit trail
     * 
     * @param int $payroll_entry_id
     * @param int $employee_id
     * @param string $validation_stage
     * @param float $net_pay
     * @param float $shortfall
     * @param string $validation_result
     * @param string $notes
     * @return bool
     */
    public function logValidationAudit($payroll_entry_id, $employee_id, $validation_stage, $net_pay, $shortfall, $validation_result, $notes = '') {
        
        $payroll_entry_id = intval($payroll_entry_id);
        $employee_id = intval($employee_id);
        $net_pay = floatval($net_pay);
        $shortfall = floatval($shortfall);
        $notes = $this->db->escape_string($notes);
        
        $context_query = "SELECT gross, total_deductions FROM payroll_entries WHERE payroll_entry_id = '$payroll_entry_id' LIMIT 1";
        $context_result = $this->db->query($context_query);
        $context = $this->db->fetch_array($context_result);
        
        $gross = floatval($context['gross'] ?? 0);
        $total_deductions = floatval($context['total_deductions'] ?? 0);
        
        $query = "INSERT INTO gaa_validation_audit 
                    (payroll_entry_id, employee_id, validation_stage, net_pay, shortfall_amount, 
                     gross_amount, total_deductions_amount, validation_result, notes, validated_date)
                  VALUES 
                    ('$payroll_entry_id', '$employee_id', '$validation_stage', '$net_pay', '$shortfall',
                     '$gross', '$total_deductions', '$validation_result', '$notes', NOW())";
        
        $result = $this->db->query($query);
        return $result ? true : false;
    }
    
    // ===================================================
    // PRIVATE HELPER METHODS
    // ===================================================
    
    private function getEmployeeLatestGross($employee_id) {
        $employee_id = intval($employee_id);
        $query = "SELECT gross FROM payroll_entries 
                  WHERE employee_id = '$employee_id' 
                  ORDER BY created_at DESC LIMIT 1";
        $result = $this->db->query($query);
        if ($this->db->num_rows($result) > 0) {
            $row = $this->db->fetch_array($result);
            return floatval($row['gross']);
        }
        return $this->calculateEmployeeGross($employee_id);
    }
    
    private function calculateEmployeeGross($employee_id) {
        $employee_id = intval($employee_id);
        $query = "SELECT SUM(b.earning_comp_amt) as total_gross 
                  FROM employee_earnings a
                  INNER JOIN employee_earnings_components b 
                    ON a.employee_earning_id = b.employee_earning_id
                  WHERE a.employee_id = '$employee_id' 
                  AND (a.end_date IS NULL OR a.end_date >= CURDATE())";
        $result = $this->db->query($query);
        if ($this->db->num_rows($result) > 0) {
            $row = $this->db->fetch_array($result);
            return floatval($row['total_gross'] ?? 0);
        }
        return 0;
    }
    
    /**
     * Compatibility method for computeNetPay used in some parts of the system
     */
    public function computeNetPay($gross, $mandatory_deductions, $authorized_deductions) {
        return round(floatval($gross) - floatval($mandatory_deductions) - floatval($authorized_deductions), 2);
    }
}
?>
