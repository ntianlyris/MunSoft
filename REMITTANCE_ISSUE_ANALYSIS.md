# Remittance Module Issue Analysis

## Executive Summary

The "remittances of each deduction display the same amount" issue is caused by a **fundamental database schema design flaw**, NOT a display logic error. The system cannot store granular deduction-level data due to missing database columns.

---

## Problem Statement

**User Observation:**
- When viewing remittances, each deduction for the same employee displays the same total amount
- Expected: Different amounts per employee per deduction type
- Actual: Total amount per employee per remittance type (aggregation collapse)

**Root Cause (Two-Fold):**

1. **Database Schema Deficiency** - Critical Design Flaw
2. **Query GROUP BY Clause Error** - Data Aggregation Issue

---

## Technical Analysis

### Part 1: Database Schema Problem

**Current `remittance_details` Table (5 Columns):**
```sql
CREATE TABLE `remittance_details` (
  `remit_detail_id` int(10) UNSIGNED NOT NULL,
  `remittance_id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `remittance_type` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00
)
```

**What PHP Code Attempts to Save (Lines 124-151 in `remittance_handler.php`):**
```php
[
    'employee_id' => $row['employee_id'],
    'config_deduction_id' => $row['config_deduction_id'] ?? null,  // NON-EXISTENT COLUMN
    'govshare_id' => $row['govshare_id'] ?? null,                  // NON-EXISTENT COLUMN
    'amount' => $row['employee_share'],
    'employer_share' => $row['employer_share'] ?? 0                // NON-EXISTENT COLUMN (stored as single 'amount')
]
```

**Schema Issues:**
- ❌ No `config_deduction_id` - Cannot identify which deduction within a type
- ❌ No `govshare_id` - Cannot distinguish government share records
- ❌ No `employee_share` column - Stores as single `amount` field
- ❌ No `employer_share` column - No separation between employee and employer contributions
- ❌ No way to drill down to individual deduction components

**Result:**
- 5 database columns vs. PHP code expects 5+ distinct fields
- Information is **irretrievably lost** during insertion
- No granular deduction breakdowns can be stored or retrieved

---

### Part 2: Query GROUP BY Aggregation Problem

**Current Queries - All 8 Methods Use Same Pattern:**

Location: [`includes/class/Remittance.php`](includes/class/Remittance.php) - Lines 22-510 (all methods)

Example - `GetRemittancePhilHealth()` (Lines 22-92):
```php
LEFT JOIN payroll_deductions c ON b.payroll_entry_id = c.payroll_entry_id
LEFT JOIN employee_deductions_components d ON c.deduction_component_id = d.deduction_component_id
LEFT JOIN config_deductions e ON d.config_deduction_id = e.config_deduction_id
LEFT JOIN govshares f ON d.config_deduction_id = f.govshare_id AND d.employee_deduction_id = f.employee_govshare_id
...
GROUP BY b.employee_id  // ← THIS IS THE PROBLEM
```

**The Problem:**
- `GROUP BY b.employee_id` collapses ALL deductions for one employee into a SINGLE row
- All components (PhilHealth premium, etc.) are SUMMED together by employee
- Result: One total per employee per remittance type (loses individual deduction breakdown)

**Example Scenario:**

Employee 3 in September has:
- PhilHealth Premium (config_deduction_id: 21): 500.00
- Loan Repayment (config_deduction_id: 56): 3000.00
- Other deduction: 150.00
- **Expected Output:** 3 separate lines in remittance_details
  - (Employee 3, config_deduction_id: 21, amount: 500.00)
  - (Employee 3, config_deduction_id: 56, amount: 3000.00)
  - (Employee 3, other, amount: 150.00)
- **Actual Output:** 1 line only (due to GROUP BY and schema limitation)
  - (Employee 3, remittance_type: 'philhealth', amount: 3650.00) ← ALL summed together

---

## Database Structure Comparison

| File | remittance_details Columns | Status |
|------|---------------------------|--------|
| Production Backup (`munsoft_polanco fixed back-up.sql`) | 5 columns only | SAME ISSUE |
| Current Development (`munsoft_polanco.sql`) | 5 columns only | SAME ISSUE |
| Actual Web Code Expects | 5+ distinct fields | MISMATCH |

**Evidence:**
- Production: Lines 2566-2572 show 5-column table
- Development: Lines 1126-1131 show identical 5-column table
- **This is NOT a development-only bug** - Production has the same limitation

---

## Code Evidence

### Evidence #1: remittance_handler.php (Lines 124-151)

Building details array with fields that don't exist in DB:
```php
$details['philhealth'] = array_map(function($row) {
    return [
        'employee_id' => $row['employee_id'],
        'config_deduction_id' => $row['config_deduction_id'] ?? null,  // Trying to save
        'govshare_id' => $row['govshare_id'] ?? null,                  // Trying to save
        'amount'      => $row['employee_share'],
        'employer_share' => $row['employer_share'] ?? 0                // Trying to save
    ];
}, $data['philhealth']);
```

**But when INSERT executes, only these columns are sent:**
- `remittance_id`
- `employee_id`
- `remittance_type`
- `amount`

**The extra fields are SILENTLY DROPPED** (MySQL ignores unmapped columns in INSERT)

### Evidence #2: Remittance.php (Lines 166-170)

```php
ded.deduction_type_id,      // Selected from query
ded.config_deduction_id,    // Selected from query  
gov.govshare_id,            // Selected from query
SUM(IFNULL(ded.employee_share,0)) AS employee_share,
SUM(IFNULL(gov.employer_share,0)) AS employer_share,
```

**But the fundamental issue:** 
Even though these are selected, the `GROUP BY b.employee_id` clause aggregates them away.

---

## Impact Analysis

### What Works:
✅ Total remittance amounts are calculated correctly  
✅ Employee vs. employer shares are calculated correctly (in memory)  
✅ Summary reports show accurate totals  

### What Doesn't Work:
❌ Cannot drill down to individual deduction components  
❌ Cannot audit which specific deductions make up each remittance type  
❌ Cannot distinguish between similar entries  
❌ Cannot separate employee vs. employer shares in stored records  
❌ Historical audit trail is insufficient (only totals stored)  

---

## Required Solutions

### Solution 1: Database Schema Enhancement (REQUIRED)

**ALTER remittance_details table to add missing columns:**

```sql
ALTER TABLE `remittance_details` 
ADD COLUMN `config_deduction_id` INT(11) DEFAULT NULL AFTER `remittance_type`,
ADD COLUMN `govshare_id` INT(11) DEFAULT NULL AFTER `config_deduction_id`,
ADD COLUMN `employee_share` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `amount`,
ADD COLUMN `employer_share` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `employee_share`,
ADD INDEX idx_config_deduction (config_deduction_id),
ADD INDEX idx_govshare (govshare_id),
ADD CONSTRAINT fk_remittance_details_config_deduction 
  FOREIGN KEY (config_deduction_id) REFERENCES config_deductions(config_deduction_id),
ADD CONSTRAINT fk_remittance_details_govshare 
  FOREIGN KEY (govshare_id) REFERENCES govshares(govshare_id);
```

**Resulting remittance_details Table (9 Columns):**
```
- remit_detail_id
- remittance_id (FK)
- employee_id (FK)
- remittance_type (varchar)
- config_deduction_id (FK) ← NEW
- govshare_id (FK) ← NEW
- amount (decimal) ← Original
- employee_share (decimal) ← NEW
- employer_share (decimal) ← NEW
```

### Solution 2: Query GROUP BY Modification (REQUIRED)

**Modify all 8 methods in Remittance.php to NOT aggregate by employee alone.**

Current (Wrong):
```sql
GROUP BY b.employee_id
```

Proposed (Correct):
```sql
GROUP BY b.employee_id, ded.config_deduction_id, gov.govshare_id
```

This ensures each deduction component is stored separately.

### Solution 3: Code Mapping Correction (REQUIRED)

**Update remittance_handler.php to properly map all 5+ fields:**

```php
// Currently: trying to save unmapped columns (silently dropped)
// After fix: should explicitly map and insert:

INSERT INTO remittance_details 
(remittance_id, employee_id, remittance_type, config_deduction_id, govshare_id, amount, employee_share, employer_share) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
```

---

## Migration Path

### Phase 1: Backup & Prepare
1. Backup current database
2. Create migration script
3. Test on development only

### Phase 2: Schema Changes
1. Add new columns to `remittance_details`
2. Update foreign key constraints
3. Add appropriate indexes

### Phase 3: Code Updates
1. Fix all 8 methods in Remittance.php (change GROUP BY clauses)
2. Fix remittance_handler.php (ensure all fields are mapped)
3. Update SaveRemittances method to handle new columns

### Phase 4: Data Reprocessing
1. Clear existing remittance_details records
2. Re-run remittances through the corrected system
3. Verify data integrityis correct

### Phase 5: Testing & Validation
1. Test with multiple employees & deduction types
2. Verify granular data storage
3. Verify historical data integrity
4. User acceptance testing

---

## Deliverables

1. **SQL Migration Script** - Add columns with proper constraints
2. **Updated Remittance.php** - Fix GROUP BY clauses in all 8 methods
3. **Updated remittance_handler.php** - Ensure proper field mapping
4. **Test Data** - Verify correct deduction-level storage
5. **Documentation** - Implementation guide for deployment

---

## Risk Assessment

| Item | Level | Mitigation |
|------|-------|-----------|
| Data loss | MEDIUM | Full backup before migration |
| Query performance | LOW | Indexes on new FK columns |
| Application logic | MEDIUM | Thorough testing of all 8 remittance types |
| Historical data | HIGH | Plan for re-processing existing remittances |
| User impact | MEDIUM | Temporary remittance processing pause during migration |

---

## Conclusion

The "same amount repeating" issue is **not a display bug** but a **fundamental data architecture problem**. The system was designed to store only aggregated totals per employee per remittance type, but the code and business logic expect granular component-level storage.

**Both current and production databases have the same limitation**, indicating this was inherited from the original design.

A comprehensive database schema redesign plus query modification is **required** to properly support the intended functionality.

---

**Analysis Completed:** 2025-11-01  
**Files Analyzed:** 
- [payroll/remittance.php](payroll/remittance.php) - UI
- [includes/class/Remittance.php](includes/class/Remittance.php) - Business Logic  
- [payroll/remittance_handler.php](payroll/remittance_handler.php) - AJAX Handler
- [db/munsoft_polanco.sql](db/munsoft_polanco.sql) - Current DB Schema
- [assets/munsoft_polanco fixed back-up.sql](assets/munsoft_polanco%20fixed%20back-up.sql) - Production Backup
