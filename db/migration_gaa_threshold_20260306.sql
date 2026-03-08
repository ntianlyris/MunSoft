-- =====================================================
-- GAA NET PAY THRESHOLD COMPLIANCE MIGRATION
-- Date: 2026-03-06
-- Purpose: Implement PHP 5,000.00 minimum net pay validation
-- Reference: General Appropriations Act (GAA) requirements
-- =====================================================

-- =====================================================
-- STEP 1: Add GAA violation flag to payroll_entries
-- =====================================================

ALTER TABLE `payroll_entries` 
ADD COLUMN `gaa_violation_flag` TINYINT(1) NOT NULL DEFAULT 0 
COMMENT '0=compliant, 1=net_pay_below_5000_threshold' 
AFTER `status`;

-- Add index for quick violation queries
CREATE INDEX `idx_gaa_violation_flag` ON `payroll_entries`(`gaa_violation_flag`);

-- =====================================================
-- STEP 2: Create GAA audit log table (for COA compliance)
-- =====================================================

CREATE TABLE IF NOT EXISTS `gaa_audit_log` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payroll_entry_id` INT(11) NOT NULL COMMENT 'References payroll_entries.payroll_entry_id',
  `employee_id` INT(11) NOT NULL COMMENT 'References employees_tbl.employee_id',
  `action` ENUM(
    'VALIDATED',
    'BLOCKED',
    'WARNED',
    'APPROVED',
    'OVERRIDE_APPROVED'
  ) NOT NULL COMMENT 'Type of GAA validation action performed',
  `computed_net_pay` DECIMAL(12,2) NOT NULL COMMENT 'Net pay amount at time of validation',
  `threshold_amount` DECIMAL(12,2) NOT NULL DEFAULT 5000.00 COMMENT 'GAA minimum threshold (PHP 5,000.00)',
  `shortfall_amount` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Amount below threshold (if any)',
  `performed_by` INT(11) NOT NULL COMMENT 'User ID who performed validation (FK: admin_users or users)',
  `performed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of validation event',
  `validation_point` ENUM('deduction_entry', 'payroll_save', 'batch_approval') DEFAULT 'payroll_save' COMMENT 'Where in workflow validation occurred',
  `remarks` LONGTEXT NULL COMMENT 'Additional notes (e.g., override reason)',
  
  -- Indexes for compliance reporting
  KEY `idx_payroll_entry_id` (`payroll_entry_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_action` (`action`),
  KEY `idx_validation_point` (`validation_point`),
  KEY `idx_performed_at` (`performed_at`),
  KEY `idx_action_date` (`action`, `performed_at`),
  
  -- Foreign key constraints (optional - adjust table names if different in your schema)
  -- FOREIGN KEY (`payroll_entry_id`) REFERENCES `payroll_entries`(`payroll_entry_id`) ON DELETE CASCADE,
  -- FOREIGN KEY (`employee_id`) REFERENCES `employees_tbl`(`employee_id`) ON DELETE RESTRICT,
  -- FOREIGN KEY (`performed_by`) REFERENCES `admin_users`(`admin_id`) ON DELETE RESTRICT,
  
  CONSTRAINT `fk_gaa_payroll_entry` FOREIGN KEY (`payroll_entry_id`) 
    REFERENCES `payroll_entries`(`payroll_entry_id`) ON DELETE CASCADE
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
  COMMENT='GAA compliance audit log - tracks all minimum net pay threshold validations';

-- =====================================================
-- STEP 3: Create index for audit log queries
-- =====================================================

-- Combined index for period-based compliance reports
CREATE INDEX `idx_gaa_period_audit` 
ON `gaa_audit_log`(`performed_at`, `action`);

-- =====================================================
-- VERIFICATION QUERIES (Run these after migration)
-- =====================================================
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
--   WHERE TABLE_NAME = 'payroll_entries' AND COLUMN_NAME = 'gaa_violation_flag';
-- 
-- SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
--   WHERE TABLE_NAME = 'gaa_audit_log';
-- 
-- DESCRIBE `gaa_audit_log`;
