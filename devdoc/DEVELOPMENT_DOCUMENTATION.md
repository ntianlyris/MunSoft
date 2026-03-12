# IntelliGov Development Documentation

> **Synthesized from:** Notes.txt, BUG_FIX_BLOCKING_LOGIC.md, DEPLOYMENT_CHECKLIST.md, DEPLOYMENT_COMPLETE_STATUS_BASED_EDITING.md, IMPLEMENTATION_CHANGE_SUMMARY.md, IMPLEMENTATION_REPORT.md, IMPLEMENTATION_SUMMARY.md, MOBILE_SETUP_GUIDE.md, PAYROLL_EDITING_FLOW_ANALYSIS.md, PAYROLL_STATUS_BASED_EDITING.md, PAYROLL_WORKFLOW_SETUP.md, PROJECT_COMPLETION_REPORT.md, QUICK_START.md, REMITTANCE_ISSUE_ANALYSIS.md, SECURITY_IMPLEMENTATION.md, TWO_LAYER_BLOCKING_SYSTEM.md  
> **Date Compiled:** March 11, 2026

---

## 1. Project Overview

**IntelliGov** is an integrated HRIS (Human Resource Information System) and Payroll Management system designed for local government use. Built on the AdminLTE 3 (Bootstrap 4) framework, it provides comprehensive employee records management, payroll computation, remittance processing, leave management, and a mobile-first employee self-service dashboard powered by Progressive Web App (PWA) technology.

> **Development History Note:** During the initial phases of development through early 2026, the project was originally named **"MunSoft"**. It was later rebranded to **"IntelliGov"** to better reflect its integration of smart governance features, AI-assisted compliance modules (like the GAA Net Pay Intelligence), and seamless operational capabilities.

**Technology Stack:** PHP 7.4+, MySQL/MariaDB, Bootstrap 4, AdminLTE 3, jQuery, Font Awesome 5, SweetAlert2, DataTables, Service Workers, TCPDF (PDF generation).

---

## 2. Core Modules & Feature Development History

### 2.1 Employee Records Management
- Employee master data including personal info, employment details, and photo uploads.
- Employee photo upload feature with GD library-based automatic square crop and resize (800×800px), dark-themed full-size viewer modal, and history tracking via the `employee_photos` table.
- Photo upload available across all profile pages (admin, payroll, hris, employee portals).

### 2.2 Employee Employments & Service Records
- Employment records linked with earnings for service record generation.
- Earning codes (Sal-Reg, Sal-Cas, PERA, RA, TA, SUBSIST, HAZARD) identify salary types and must remain consistent.
- The `employment_id` (current position) is captured during payroll save to reference historical positions.

### 2.3 Employee Earnings, Deductions & Government Shares
- Earnings, deductions, and government shares are configured per employee and used during payroll generation.
- Confirmation dialogs added before save operations for all three components to prevent accidental data changes.
- Edit blocking enforced when payroll is locked (see Section 3 below).

### 2.4 Payroll Generation & Computation
- Payroll is computed per pay period by department and separated by employment type (Regular vs. Casual).
- Supports monthly and semi-monthly frequencies.
- On save, snapshots of earnings, deductions, and government shares are recorded for audit/history.
- Option to include/exclude specific employees from payroll (added Oct 2025).
- Journal entries auto-generated and separated for Regular and Casual payrolls.

### 2.5 GAA Net Pay Intelligence Module
- An AI-powered, self-contained module enforcing the ₱5,000 General Appropriations Act (GAA) net pay threshold.
- Categorizes net pay into 6 status tiers: CRITICAL (< ₱5,000), DANGER, WARNING, CAUTION, STABLE, and SAFE (> ₱10,000).
- Provides real-time validation during data entry, AI analysis for individual profiles, and batch evaluation of payroll periods.
- Includes a PHP API (`api/gaa_api.php`) and a comprehensive JavaScript UI integration with real-time badges, prediction charts, and headroom bars.

### 2.6 Remittance Processing
- Remittances generated from payroll data, grouped by type (Government: GSIS, PhilHealth, Pag-IBIG, BIR/Tax; Payroll Deductions: Loans, Other Payables).
- Per-employee breakdowns available for both Loans and Other Payables (e.g. PhilAm Life, PMGEA dues).
- PDF printing supported for remittance details.
- **Known schema issue (see Section 5):** The `remittance_details` table only stores aggregated amounts per employee per type; granular deduction-level storage requires a database schema enhancement with additional columns (`config_deduction_id`, `govshare_id`, `employee_share`, `employer_share`).
- **Consistency Fix (Mar 12, 2026):** Implemented strict filtering to ensure remittances are only generated from payrolls with `APPROVED` or `PAID` status.
- **Auto-Cleanup Logic:** Added automatic deletion of existing `remittance_details` records before saving new ones during remittance regeneration. This ensures that corrected payroll deductions (like the Pagibig MPL loan case) are accurately reflected and stale data is replaced.
- **UI & Feedback Enhancements (Mar 12, 2026):** Integrated SweetAlert2 for all notifications, added a loading spinner (`#Loader`) for background processing feedback, and implemented a confirmation dialog before save operations.

### 2.7 Payslip Generation & Printing
- Payslips generated per employee per pay period with full earnings/deductions breakdowns.
- PDF print functionality implemented and tested.
- Summary List of Payroll (SLP) report also printable with column layout adjustments.
- **Payslip Aggregation Fix (Mar 11, 2026):** Enhanced `get_payslip_months.php` to support dual-frequency (monthly/semi-monthly) data aggregation; implements `MAX()` for gross/deductions and `SUM()` for net pay to correctly represent split pay periods on the employee dashboard.

### 2.8 Leave Management
- Leave application submission, approval workflow, and balance tracking.
- Leave types and credits managed via configuration tables.

### 2.10 Emergency Payroll Deletion Module (Mar 11, 2026)
- **Feature:** A high-level administrative bypass tool created for critical data corrections in production environments.
- **Filtering Scope:** Precisely isolates records by `payroll_period_id`, `dept_id`, and `employment_type`.
- **Administrative Bypass:** Unlike standard deletion tools, this module ignores workflow status (`APPROVED`, `PAID`, etc.), allowing administrators to wipe processed payrolls that are otherwise locked by the system.
- **Performance Optimization:** Implemented utilizing MariaDB's native `ON DELETE CASCADE` foreign key constraints, firing a single parent deletion query rather than slow PHP-based manual loops.
- **Security:** Access is strictly restricted to users with both the `Administrator` role and the `Manage System` privilege.
- **Standalone Auditing:** To prevent the cascade from destroying the proof of deletion, actions are logged to a detached `system_logs` table instead of the standard per-entry audit trail.
### 2.11 Mobile Dashboard & Employee Portal (Mar 2026)
- Complete mobile-first redesign of the employee portal with four dashboard cards: **Profile**, **Payrolls**, **Payslips**, **Leave**.
- Quick actions grid, recent activity sections, and modal dialogs for detailed views.
- **PWA Features:** Service Worker for offline caching, `manifest.json` for app installation, offline fallback page with auto-reconnect detection.
- Responsive breakpoints: Mobile (<576px, 1-column) → Tablet (577–768px, 2-column) → Desktop (768px+, 4-column).
- Dark mode, accessibility (ARIA labels, keyboard nav, high contrast), and print styles included.
- Five new PHP functions added to `includes/view/view.php`: `GetEmployeePayrollRecords()`, `GetEmployeeLeaveBalance()`, `GetEmployeeLeaveApplications()`, `GetEmployeeProfileSummary()`, `GetEmployeePayslipHistory()`.

---

## 3. Payroll Edit Blocking System

### 3.1 Two-Layer Blocking Architecture (Mar 2026)

A comprehensive dual-protection system prevents unauthorized modifications to payroll-related data:

**Layer 1 — Period-Based Blocking (Semi-Monthly Consistency):**
- When a semi-monthly 1st-half payroll is locked, earnings/deductions/government shares edits are blocked to prevent inconsistency between 1st and 2nd half calculations.
- Monthly payroll skips this layer entirely.

**Layer 2 — Status-Based Blocking (Workflow Integrity):**
- Edits are only allowed when payroll status is `DRAFT`.
- Once submitted (`REVIEW`), approved (`APPROVED`), or paid (`PAID`), all configuration edits are blocked.
- Users must explicitly return payroll to `DRAFT` status to make corrections.

**Backend implementation:** `earnings_handler.php`, `deductions_handler.php`, `govshare_handler.php` — each performs both layer checks before allowing edits.  
**Frontend handling:** Dynamic error messages via SweetAlert2, differentiating between period-based and status-based blocking reasons.

### 3.2 Bug Fix — Blocking Logic (Mar 5, 2026)
- Root cause: The `GetLastLockedPayrollPeriodByEmployee()` query excluded `DRAFT` status entries, meaning newly saved payrolls were invisible to the blocking logic.
- Fix: Removed `AND b.status NOT IN ('DRAFT')` condition so `locked_period = 1` alone determines editability.
- Key design principle: `locked_period` (config editability) and `status` (workflow state) are separate concerns.

### 3.3 Payslip Download/Print Blocking System (Mar 10, 2026)
A dual-layer security restriction has been implemented to control access to employee payslips:
- **Status-Based Blocking:** Payslips are strictly locked and unviewable unless the corresponding payroll period calculates to an `APPROVED` or `PAID` status.
- **Date-Based Lock:** Regular employees are prevented from downloading their payslips until the current payroll period has officially concluded (e.g., March 1-15 period becomes available at midnight on March 16).
- **Role-Based Privilege Bypass:** Administrative users carrying the `Manage System` global privilege bypass the Date-Based Lock to allow early access to printing approved payslips.
- **Dual-Layer Enforcement:** 
  - *Frontend:* Blocks UI elements in `employee/index.php` with disabled "Locked" buttons if eligibility fails.
  - *Backend Gatekeeper:* The `Payslip->IsPayslipDownloadable()` method intercepts direct PDF API generation (`prints/print_payslip.php`) and internal previews (`payroll/payslip_handler.php`), outright neutralizing URL/Endpoint manipulation exploits.

### 3.4 Remittance Save Blocking (Mar 12, 2026)
To maintain the integrity of finalized financial reports, a blocking mechanism prevents updating or regenerating remittances once they are officially remitted:
- **Locking Criteria:** Any remittance record with `status = 'Remitted'` is considered locked.
- **Backend Gatekeeper:** The `Remittance->IsRemittanceLocked($period, $types)` method performs an atomic check against the `remittances` table before any save or delete operation in the `SaveRemittances` flow.
- **Atomic Operations:** If even one remittance type in a batch (e.g., GSIS in a monthly run) is locked, the entire batch save is blocked to ensure consistency.

---

## 4. Payroll Workflow Status System

### 4.1 Workflow States
| Status | Badge | Description |
|--------|-------|-------------|
| DRAFT | Gray | Payroll created, fully editable, can regenerate |
| REVIEW | Yellow | Submitted for review, edits blocked |
| APPROVED | Cyan | Approved, ready for payment |
| PAID | Green | Payment processed, permanently locked |

### 4.2 Transitions
- DRAFT → REVIEW (submit), REVIEW → APPROVED (approve), REVIEW → DRAFT (return with reason), APPROVED → PAID (mark paid, final).
- **Administrative Override:** Emergency Delete bypasses the entire workflow graph for specialized cleanup (logs to `system_logs`).
- Bulk status updates with atomic pre-validation (all-or-nothing).
- All transitions logged in `payroll_workflow_transitions` table with user, IP, timestamp, and reason.

### 4.3 Database Schema
- `payroll_entries` table extended with: `status`, `submitted_by/date`, `approved_by/date`, `marked_paid_by/date`, `returned_reason`.
- New tables: `payroll_workflow_transitions` (audit trail), `payroll_workflow_rules` (configurable rules).
- Migration file: `db/payroll_workflow_migration_20260305.sql`.

### 4.4 Payroll Entry Edit/Delete
- Edit allowed only in `DRAFT` status; delete allowed only in `DRAFT`.
- Net pay validation (±0.01 tolerance) before save.
- Full audit trail in `payroll_entries_audit` table.

---

## 5. Known Issues & Pending Items

### 5.1 Remittance Schema Limitation
- The `remittance_details` table lacks `config_deduction_id`, `govshare_id`, `employee_share`, and `employer_share` columns.
- The `GROUP BY employee_id` clause in all 8 Remittance class methods collapses per-deduction detail into aggregated totals.
- **Required fix:** ALTER table to add columns + modify GROUP BY clauses + update INSERT mappings. Migration plan documented with 5 phases: backup, schema change, code update, data reprocessing, testing.

### 5.2 Pending Development Items
- Export CSV button for remittance breakdowns (placeholder ready, backend not yet implemented).
- Email delivery for payslips/remittances (optional enhancement).
- Role-based permission matrix for workflow actions (currently basic role checks).
- End-to-end testing for payroll workflow transitions.

---

## 6. Security Implementation

### 6.1 Role-Based Access Control (RBAC)
- Employee users are restricted from edit/delete operations across all modules.
- **Frontend:** CSS attribute selectors + JavaScript fallback hide action buttons for Employee role via `body[data-user-role="Employee"]`.
- **Backend:** `CheckModifyPermission()` function in all `save_settings.php` handlers returns HTTP 403 for Employee role attempts.
- Applied across HRIS, Admin, and Payroll modules.

### 6.2 General Security Measures
- Session-based authentication with 30-minute timeout.
- CSRF protection, XSS prevention (htmlspecialchars), SQL injection protection.
- Input sanitization and type validation on all endpoints.
- Audit logging with IP address and user agent capture.
- Sensitive data excluded from PWA cache.
- HTTPS required for production (Service Worker requirement).

---

## 7. Mobile Dashboard Deployment Guide

### 7.1 Prerequisites
- PHP 7.4+, MySQL/MariaDB, Apache/Nginx with mod_rewrite, HTTPS (or localhost for dev).
- Session must set `$_SESSION['employee_id']` on login.

### 7.2 Key Files
| File | Purpose |
|------|---------|
| `service-worker.js` | Offline caching & background sync |
| `offline.html` | Fallback page when no internet |
| `manifest.json` | PWA app metadata & shortcuts |
| `css/employee-mobile.css` | Mobile-first responsive styles (450+ lines) |
| `employee/index.php` | Redesigned 4-card dashboard |
| `includes/view/view.php` | 5 new employee data functions |
| `includes/layout/head.php` | PWA meta tags & mobile CSS link |

### 7.3 Caching Strategy
- **Static assets:** Cache-first (CSS, JS, fonts, images).
- **API calls:** Network-first with cache fallback.
- **Dynamic data:** Stale-while-revalidate.
- CACHE_NAME versioning for cache invalidation on updates.

### 7.4 Performance Targets
- Lighthouse: 90+ across all categories.
- FCP ~1.5s, LCP ~2.5s, TTI ~3s, CLS <0.1.

---

## 8. Database Migration Files Reference

| Migration File | Purpose |
|---------------|---------|
| `db/munsoft_polanco.sql` | Main database schema |
| `db/migration_employee_photos.sql` | Employee photos table |
| `db/payroll_migration_20260305.sql` | Payroll audit & config columns |
| `db/payroll_workflow_migration_20260305.sql` | Workflow status & transition tables |

---

## 9. Future Recommendations

1. **Remittance Schema Redesign** — Implement the documented ALTER TABLE + code changes to support granular deduction-level storage in remittance records.
2. **Role-Based Permission Matrix** — Expand RBAC beyond Employee restriction to include granular per-operation permissions for HR, Finance, and Manager roles.
3. **Email Notifications** — Add automated email alerts for payroll status changes, payslip availability, and leave approvals.
4. **Audit Dashboard** — Build an admin UI to view and filter payroll audit trail and workflow transition history.
5. **Photo Enhancements** — WebP format optimization, batch upload, and AI-based face-centering for crop.
6. **Push Notifications** — Leverage PWA push notifications for payroll release and leave status updates.
7. **Multi-Language Support** — Internationalization for broader deployment.
8. **Biometric/2FA Authentication** — Stronger auth for production environments.
9. **Report Exports** — Expand CSV/Excel export across all modules (payroll, remittance, service records).
10. **Performance Monitoring** — Integrate real-time performance logging and analytics dashboard.

---

*This document consolidates all development notes, implementation plans, bug reports, and deployment guides into a single reference. For source code changes, refer to the respective PHP/JS files mentioned in each section.*
