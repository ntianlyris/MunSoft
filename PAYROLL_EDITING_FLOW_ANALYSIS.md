# 📊 Payroll System Flow Analysis - Employee Earnings & Deductions Editing

**Date:** March 5, 2026  
**Analysis Scope:** Complete payroll workflow with blocking mechanisms for earnings/deductions edits based on payroll period status

---

## 🎯 Executive Summary

The MunSoft payroll system implements a **comprehensive blocking mechanism** that prevents editing of employee earnings and deductions during the **first half of semi-monthly payroll periods** if payroll has already been generated and saved. This protection prevents payroll calculation inconsistencies.

---

## 📈 Complete Payroll Workflow

```
┌─────────────────────────────────────────────────────────────────┐
│                    EMPLOYEE EARNINGS & DEDUCTIONS                │
│                     Configuration Phase (SETUP)                  │
│                                                                   │
│  - Employee earnings rates added via Hris module                 │
│  - Employee deductions set up                                    │
│  - Government shares configuration                               │
└─────────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────────┐
│                  PAYROLL GENERATION & SAVING                     │
│                  (payroll/payroll_records.php)                   │
│                                                                   │
│  1. Select Period (Monthly or Semi-Monthly)                      │
│  2. Select Department                                            │
│  3. Select Employment Type (Regular/Casual)                      │
│  4. System Computes Payroll Based on:                            │
│     - Latest active employee earnings                            │
│     - Latest active employee deductions                          │
│     - Latest active government shares                            │
│     - Current payroll frequency (monthly/semi-monthly)           │
│  5. Payroll Entries Created with Status: DRAFT                   │
│     - Gross pay calculated                                       │
│     - Deductions & gov shares applied                            │
│     - For semi-monthly: May be in 1st or 2nd half               │
└─────────────────────────────────────────────────────────────────┘
                            ↓
        ┌─────────────────────────────────────────────────┐
        │      IS PAYROLL ALREADY SAVED?                  │
        └─────────────────────────────────────────────────┘
                    ↙                           ↖
              YES (LOCKED)                  NO (CAN STILL EDIT)
                ↓                               ↓
        ┌──────────────────────┐      ┌──────────────────────┐
        │   BLOCKING LOGIC      │      │   EDITING ALLOWED    │
        │   APPLIED             │      │   FOR THIS PAYROLL   │
        │                       │      │                      │
        │ • Earnings edit       │      │ • Still in memory    │
        │   BLOCKED             │      │ • Not yet locked     │
        │ • Deductions edit     │      │ • Can modify rates   │
        │   BLOCKED             │      │ • Need to resave     │
        │ • Gov shares edit     │      │                      │
        │   BLOCKED             │      │                      │
        └──────────────────────┘      └──────────────────────┘
                ↓                               ↓
        BLOCK_EDIT ERROR              Save PayrollEntry()
        RESPONSE SENT                 Status: DRAFT
                ↓                               ↓
    JavaScript Alert:              ┌──────────────────────┐
    "Cannot edit. Already        │ PAYROLL WORKFLOW     │
     applied in locked           │ STATUS ENGINE        │
     payroll period"             │                      │
                                  │ DRAFT → REVIEW       │
                                  │ REVIEW → APPROVED    │
                                  │ APPROVED → PAID      │
                                  │ PAID → FINAL         │
                                  └──────────────────────┘
```

---

## 🔍 Detailed Blocking Mechanism Analysis

### A. WHEN DOES BLOCKING OCCUR?

#### **Condition 1: Semi-Monthly Payroll Frequency**
```
IF payroll_frequency == 'semi-monthly' AND last_locked_period_exists
   AND locked_period is in FIRST HALF (dates 1-15)
THEN
   Block edit_employee_earnings ❌
   Block edit_employee_deductions ❌
   Block edit_employee_govshares ❌
END IF
```

#### **Condition 2: Detection Logic**
- **File:** `includes/class/Payroll.php`
- **Method:** `GetLastLockedPayrollPeriodByEmployee($employee_id)`
  - Checks database for latest `locked_period = 1` AND `status NOT IN ('DRAFT')`
  - Returns the locked period dates if exists
  
- **File:** `includes/class/Payroll.php`
- **Method:** `IsSecondHalfOfMonth($date_start)`
  - Extracts day from `$date_start`
  - If day > 15: It's second half (EDITS ALLOWED ✅)
  - If day <= 15: It's first half (EDITS BLOCKED ❌)

---

## 📋 Employee Earnings Blocking Implementation

### **File:** `payroll/earnings_handler.php` (Lines 59-92)

```php
case 'edit_employee_earnings':
    // Step 1: Get the employee's last locked payroll period
    $last_locked_period = $Payroll->GetLastLockedPayrollPeriodByEmployee($employee_id);
    $locked_start_date = $last_locked_period['date_start'] ?? null;
    
    // Step 2: Get active payroll frequency
    $active_frequency = $Payroll->GetCurrentActiveFrequency();
    $frequency = $active_frequency['freq_code'] ?? 'monthly';

    // Step 3: Apply blocking logic
    if($frequency == 'semi-monthly' && $locked_start_date) {
        $is_second_half = $Payroll->IsSecondHalfOfMonth($locked_start_date);
        
        if(!$is_second_half) {  // First half detected
            $json_data = '{"result":"block_edit"}';  // BLOCK IT
            echo $json_data;
            exit;
        }
    }
    
    // Step 4: If not blocked, proceed with normal edit
    // ... (continue with edit logic)
```

### **Frontend Handler:** `payroll/scripts/earnings.js` (Lines 142-154)

```javascript
else if(result == 'block_edit'){
    Swal.fire({
        title: 'Error',
        text: 'Cannot save/edit earnings. Employee earnings are already applied in the previous (1st-half) locked payroll period.',
        icon: 'error',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ok'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.reload();
        }
    });
}
```

---

## 📋 Employee Deductions Blocking Implementation

### **File:** `payroll/deductions_handler.php` (Lines 54-81)

```php
case 'edit_employee_deductions':
    // Step 1: Get the employee's last locked payroll period
    $last_locked_period = $Payroll->GetLastLockedPayrollPeriodByEmployee($employee_id);
    $locked_start_date = $last_locked_period['date_start'] ?? null;
    
    // Step 2: Get active payroll frequency
    $active_frequency = $Payroll->GetCurrentActiveFrequency();
    $frequency = $active_frequency['freq_code'] ?? 'monthly';

    // Step 3: Apply blocking logic
    if($frequency == 'semi-monthly' && $locked_start_date) {
        $is_second_half = $Payroll->IsSecondHalfOfMonth($locked_start_date);
        
        if(!$is_second_half) {  // First half detected
            $json_data = '{"result":"block_edit"}';  // BLOCK IT
            echo $json_data;
            exit;
        }
    }
    
    // Step 4: If not blocked, proceed with normal edit
    // ... (continue with edit logic)
```

### **Frontend Handler:** `payroll/scripts/deductions.js` (Lines 93-105)

```javascript
else if(result == 'block_edit'){
    Swal.fire({
        title: 'Error',
        text: 'Cannot save/edit deduction. Employee deduction is already applied in the previous (1st-half) locked payroll period.',
        icon: 'error',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ok'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.reload();
        }
    });
}
```

---

## 💡 Employee Government Shares Blocking Implementation

### **File:** `payroll/govshare_handler.php` (Lines 195-221)

```php
case 'save_employee_govshares':
    // Step 1: Get the employee's last locked payroll period
    $last_locked_period = $Payroll->GetLastLockedPayrollPeriodByEmployee($employee_id);
    $locked_start_date = $last_locked_period['date_start'] ?? null;
    
    // Step 2: Get active payroll frequency
    $active_frequency = $Payroll->GetCurrentActiveFrequency();
    $frequency = $active_frequency['freq_code'] ?? 'monthly';

    // Step 3: Apply blocking logic
    if($frequency == 'semi-monthly' && $locked_start_date) {
        $is_second_half = $Payroll->IsSecondHalfOfMonth($locked_start_date);
        
        if(!$is_second_half) {  // First half detected
            echo json_encode([
                'status' => 'block_edit',
                'message' => 'Cannot save/modify government shares. Employee government shares are already applied in the previous (1st-half) locked payroll period.'
            ]);
            exit;
        }
    }
```

### **Frontend Handler:** `payroll/scripts/govshares.js` (Line 256)

```javascript
} else if (res.status === 'block_edit') {
    // Handle block_edit response from govshare_handler
```

---

## 📊 Payroll Workflow Status Engine

### **NEW Workflow Implementation (March 5, 2026)**

The system now has a **4-status workflow** for payroll entries:

#### **Workflow States:**

| Status | Color | Meaning | Transitions To |
|--------|-------|---------|-----------------|
| **DRAFT** | Gray | Payroll created, awaiting review | REVIEW |
| **REVIEW** | Yellow | Submitted for review, awaiting approval | APPROVED, DRAFT |
| **APPROVED** | Cyan | Approved, ready for payment | PAID |
| **PAID** | Green | Payment processed, final state | (None - locked) |

#### **Workflow Rules:**
- ✅ DRAFT → REVIEW (Submit for Review)
- ✅ REVIEW → APPROVED (Approve)
- ✅ REVIEW → DRAFT (Return to Draft - requires reason)
- ✅ APPROVED → PAID (Mark as Paid - final)
- ✅ PAID → No further transitions (Locked state)

#### **Implementation Files:**
- [includes/class/Payroll.php](includes/class/Payroll.php) - 5 new methods for workflow
- [payroll/payroll_handler.php](payroll/payroll_handler.php) - 3 new handler cases
- [payroll/scripts/payrolls.js](payroll/scripts/payrolls.js) - 6 new workflow functions
- [db/payroll_workflow_migration_20260305.sql](db/payroll_workflow_migration_20260305.sql) - Database schema

---

## 🔄 Complete End-to-End Payroll Flow

### **Step 1: Configuration Phase**
```
Employee Master Data → Earnings Setup → Deductions Setup → Gov Shares Setup
```
- **No payroll period lockdown applies here**
- Edits are allowed freely (no payroll generated yet)

### **Step 2: Payroll Generation**
```
SELECT Period (e.g., March 1-15) + Department + Employment Type
                        ↓
             SYSTEM COMPUTES PAYROLL
                        ↓
      Using LATEST ACTIVE employee config
      (earnings, deductions, gov shares)
                        ↓
         PAYROLL ENTRIES CREATED
         With Status: DRAFT
         locked_period: 1 (YES)
```

### **Step 3: Post-Save Blocking Check**
```
IF frequency == semi-monthly AND locked_period exists:
   
   IF date_start is in FIRST HALF (1-15):
      ❌ Earnings edits BLOCKED
      ❌ Deductions edits BLOCKED
      ❌ Gov shares edits BLOCKED
      ✅ Payroll entries can still be edited/deleted (DRAFT status)
      
   ELSE (SECOND HALF 16-31):
      ✅ Earnings edits ALLOWED
      ✅ Deductions edits ALLOWED
      ✅ Gov shares edits ALLOWED
      ✅ Payroll entries can still be edited/deleted
```

### **Step 4: Workflow Progression**
```
DRAFT (Auto-set on Save)
  ↓
[Managers may modify/delete if status=DRAFT]
  ↓
SUBMIT → REVIEW (Manual submit action)
  ↓
[Awaiting approval]
  ↓
APPROVE → APPROVED (Manager approves)
  ↓
[Ready for payment]
  ↓
MARK PAID → PAID (Final state, locked)
  ↓
[No further transitions possible]
```

---

## ⚠️ Why This Blocking Exists

### **Problem Being Solved:**

In a **semi-monthly payroll system** with 2 pay periods per month:
- **1st Half:** March 1-15
- **2nd Half:** March 16-31

If an employee's earnings/deductions are updated AFTER the 1st half payroll is processed:
- The 2nd half payroll would use the NEW rates
- But employee would be paid for 1st half with OLD rates
- This creates **payroll calculation inconsistency**

### **Solution Implemented:**

**Lock down employee config during 1st half:**
```
Once 1st half payroll is saved (LOCKED):
  - Cannot edit earnings rates
  - Cannot edit deduction amounts
  - Cannot edit gov share percentages
Until:
  - Employee reaches 2nd half period (can then update for consistency)
```

This ensures **uniform payroll calculations** throughout the month.

---

## 🛡️ Current Protection Levels

### **Payroll Entries Editing:**
- ✅ Can edit/delete DRAFT payroll entries (regardless of period)
- ❌ Cannot edit REVIEW/APPROVED/PAID entries (locked by workflow status)

### **Employee Configuration Editing:**
- ✅ Can edit earnings/deductions in 2nd half of month
- ❌ Cannot edit earnings/deductions if 1st half is locked
- ✅ Can always edit earnings/deductions for monthly frequency payroll

### **Workflow Auditing:**
- ✅ All status transitions logged with:
  - User ID
  - Timestamp
  - IP address
  - Reason (for returns)
  - From/To status

---

## 📝 Testing Checklist

### **Test Case 1: Semi-Monthly with 1st Half Locked**
- [ ] Create payroll for March 1-15 (semi-monthly)
- [ ] Save payroll (status=DRAFT, locked_period=1)
- [ ] Try to edit employee earnings
- **Expected:** Block_edit error message ✅

### **Test Case 2: Semi-Monthly with 2nd Half**
- [ ] Create payroll for March 16-31 (semi-monthly)
- [ ] Save payroll
- [ ] Try to edit employee earnings
- **Expected:** Edit succeeds ✅

### **Test Case 3: Monthly Payroll**
- [ ] Create payroll for March 1-31 (monthly)
- [ ] Save payroll
- [ ] Try to edit employee earnings
- **Expected:** Edit succeeds (monthly has no blocking) ✅

### **Test Case 4: Workflow Transitions**
- [ ] DRAFT → REVIEW (Submit for Review) ✅
- [ ] REVIEW → APPROVED (Approve) ✅
- [ ] REVIEW → DRAFT (Return with reason) ✅
- [ ] APPROVED → PAID (Mark as Paid) ✅
- [ ] PAID → No transitions available ✅

---

## 📊 Data Flow Diagram

```
┌──────────────────────────────────────────────────────────────┐
│              PAYROLL RECORDS PAGE (payroll_records.php)       │
│                                                               │
│  [Period Selector] [Department] [Emp Type]                   │
│           ↓             ↓              ↓                      │
│  ┌────────────────────────────────────────────────────────┐  │
│  │  JavaScript: RetrievePayroll()                         │  │
│  │  - Calls: payroll_handler.php?action=fetch_payroll    │  │
│  │  - Gets: All payroll entries for selection            │  │
│  │  - Displays: Table with status, amount, buttons       │  │
│  └────────────────────────────────────────────────────────┘  │
│           ↓                                                    │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ Display Payroll Entries with Workflow Buttons:          │ │
│  │                                                          │ │
│  │ Status: DRAFT [Gray]                                   │ │
│  │   Buttons: [View] [Submit] [Delete]                    │ │
│  │                                                          │ │
│  │ Status: REVIEW [Yellow]                                │ │
│  │   Buttons: [View] [Approve] [Return] [History]         │ │
│  │                                                          │ │
│  │ Status: APPROVED [Cyan]                                │ │
│  │   Buttons: [View] [Mark Paid] [History]                │ │
│  │                                                          │ │
│  │ Status: PAID [Green]                                   │ │
│  │   Buttons: [View] [History]                            │ │
│  └─────────────────────────────────────────────────────────┘ │
│           ↓                                                    │
│  [User clicks action button]                                  │
│           ↓                                                    │
└──────────────────────────────────────────────────────────────┘
           ↓
┌──────────────────────────────────────────────────────────────┐
│         JAVASCRIPT (payrolls.js)                             │
│                                                               │
│  - submitForReview([ids])                                    │
│  - approvePayroll([ids])                                     │
│  - returnToDraft([ids], reason)                              │
│  - markAsPaid([ids])                                         │
│                                                               │
│  All do:                                                      │
│  1. Show SweetAlert confirmation                             │
│  2. AJAX POST to payroll_handler.php                         │
│  3. Call: update_payroll_status_bulk                        │
│  4. Refresh table on success                                 │
└──────────────────────────────────────────────────────────────┘
           ↓
┌──────────────────────────────────────────────────────────────┐
│         PAYROLL HANDLER (payroll_handler.php)               │
│         Case: 'update_payroll_status_bulk'                  │
│                                                               │
│  1. Validate session                                         │
│  2. Get all payroll entries by IDs                           │
│  3. Pre-validate ALL transitions (fail all if any bad)      │
│  4. Execute UPDATE if all valid                             │
│  5. Log to payroll_workflow_transitions                      │
│  6. Return success/error + counts                           │
└──────────────────────────────────────────────────────────────┘
           ↓
┌──────────────────────────────────────────────────────────────┐
│         PAYROLL CLASS (includes/class/Payroll.php)           │
│         Method: BulkUpdatePayrollStatus()                    │
│                                                               │
│  1. Map intval all IDs                                       │
│  2. Validate new_status is in [DRAFT/REVIEW/APPROVED/PAID]  │
│  3. Query: SELECT payroll_entry_id, status FROM entries    │
│  4. For EACH entry:                                          │
│     - Call: ValidateStatusTransition(current, new)          │
│     - If invalid: add to failed_ids array                   │
│  5. If any failed: Return error (FAIL ALL)                  │
│  6. If all valid:                                            │
│     - UPDATE payroll_entries SET status = new_status       │
│     - Add submitted_by/date OR approved_by/date             │
│     - INSERT INTO payroll_workflow_transitions              │
│  7. Return: {'status': 'success', 'affected': N}            │
└──────────────────────────────────────────────────────────────┘
```

---

## 🎯 Summary of Current Implementation Status

### **✅ IMPLEMENTED:**
1. **4-Status Workflow System** (DRAFT → REVIEW → APPROVED → PAID)
2. **Payroll Entry Workflow Transitions** with validation
3. **Bulk Status Updates** with atomic pre-validation
4. **Audit Trail Logging** (user, IP, timestamp, reason)
5. **Earnings Edit Blocking** during 1st half locked periods
6. **Deductions Edit Blocking** during 1st half locked periods
7. **Government Shares Edit Blocking** during 1st half locked periods
8. **Workflow Status Badges** (color-coded by status)
9. **Conditional Action Buttons** (per current status)
10. **Transaction History** (viewable per payroll entry)

### **⏳ PENDING:**
1. **Database Migration Execution** - SQL migration file ready, needs to be imported
2. **End-to-End Testing** - All features coded, awaiting user testing
3. **Role-Based Permissions** (Optional) - Can restrict actions per role

### **🔒 Security Features Implemented:**
- Session validation on all endpoints
- Input sanitization and type validation
- SQL injection prevention (parameterized queries)
- Status transition validation before execution
- Audit logging with IP/user agent capture
- Pre-validation before bulk operations (atomic execution)

---

## 🚀 Next Actions

1. **Execute Database Migration:**
   ```sql
   -- Import file: db/payroll_workflow_migration_20260305.sql
   -- This adds workflow tables and columns to payroll_entries
   ```

2. **Verify Blocking Logic:**
   - Test earnings edit blocking in semi-monthly 1st half
   - Test deductions edit blocking in semi-monthly 1st half
   - Test gov shares edit blocking in semi-monthly 1st half
   - Verify 2nd half allows edits

3. **Test Workflow Transitions:**
   - Complete test cases from checklist above

4. **Deploy to Production:**
   - After successful testing
   - Monitor audit logs for issues

---

## 📚 Related Files Reference

| Component | File | Lines | Purpose |
|-----------|------|-------|---------|
| Backend Logic | [payroll/earnings_handler.php](payroll/earnings_handler.php) | 59-92 | Edit earnings blocking |
| Backend Logic | [payroll/deductions_handler.php](payroll/deductions_handler.php) | 54-81 | Edit deductions blocking |
| Backend Logic | [payroll/govshare_handler.php](payroll/govshare_handler.php) | 195-221 | Edit gov shares blocking |
| DB Checking | [includes/class/Payroll.php](includes/class/Payroll.php) | 439+ | GetLastLockedPayrollPeriodByEmployee() |
| Date Logic | [includes/class/Payroll.php](includes/class/Payroll.php) | 520+ | IsSecondHalfOfMonth() |
| Frontend Handler | [payroll/scripts/earnings.js](payroll/scripts/earnings.js) | 142-154 | Show block_edit error |
| Frontend Handler | [payroll/scripts/deductions.js](payroll/scripts/deductions.js) | 93-105 | Show block_edit error |
| Frontend Handler | [payroll/scripts/govshares.js](payroll/scripts/govshares.js) | 256 | Show block_edit error |
| Workflow Status | [payroll/scripts/payrolls.js](payroll/scripts/payrolls.js) | 960-1100+ | 6 workflow functions |
| Workflow Setup | [db/payroll_workflow_migration_20260305.sql](db/payroll_workflow_migration_20260305.sql) | - | Schema migration |

