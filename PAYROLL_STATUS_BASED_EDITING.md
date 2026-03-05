# 📋 Payroll Status-Based Editing & Regeneration Guide

**Date:** March 5, 2026  
**Version:** 2.0 - Status-Based Blocking  
**Status:** ✅ IMPLEMENTED

---

## 🎯 Overview

The payroll system now implements **status-based blocking** for employee configuration edits. This allows flexibility during the DRAFT phase while protecting finalized payroll from unintended modifications.

### Key Principle:
```
DRAFT Status    → ✅ CAN EDIT & REGENERATE
REVIEW Status   → ❌ CANNOT EDIT - Must backtrack to DRAFT
APPROVED Status → ❌ CANNOT EDIT - Locked
PAID Status     → ❌ CANNOT EDIT - Final, no changes allowed
```

---

## 📊 Payroll Life Cycle with Edit Rules

```
┌─────────────────────────────────────────────────────────────────────┐
│                      PAYROLL LIFE CYCLE                              │
└─────────────────────────────────────────────────────────────────────┘

1. CREATION PHASE
   ├─ Select Period (e.g., March 1-15)
   ├─ Select Department 
   ├─ Select Employment Type
   ├─ System computes payroll using current employee config
   └─ Payroll entries created with Status: DRAFT
                    ↓

2. DRAFT STATUS ✅ FULLY EDITABLE
   ├─ Earnings edits: ✅ ALLOWED
   ├─ Deductions edits: ✅ ALLOWED
   ├─ Gov shares edits: ✅ ALLOWED
   ├─ Payroll entry edits: ✅ ALLOWED
   ├─ Delete payroll: ✅ ALLOWED
   ├─ Regenerate & save: ✅ ALLOWED (overwrites previous values)
   └─ User can modify amounts and recompute as needed
                    ↓

3. SUBMIT → REVIEW STATUS ❌ LOCKED FOR EDITS
   ├─ User clicks "Submit" button on payroll records
   ├─ Workflow transitions: DRAFT → REVIEW
   ├─ Earnings edits: ❌ BLOCKED
   ├─ Deductions edits: ❌ BLOCKED
   ├─ Gov shares edits: ❌ BLOCKED
   ├─ Payroll entry edits: ❌ BLOCKED
   ├─ User gets error: "Payroll is in REVIEW status. Please backtrack 
   │  to DRAFT status first to make changes to earnings."
   └─ Next action: User must RETURN to DRAFT first
                    ↓

4. RETURN → DRAFT (Workflow Reversal)
   ├─ User clicks "Return" button on payroll records
   ├─ Provides reason (e.g., "Rate correction needed")
   ├─ Workflow transitions: REVIEW → DRAFT
   ├─ Payroll is now editable again ✅
   ├─ Employee config is now editable again ✅
   └─ Back to Step 2: Can regenerate, modify, re-submit
                    ↓

5. APPROVE STATUS ❌ LOCKED FOR REVIEW
   ├─ Manager clicks "Approve" button
   ├─ Workflow transitions: REVIEW → APPROVED
   ├─ Earnings edits: ❌ BLOCKED
   ├─ Deductions edits: ❌ BLOCKED
   ├─ Gov shares edits: ❌ BLOCKED
   ├─ Locked for payment processing
   └─ User cannot make changes
                    ↓

6. MARK PAID → PAID STATUS ❌ FINAL
   ├─ Finance clicks "Mark as Paid" button
   ├─ Workflow transitions: APPROVED → PAID
   ├─ Earnings edits: ❌ BLOCKED
   ├─ Deductions edits: ❌ BLOCKED
   ├─ Gov shares edits: ❌ BLOCKED
   ├─ Final state - no further transitions allowed
   └─ Payroll is archived/locked permanently

```

---

## 🔄 Regenerate & Resave While in DRAFT

### Scenario: Rates Changed, Need to Recalculate

**Situation:**
- Payroll saved on March 5 with old rates
- March 6: New rates effective from March 1
- Need to recalculate payroll using new rates

**Steps:**

1. **Edit Employee Earnings/Deductions**
   ```
   Status: DRAFT → Edits ALLOWED ✅
   
   - Go to Earnings configuration
   - Update rates to new values
   - Save earnings
   - No error shown → Edit succeeded
   ```

2. **Regenerate Payroll**
   ```
   - Go to Payroll Records
   - Select same Period, Department, Employment Type
   - Click "Generate Payroll"
   - System computes with NEW rates
   - Click "Save Payroll"
   - Payroll entry updated with new amounts
   - Previous values overwritten
   ```

3. **Verify & Submit**
   ```
   - Review payroll amounts
   - Verify calculations are correct
   - Status still DRAFT
   - When ready: Click "Submit for Review"
   - Transitions to REVIEW → Edits now blocked
   ```

**Result:** ✅ Payroll successfully regenerated with new values

---

## 🚫 Error Messages & Their Meanings

### Error 1: DRAFT Payroll Exists - Cannot Edit Config
**Message:** `"Payroll is in DRAFT status. Please backtrack to DRAFT status first to make changes to earnings."`

Wait, that doesn't make sense. If payroll is DRAFT, why block? **This error should NOT appear for DRAFT status.** If you see this, there's a bug.

### Error 2: REVIEW Status - Backtrack Required (COMMON)
**Message:** `"Payroll is in REVIEW status. Please backtrack to DRAFT status first to make changes to earnings."`

**What it means:**
- Payroll was submitted for review
- User cannot edit earnings while payroll is under review
- User must return payroll to DRAFT first

**How to fix:**
1. Go to Payroll Records
2. Click "Return" button on the payroll entry
3. Provide reason for return
4. Payroll returns to DRAFT status
5. Now you can edit earnings again ✅

### Error 3: APPROVED Status - Cannot Edit
**Message:** `"Payroll is in APPROVED status. Please backtrack to DRAFT status first to make changes to earnings."`

**What it means:**
- Payroll has been approved and locked
- Cannot edit earnings after approval
- Must return to DRAFT (requires manager override)

**Note:** Returning from APPROVED is more restrictive - may require manager approval

### Error 4: PAID Status - No Changes Allowed
**Message:** `"Payroll is in PAID status. Please backtrack to DRAFT status first to make changes to earnings."`

**What it means:**
- Payment has been processed
- Payroll is in final state
- No changes allowed
- Contact Finance/HR if corrections needed

---

## 🛠️ Implementation Details

### Backend Blocking Logic

**File:** `payroll/earnings_handler.php`, `payroll/deductions_handler.php`, `payroll/govshare_handler.php`

**Logic:**
```php
// Get the most recent payroll entry for this employee
$last_locked_period = $Payroll->GetLastLockedPayrollPeriodByEmployee($employee_id);
$payroll_status = $last_locked_period['status'] ?? null;

// BLOCK if payroll exists and status is NOT DRAFT
if($last_locked_period && $payroll_status !== 'DRAFT'){
    return error with status and message
}

// If we reach here, editing is ALLOWED
```

### What Changed:

| Component | Old Logic | New Logic |
|-----------|-----------|-----------|
| **Blocking trigger** | Semi-monthly 1st half period | Payroll workflow status |
| **Block condition** | `IsSecondHalfOfMonth()` | `status !== 'DRAFT'` |
| **Status checked** | Period dates | Payroll entry status field |
| **Monthly payroll** | Could still block | Always allows (no status check) |
| **Error message** | Generic "locked period" | Dynamic with status & instructions |

### Status Query:
```sql
SELECT a.*, b.payroll_entry_id, b.status 
FROM payroll_periods a
INNER JOIN payroll_entries b
ON a.payroll_period_id = b.payroll_period_id
WHERE b.locked_period = 1 
AND b.employee_id = $employee_id
ORDER BY a.payroll_period_id DESC LIMIT 1
```

---

## ✅ Test Scenarios

### Test 1: Edit While DRAFT (Should ALLOW)
```
Setup:
- Create payroll period: March 1-15
- Save payroll: Status = DRAFT
- Edit employee earnings

Expected: Edit succeeds ✅
Result: Earnings amount updated in database
```

### Test 2: Edit While REVIEW (Should BLOCK)
```
Setup:
- Payroll status: DRAFT
- Submit payroll for review
- Payroll status: REVIEW
- Try to edit employee earnings

Expected: ❌ Error shown
Message: "Payroll is in REVIEW status. Please backtrack to DRAFT..."
Action required: User returns to DRAFT first
```

### Test 3: Regenerate & Resave (Should ALLOW)
```
Setup:
- Payroll created with Rate A
- Payroll status: DRAFT
- Rates changed to Rate B
- Edit earnings to new Rate B
- Regenerate payroll

Expected: ✅ Payroll regenerated with Rate B
Result: Payroll amounts recalculated with new rates
Allow: User can save this version
```

### Test 4: Edit Deductions While REVIEW (Should BLOCK)
```
Setup:
- Payroll status: REVIEW
- Try to edit employee deductions

Expected: ❌ Error shown
Same blocking as earnings
```

### Test 5: Edit Gov Shares While APPROVED (Should BLOCK)
```
Setup:
- Payroll status: APPROVED
- Try to edit employee gov shares

Expected: ❌ Error shown
Message: "Payroll is in APPROVED status..."
```

---

## 🔑 Key Points for Users

### ✅ DO:
- ✅ Make config changes while payroll is DRAFT
- ✅ Regenerate payroll multiple times before submitting
- ✅ Return payroll to DRAFT to make corrections
- ✅ Submit once rates are final and payroll correct
- ✅ Review thoroughly before approving

### ❌ DON'T:
- ❌ Expect to edit earnings after clicking "Submit"
- ❌ Try to change rates after approval
- ❌ Skip the "Return to DRAFT" step when corrections needed
- ❌ Edit config during review/approval/payment phases

---

## 🎓 Workflow Decision Tree

```
Want to edit earnings?
    ↓
Is payroll status DRAFT?
    ├─ YES ✅ → Go ahead, edit earnings
    │            Regenerate payroll if needed
    │            Re-submit when ready
    │
    └─ NO ❌ → Cannot edit
               Is status REVIEW?
                   ├─ YES → Click "Return" button
                   │        Provide reason
                   │        Status → DRAFT
                   │        Now can edit ✅
                   │
                   ├─ APPROVED → Cannot return (locked)
                   │              Contact manager/finance
                   │
                   └─ PAID → Cannot change (final)
                            Contact HR/Finance
```

---

## 🔔 Important Reminders

### Before Submitting Payroll:
1. Verify all rates are correct
2. Regenerate payroll to confirm amounts
3. Review gross pay, deductions, net pay
4. Confirm no errors in calculations
5. THEN submit for review

### After Submitting (REVIEW Status):
- ❌ Cannot edit earnings
- ❌ Cannot edit deductions
- ❌ Cannot edit gov shares
- ✅ CAN return to DRAFT if corrections needed

### After Approval (APPROVED Status):
- ❌ No edits allowed
- ⚠️ Only admin/finance can return to DRAFT
- ✅ Can proceed to "Mark as Paid"

### After Payment (PAID Status):
- ❌ Final state, no changes
- ℹ️ Contact Finance if payment error occurred

---

## 📋 Summary Table

| Phase | Payroll Edits | Config Edits | Can Regenerate | Next Action |
|--------|---|---|---|---|
| **DRAFT** | ✅ YES | ✅ YES | ✅ YES | Submit for Review |
| **REVIEW** | ❌ NO | ❌ NO | ❌ NO | Approve or Return to DRAFT |
| **APPROVED** | ❌ NO | ❌ NO | ❌ NO | Mark as Paid or Return to DRAFT |
| **PAID** | ❌ NO | ❌ NO | ❌ NO | Archive (Final) |

---

## 🚀 Technical Flow

```
User Action: Edit Employee Earnings
                    ↓
JavaScript submits: 
  POST earnings_handler.php
  action: edit_employee_earnings
  employee_id: X
                    ↓
Backend php checks:
  $payroll = GetLastLockedPayrollPeriodByEmployee(X)
  $status = $payroll['status']
                    ↓
Decision:
  if $status === 'DRAFT' → ALLOW ✅
  if $status === 'REVIEW' → BLOCK ❌
  if $status === 'APPROVED' → BLOCK ❌
  if $status === 'PAID' → BLOCK ❌
                    ↓
Response:
  {"result":"block_edit", "status":"REVIEW", 
   "message":"Payroll is in REVIEW status..."}
                    ↓
Frontend handles:
  else if(result == 'block_edit')
    Show SweetAlert with message
    Reload page
```

