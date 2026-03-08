-- =====================================================
-- GAA NET PAY THRESHOLD VALIDATOR - DATABASE MIGRATION
-- ALTERNATIVE DESIGN: Separate GAA Status Table
-- Date: March 7, 2026
-- Purpose: Add GAA validation tracking without modifying payroll_entries
-- =====================================================

-- =====================================================
-- PART 1: CREATE payroll_gaa_status TABLE (1-to-1 with payroll_entries)
-- =====================================================

CREATE TABLE IF NOT EXISTS `payroll_gaa_status` (
  `payroll_gaa_status_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payroll_entry_id` INT(11) NOT NULL UNIQUE,
  `gaa_violation_flag` TINYINT(1) DEFAULT 0,
  `gaa_shortfall_amount` DECIMAL(12,2) DEFAULT 0,
  `gaa_validated_date` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`payroll_entry_id`) REFERENCES `payroll_entries`(`payroll_entry_id`) ON DELETE CASCADE,
  KEY `idx_gaa_violation` (`gaa_violation_flag`),
  KEY `idx_gaa_validated_date` (`gaa_validated_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- PART 2: CREATE gaa_validation_audit TABLE (unchanged)
-- For detailed audit trail of every validation attempt
-- =====================================================

CREATE TABLE IF NOT EXISTS `gaa_validation_audit` (
  `gaa_audit_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payroll_entry_id` INT(11) NOT NULL,
  `employee_id` INT(11) NOT NULL,
  `validation_stage` ENUM('DEDUCTION_ENTRY', 'PAYROLL_SAVE', 'BATCH_APPROVAL') NOT NULL,
  `net_pay` DECIMAL(12,2) NOT NULL,
  `threshold_amount` DECIMAL(12,2) NOT NULL DEFAULT 5000.00,
  `shortfall_amount` DECIMAL(12,2) DEFAULT 0,
  `gross_amount` DECIMAL(12,2) DEFAULT 0,
  `total_deductions_amount` DECIMAL(12,2) DEFAULT 0,
  `validation_result` ENUM('PASS', 'FAIL', 'WARNING') NOT NULL,
  `validated_by` INT(11) NULL,
  `validated_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` LONGTEXT NULL,
  FOREIGN KEY (`payroll_entry_id`) REFERENCES `payroll_entries`(`payroll_entry_id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees_tbl`(`employee_id`) ON DELETE CASCADE,
  KEY `idx_validation_stage` (`validation_stage`),
  KEY `idx_validation_result` (`validation_result`),
  KEY `idx_validation_date` (`validated_date`),
  KEY `idx_payroll_audit` (`payroll_entry_id`, `validation_stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- PART 3: INDEXES FOR PERFORMANCE
-- =====================================================

-- Batch approval queries (frequent access)
CREATE INDEX `idx_payroll_gaa_batch_check` 
ON `payroll_gaa_status`(`gaa_violation_flag`);

-- =====================================================
-- PART 4: SAMPLE QUERIES (Note the JOIN pattern)
-- =====================================================

-- Query 1: Get violations in payroll period
/*
SELECT pe.payroll_entry_id, e.employee_id_num, CONCAT(e.lastname, ', ', e.firstname) as name,
       pe.gross, pe.total_deductions, pe.net_pay, pgs.gaa_shortfall_amount
FROM payroll_entries pe
JOIN payroll_gaa_status pgs ON pe.payroll_entry_id = pgs.payroll_entry_id
JOIN employees_tbl e ON pe.employee_id = e.employee_id
WHERE pe.payroll_period_id = 42 
  AND pe.dept_id = 5
  AND pgs.gaa_violation_flag = 1
ORDER BY pgs.gaa_shortfall_amount DESC;
*/

-- Query 2: Verify payroll_entries is NOT modified
/*
DESCRIBE payroll_entries;  -- Should show original columns only
*/

-- Query 3: Check gaa_status exists
/*
SHOW TABLES LIKE 'payroll_gaa_status';
*/

-- =====================================================
-- PART 5: VERIFY CHANGES
-- =====================================================

-- Run these after migration:
-- SELECT COUNT(*) FROM payroll_gaa_status;  -- Should be 0
-- SELECT COUNT(*) FROM gaa_validation_audit; -- Should be 0
-- DESCRIBE payroll_gaa_status;
-- DESCRIBE gaa_validation_audit;

-- =====================================================
-- DONE
-- =====================================================
