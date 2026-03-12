-- RBAC System Migration: Advanced Permission-Based Model
-- Date: March 12, 2026

-- 1. Insert New Permissions (IGNORE if already exists)
INSERT IGNORE INTO permissions_tbl (perm_desc) VALUES 
('Manage System'),
('Update Data'),
('Manage HR'),
('Manage Payroll'),
('Access HR'),
('Access Payroll');

-- 2. Insert New Roles (IGNORE if already exists)
-- Roles 8 and 9 are designated for Updaters
INSERT IGNORE INTO roles_tbl (roleID, roleName) VALUES 
(8, 'HR Updater'),
(9, 'Payroll Updater');

-- 3. Clear existing role mappings for standardized roles 
-- (Optional: only if you want a full reset of these roles)
-- DELETE FROM role_perm_tbl WHERE roleID IN (1, 2, 4, 5, 8, 9);

-- 4. Map Permissions to Roles
-- Note: Using subqueries to get perm_id to ensure robustness

-- Administrator (Role 1) & SysDeveloper (Role 2) -> Manage System
INSERT IGNORE INTO role_perm_tbl (roleID, perm_id) 
SELECT 1, perm_id FROM permissions_tbl WHERE perm_desc = 'Manage System';
INSERT IGNORE INTO role_perm_tbl (roleID, perm_id) 
SELECT 2, perm_id FROM permissions_tbl WHERE perm_desc = 'Manage System';

-- HR (Role 4) -> Access HR, Update Data, Manage HR
INSERT IGNORE INTO role_perm_tbl (roleID, perm_id) 
SELECT 4, perm_id FROM permissions_tbl WHERE perm_desc IN ('Access HR', 'Update Data', 'Manage HR');

-- Payroll Master (Role 5) -> Access Payroll, Update Data, Manage Payroll
INSERT IGNORE INTO role_perm_tbl (roleID, perm_id) 
SELECT 5, perm_id FROM permissions_tbl WHERE perm_desc IN ('Access Payroll', 'Update Data', 'Manage Payroll');

-- HR Updater (Role 8) -> Access HR, Update Data
INSERT IGNORE INTO role_perm_tbl (roleID, perm_id) 
SELECT 8, perm_id FROM permissions_tbl WHERE perm_desc IN ('Access HR', 'Update Data');

-- Payroll Updater (Role 9) -> Access Payroll, Update Data
INSERT IGNORE INTO role_perm_tbl (roleID, perm_id) 
SELECT 9, perm_id FROM permissions_tbl WHERE perm_desc IN ('Access Payroll', 'Update Data');

-- 5. Verification Check (Optional)
-- SELECT r.roleName, p.perm_desc 
-- FROM role_perm_tbl rp 
-- JOIN roles_tbl r ON rp.roleID = r.roleID 
-- JOIN permissions_tbl p ON rp.perm_id = p.perm_id
-- ORDER BY r.roleID;
