-- =====================================================
-- PAYROLL WORKFLOW MIGRATION
-- Date: 2026-03-05
-- Purpose: Add workflow status tracking to payroll system
-- Workflow: DRAFT → REVIEW → APPROVED/DRAFT ← PAID
-- =====================================================

-- =====================================================
-- STEP 1: Add columns to payroll_entries table
-- =====================================================
ALTER TABLE `payroll_entries` ADD COLUMN `status` ENUM('DRAFT', 'REVIEW', 'APPROVED', 'PAID') NOT NULL DEFAULT 'DRAFT' AFTER `emp_type_stamp`;

ALTER TABLE `payroll_entries` ADD COLUMN `submitted_by` INT(11) NULL AFTER `status`;
ALTER TABLE `payroll_entries` ADD COLUMN `submitted_date` DATETIME NULL AFTER `submitted_by`;

ALTER TABLE `payroll_entries` ADD COLUMN `approved_by` INT(11) NULL AFTER `submitted_date`;
ALTER TABLE `payroll_entries` ADD COLUMN `approved_date` DATETIME NULL AFTER `approved_by`;

ALTER TABLE `payroll_entries` ADD COLUMN `marked_paid_by` INT(11) NULL AFTER `approved_date`;
ALTER TABLE `payroll_entries` ADD COLUMN `marked_paid_date` DATETIME NULL AFTER `marked_paid_by`;

ALTER TABLE `payroll_entries` ADD COLUMN `returned_reason` LONGTEXT NULL AFTER `marked_paid_date`;

-- Add indexes for workflow queries
CREATE INDEX `idx_status` ON `payroll_entries`(`status`);
CREATE INDEX `idx_period_dept_status` ON `payroll_entries`(`payroll_period_id`, `dept_id`, `emp_type_stamp`, `status`);

-- =====================================================
-- STEP 2: Create payroll_workflow_transitions table
-- =====================================================
CREATE TABLE IF NOT EXISTS `payroll_workflow_transitions` (
  `transition_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payroll_entry_id` INT(11) NOT NULL,
  `from_status` ENUM('DRAFT', 'REVIEW', 'APPROVED', 'PAID') NOT NULL,
  `to_status` ENUM('DRAFT', 'REVIEW', 'APPROVED', 'PAID') NOT NULL,
  `changed_by` INT(11) NOT NULL,
  `changed_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reason` LONGTEXT NULL COMMENT 'Reason for transition (e.g., reason for returning to DRAFT)',
  `ip_address` VARBINARY(16) NULL,
  `user_agent` VARCHAR(255) NULL,
  FOREIGN KEY (`payroll_entry_id`) REFERENCES `payroll_entries`(`payroll_entry_id`) ON DELETE CASCADE,
  FOREIGN KEY (`changed_by`) REFERENCES `admin_users`(`admin_id`) ON DELETE RESTRICT,
  KEY `idx_payroll_date` (`payroll_entry_id`, `changed_date`),
  KEY `idx_status_transition` (`from_status`, `to_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- STEP 3: Create payroll_workflow_rules table (for configuration)
-- =====================================================
CREATE TABLE IF NOT EXISTS `payroll_workflow_rules` (
  `rule_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `from_status` ENUM('DRAFT', 'REVIEW', 'APPROVED', 'PAID') NOT NULL,
  `to_status` ENUM('DRAFT', 'REVIEW', 'APPROVED', 'PAID') NOT NULL,
  `allows_bulk` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Allow bulk transition, 0=Only single',
  `requires_reason` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=Require reason, 0=Optional',
  `allowed_roles` VARCHAR(255) NOT NULL COMMENT 'Comma-separated roles: admin,supervisor,head',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_transition` (`from_status`, `to_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- STEP 4: Seed workflow rules
-- =====================================================
INSERT INTO `payroll_workflow_rules` (`from_status`, `to_status`, `allows_bulk`, `requires_reason`, `allowed_roles`, `is_active`) VALUES
('DRAFT', 'REVIEW', 1, 0, 'admin,supervisor,head', 1),
('REVIEW', 'APPROVED', 1, 0, 'admin,supervisor', 1),
('REVIEW', 'DRAFT', 1, 1, 'admin,supervisor', 1),
('APPROVED', 'PAID', 1, 0, 'admin,supervisor', 1);

-- =====================================================
-- STEP 5: Verify data integrity
-- =====================================================
-- Check existing payroll entries - set all DRAFT (safe default)
UPDATE `payroll_entries` SET `status` = 'DRAFT' WHERE `status` IS NULL OR `status` = '';

-- =====================================================
-- Done
-- =====================================================
