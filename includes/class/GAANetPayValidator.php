<?php

/**
 * GAANetPayValidator
 * 
 * Enforces the General Appropriations Act (GAA) minimum net take-home pay threshold.
 * Per GAA provisions: no employee's net pay may fall below PHP 5,000.00
 * after mandatory and authorized deductions.
 * 
 * @author Development Team
 * @version 1.0.0
 * @since 2026-03-06
 */

class GAANetPayValidator {
    
    // ────────────────────────────────────────────────────────────
    // SYSTEM CONSTANTS
    // ────────────────────────────────────────────────────────────
    
    /** PHP 5,000.00 minimum net pay per GAA */
    const MIN_NET_PAY = 5000.00;
    
    /** Class version for audit trail versioning */
    const VERSION = '1.0.0';
    
    /** Class identifier for logging */
    const CLASS_NAME = 'GAANetPayValidator';
    
    // ────────────────────────────────────────────────────────────
    // VALIDATION STATUS CODES
    // ────────────────────────────────────────────────────────────
    
    const STATUS_OK = 'OK';
    const STATUS_WARNING = 'WARNING';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_ERROR = 'ERROR';
    
    // ────────────────────────────────────────────────────────────
    // AUDIT ACTION CODES (stored in gaa_audit_log)
    // ────────────────────────────────────────────────────────────
    
    const ACTION_VALIDATED = 'VALIDATED';
    const ACTION_BLOCKED = 'BLOCKED';
    const ACTION_WARNED = 'WARNED';
    const ACTION_APPROVED = 'APPROVED';
    const ACTION_OVERRIDDEN = 'OVERRIDE_APPROVED';
    
    // ────────────────────────────────────────────────────────────
    // VALIDATION POINT IDENTIFIERS
    // ────────────────────────────────────────────────────────────
    
    const POINT_DEDUCTION_ENTRY = 'deduction_entry';
    const POINT_PAYROLL_SAVE = 'payroll_save';
    const POINT_BATCH_APPROVAL = 'batch_approval';
    
    // ────────────────────────────────────────────────────────────
    // PRIVATE INSTANCE VARIABLES
    // ────────────────────────────────────────────────────────────
    
    /** Database connection (mysqli or DB_conn) */
    protected $db;
    
    /** Currently logged-in user ID */
    protected $userId;
    
    /** Array of validation errors */
    private $errors = [];
    
    /** Array of validation warnings */
    private $warnings = [];
    
    public function __construct($db = null, $userId = 0) {
        $this->db = $db ?? (new DB_conn());
        $this->userId = ($userId > 0) ? $userId : ($_SESSION['uid'] ?? 0);
    }
    
    // ────────────────────────────────────────────────────────────
    // CORE VALIDATION METHODS
    // ────────────────────────────────────────────────────────────
    
    /**
     * Computes net pay from gross earnings and deductions
     * 
     * Formula: Net Pay = Gross Earnings - Mandatory Deductions - Authorized Deductions
     * 
     * @param float $grossEarnings       Total gross earnings
     * @param float $mandatoryDeductions GSIS, PhilHealth, Pag-IBIG, WHT, etc.
     * @param float $authorizedDeductions Loans, consignations, other authorized deductions
     * 
     * @return float Computed net pay (rounded to 2 decimal places)
     */
    public function computeNetPay($grossEarnings, $mandatoryDeductions, $authorizedDeductions) {
        $grossEarnings = (float) $grossEarnings;
        $mandatoryDeductions = (float) $mandatoryDeductions;
        $authorizedDeductions = (float) $authorizedDeductions;
        
        $net = $grossEarnings - $mandatoryDeductions - $authorizedDeductions;
        
        // Round to 2 decimal places per Philippine peso standard
        return round($net, 2);
    }
    
    /**
     * Validates if computed net pay meets the GAA minimum threshold
     * 
     * Primary validation gate - determines if payroll entry can proceed
     * 
     * @param float $netPay            Pre-computed net pay value
     * @param int $employeeId          Employee ID (for audit logging)
     * @param int $payrollEntryId      Payroll entry ID (for audit logging)
     * @param string $validationPoint  Where validation occurs: 'deduction_entry|payroll_save|batch_approval'
     * 
     * @return array Structured validation result:
     *   - status: 'OK'|'BLOCKED'|'ERROR'
     *   - net_pay: float (computed amount)
     *   - threshold: float (5000.00)
     *   - shortfall: float (amount below threshold, 0 if compliant)
     *   - message: string (human readable status)
     *   - can_proceed: bool (true if compliant)
     *   - validation_point: string (where check occurred)
     */
    public function validateThreshold($netPay, $employeeId, $payrollEntryId, $validationPoint = self::POINT_PAYROLL_SAVE) {
        $netPay = (float) $netPay;
        $shortfall = self::MIN_NET_PAY - $netPay;
        
        if ($netPay >= self::MIN_NET_PAY) {
            // Threshold met - proceed
            $result = [
                'status' => self::STATUS_OK,
                'net_pay' => $netPay,
                'threshold' => self::MIN_NET_PAY,
                'shortfall' => 0.00,
                'message' => 'Net pay meets the GAA minimum threshold of PHP ' . number_format(self::MIN_NET_PAY, 2) . '.',
                'can_proceed' => true,
                'validation_point' => $validationPoint,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Log validation success
            $this->writeAuditLog(
                $payrollEntryId,
                $employeeId,
                self::ACTION_VALIDATED,
                $netPay,
                0.00,
                $validationPoint,
                ''
            );
            
        } else {
            // Threshold breached - BLOCK
            $message = sprintf(
                'Net take-home pay of PHP %s is BELOW the GAA-mandated PHP 5,000.00 minimum threshold. Shortfall: PHP %s.',
                number_format($netPay, 2),
                number_format($shortfall, 2)
            );
            
            $result = [
                'status' => self::STATUS_BLOCKED,
                'net_pay' => $netPay,
                'threshold' => self::MIN_NET_PAY,
                'shortfall' => round($shortfall, 2),
                'message' => $message,
                'can_proceed' => false,
                'validation_point' => $validationPoint,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Log validation failure (BLOCKED)
            $this->writeAuditLog(
                $payrollEntryId,
                $employeeId,
                self::ACTION_BLOCKED,
                $netPay,
                $shortfall,
                $validationPoint,
                $message
            );
            
            // Flag the payroll record as violating GAA threshold
            $this->flagPayrollRecord($payrollEntryId);
        }
        
        return $result;
    }
    
    /**
     * Validates impact of a proposed deduction BEFORE it's saved
     * 
     * Called real-time when HR is entering a new deduction amount
     * Simulates the impact on net pay without committing changes
     * 
     * @param int $payrollEntryId  Payroll record to check against
     * @param float $proposedAmount The new deduction amount being entered
     * @param string $deductionType 'mandatory'|'authorized' (defaults to 'authorized')
     * 
     * @return array Validation result (see validateThreshold return structure)
     */
    public function validateDeductionEntry($payrollEntryId, $proposedAmount, $deductionType = 'authorized') {
        $payrollEntryId = (int) $payrollEntryId;
        $proposedAmount = (float) $proposedAmount;
        
        // Fetch current payroll record from database
        $record = $this->fetchPayrollRecord($payrollEntryId);
        
        if (!$record) {
            return $this->errorResult('Payroll record not found. Cannot validate deduction.');
        }
        
        // Simulate net pay WITH the proposed new deduction added
        if ($deductionType === 'mandatory') {
            $newMandatory = $record['mandatory_deductions'] + $proposedAmount;
            $simulatedNet = $this->computeNetPay(
                $record['gross_earnings'],
                $newMandatory,
                $record['authorized_deductions']
            );
        } else {
            // authorized deduction (default)
            $newAuthorized = $record['authorized_deductions'] + $proposedAmount;
            $simulatedNet = $this->computeNetPay(
                $record['gross_earnings'],
                $record['mandatory_deductions'],
                $newAuthorized
            );
        }
        
        // Run threshold validation on simulated net pay
        return $this->validateThreshold(
            $simulatedNet,
            $record['employee_id'],
            $payrollEntryId,
            self::POINT_DEDUCTION_ENTRY
        );
    }
    
    /**
     * Validates ALL employee records in a payroll period (BATCH validation)
     * 
     * Called before approver transitions payroll period to APPROVED status
     * Blocks entire period if ANY record violates threshold
     * 
     * @param int $payrollPeriodId Payroll period ID to validate all records for
     * 
     * @return array Batch validation result:
     *   - can_approve: bool (true if all records compliant)
     *   - total_checked: int (total records validated)
     *   - violations: array of violating employee records
     *   - passed: int (count of compliant records)
     *   - blocked: int (count of violating records)
     */
    public function validatePayrollBatch($payrollPeriodId) {
        $payrollPeriodId = (int) $payrollPeriodId;
        
        // Fetch all payroll records for this period
        $records = $this->fetchAllRecordsForPeriod($payrollPeriodId);
        
        if (!$records || empty($records)) {
            return [
                'can_approve' => true,
                'total_checked' => 0,
                'violations' => [],
                'passed' => 0,
                'blocked' => 0
            ];
        }
        
        $violations = [];
        $passed = 0;
        
        foreach ($records as $record) {
            // Compute net pay for this record
            $net = $this->computeNetPay(
                $record['gross_earnings'],
                $record['mandatory_deductions'],
                $record['authorized_deductions']
            );
            
            // Run threshold validation
            $result = $this->validateThreshold(
                $net,
                $record['employee_id'],
                $record['payroll_entry_id'],
                self::POINT_BATCH_APPROVAL
            );
            
            if (!$result['can_proceed']) {
                // Record this violation
                $violations[] = [
                    'payroll_entry_id' => $record['payroll_entry_id'],
                    'employee_id' => $record['employee_id'],
                    'employee_name' => $record['full_name'] ?? 'Unknown',
                    'net_pay' => $net,
                    'shortfall' => $result['shortfall'],
                    'message' => $result['message'],
                    'gross' => $record['gross_earnings'],
                    'deductions_total' => $record['mandatory_deductions'] + $record['authorized_deductions']
                ];
            } else {
                $passed++;
            }
        }
        
        return [
            'can_approve' => empty($violations),
            'total_checked' => count($records),
            'violations' => $violations,
            'passed' => $passed,
            'blocked' => count($violations)
        ];
    }
    
    // ────────────────────────────────────────────────────────────
    // PRIVATE HELPER METHODS
    // ────────────────────────────────────────────────────────────
    
    /**
     * Fetches a single payroll record with full employee and deduction details
     * 
     * @param int $payrollEntryId Payroll entry ID to fetch
     * 
     * @return array|null Payroll record data or null if not found
     */
    private function fetchPayrollRecord($payrollEntryId) {
        $payrollEntryId = (int) $payrollEntryId;
        
        $sql = "SELECT 
                    pr.payroll_entry_id,
                    pr.employee_id,
                    pr.gross,
                    pr.total_deductions,
                    pr.net_pay,
                    pr.earnings_breakdown,
                    pr.deductions_breakdown
                FROM payroll_entries pr
                WHERE pr.payroll_entry_id = ?
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param('i', $payrollEntryId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        $record = $result->fetch_assoc();
        $stmt->close();
        
        // Create alias for gross earnings to match expected variable names
        $record['gross_earnings'] = (float) $record['gross'];
        
        // Parse deductions breakdown if it's JSON
        if ($record['deductions_breakdown']) {
            $deductions = json_decode($record['deductions_breakdown'], true);
            $record['mandatory_deductions'] = (float) ($deductions['mandatory_deductions'] ?? $deductions['mandatory_total'] ?? 0);
            $record['authorized_deductions'] = (float) ($deductions['authorized_deductions'] ?? $deductions['authorized_total'] ?? 0);
        } else {
            $record['mandatory_deductions'] = 0;
            $record['authorized_deductions'] = 0;
        }
        
        return $record;
    }
    
    /**
     * Fetches all payroll records for a given payroll period
     * 
     * @param int $payrollPeriodId Period ID to fetch records for
     * 
     * @return array Array of payroll records for the period
     */
    private function fetchAllRecordsForPeriod($payrollPeriodId) {
        $payrollPeriodId = (int) $payrollPeriodId;
        
        $sql = "SELECT 
                    pr.payroll_entry_id,
                    pr.employee_id,
                    pr.gross,
                    pr.total_deductions,
                    pr.net_pay,
                    pr.earnings_breakdown,
                    pr.deductions_breakdown
                FROM payroll_entries pr
                WHERE pr.payroll_period_id = ?
                ORDER BY pr.employee_id ASC";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }
        
        $stmt->bind_param('i', $payrollPeriodId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $records = [];
        while ($row = $result->fetch_assoc()) {
            // Create alias for gross earnings to match expected variable names
            $row['gross_earnings'] = (float) $row['gross'];
            
            // Parse deductions breakdown
            if ($row['deductions_breakdown']) {
                $deductions = json_decode($row['deductions_breakdown'], true);
                $row['mandatory_deductions'] = (float) ($deductions['mandatory_deductions'] ?? $deductions['mandatory_total'] ?? 0);
                $row['authorized_deductions'] = (float) ($deductions['authorized_deductions'] ?? $deductions['authorized_total'] ?? 0);
            } else {
                $row['mandatory_deductions'] = 0;
                $row['authorized_deductions'] = 0;
            }
            $records[] = $row;
        }
        
        $stmt->close();
        return $records;
    }
    
    /**
     * Flags a payroll record as having a GAA violation
     * 
     * Sets gaa_violation_flag = 1 for easy filtering of blocked records
     * 
     * @param int $payrollEntryId Payroll entry ID to flag
     * 
     * @return bool Success status
     */
    private function flagPayrollRecord($payrollEntryId) {
        $payrollEntryId = (int) $payrollEntryId;
        
        $sql = "UPDATE payroll_entries
                SET gaa_violation_flag = 1
                WHERE payroll_entry_id = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param('i', $payrollEntryId);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Writes an audit log entry for the GAA compliance event
     * 
     * @param int $payrollEntryId      Payroll entry ID
     * @param int $employeeId          Employee ID
     * @param string $action           Action code: VALIDATED|BLOCKED|WARNED|APPROVED|OVERRIDE_APPROVED
     * @param float $computedNetPay    Net pay amount at time of validation
     * @param float $shortfallAmount   Amount below threshold (0 if compliant)
     * @param string $validationPoint  Where check occurred
     * @param string $remarks          Optional additional notes
     * 
     * @return bool Success status
     */
    private function writeAuditLog($payrollEntryId, $employeeId, $action, $computedNetPay, $shortfallAmount, $validationPoint, $remarks = '') {
        $payrollEntryId = (int) $payrollEntryId;
        $employeeId = (int) $employeeId;
        $computedNetPay = (float) $computedNetPay;
        $shortfallAmount = (float) $shortfallAmount;
        $userId = (int) $this->userId;
        $thresholdAmount = (float) self::MIN_NET_PAY;
        
        $sql = "INSERT INTO gaa_audit_log 
                (payroll_entry_id, employee_id, action, computed_net_pay, threshold_amount, 
                 shortfall_amount, performed_by, performed_at, validation_point, remarks)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param(
            'iisdddsss',
            $payrollEntryId,
            $employeeId,
            $action,
            $computedNetPay,
            $thresholdAmount,
            $shortfallAmount,
            $userId,
            $validationPoint,
            $remarks
        );
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Creates a standardized error result array
     * 
     * @param string $message Error message
     * 
     * @return array Error result structure
     */
    private function errorResult($message) {
        return [
            'status' => self::STATUS_ERROR,
            'net_pay' => 0.00,
            'threshold' => self::MIN_NET_PAY,
            'shortfall' => 0.00,
            'message' => 'ERROR: ' . $message,
            'can_proceed' => false,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    // ────────────────────────────────────────────────────────────
    // UTILITY METHODS
    // ────────────────────────────────────────────────────────────
    
    /**
     * Gets accumulated validation errors
     * 
     * @return array Error messages array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Gets accumulated validation warnings
     * 
     * @return array Warning messages array
     */
    public function getWarnings() {
        return $this->warnings;
    }
    
    /**
     * Gets the validator version
     * 
     * @return string Version string
     */
    public static function getVersion() {
        return self::VERSION;
    }
    
    /**
     * Gets the GAA minimum net pay threshold constant
     * 
     * @return float Minimum net pay (PHP 5,000.00)
     */
    public static function getThreshold() {
        return self::MIN_NET_PAY;
    }
}

?>
