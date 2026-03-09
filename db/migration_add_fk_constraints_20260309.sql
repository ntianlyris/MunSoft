-- ============================================================
-- Migration: Add missing foreign key constraints
-- Date: 2026-03-09
-- Purpose: Enforce referential integrity on payroll_deductions
--          and payroll_govshares tables.
-- ============================================================

-- Verify no orphaned records exist before adding constraints
SELECT 'Orphan check: payroll_deductions' AS check_name,
       COUNT(*) AS orphan_count
FROM payroll_deductions pd
LEFT JOIN payroll_entries pe ON pd.payroll_entry_id = pe.payroll_entry_id
WHERE pe.payroll_entry_id IS NULL;

SELECT 'Orphan check: payroll_govshares' AS check_name,
       COUNT(*) AS orphan_count
FROM payroll_govshares pg
LEFT JOIN payroll_entries pe ON pg.payroll_entry_id = pe.payroll_entry_id
WHERE pe.payroll_entry_id IS NULL;

-- Add FK constraint to payroll_deductions
ALTER TABLE `payroll_deductions`
  ADD CONSTRAINT `fk_pd_payroll_entry`
  FOREIGN KEY (`payroll_entry_id`)
  REFERENCES `payroll_entries` (`payroll_entry_id`)
  ON DELETE CASCADE;

-- Add FK constraint to payroll_govshares
ALTER TABLE `payroll_govshares`
  ADD CONSTRAINT `fk_pg_payroll_entry`
  FOREIGN KEY (`payroll_entry_id`)
  REFERENCES `payroll_entries` (`payroll_entry_id`)
  ON DELETE CASCADE;

-- Verify constraints were added
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'munsoft_polanco'
  AND TABLE_NAME IN ('payroll_deductions', 'payroll_govshares')
  AND REFERENCED_TABLE_NAME IS NOT NULL;
