# RBAC Security Audit & Assessment - MunSoft

**Date:** March 12, 2026  
**Auditor:** Antigravity AI  
**Subject:** Role-Based Access Control (RBAC) Integrity and Enforcement  

---

## **Final Security Rating: 68%**
### **Status: Solid Foundation / Inconsistent Enforcement**

The MunSoft security architecture is built on a professional-grade centralized logic layer, but suffers from inconsistent application across the presentation layer (UI) and individual file routes.

---

## **1. Strengths (The Pros)**

*   **Centralized RBAC Engine:** The system uses a dedicated `AccessControl` class and `PrivilegedUser` model. This ensures that logic for "what a user can do" is defined in one place, not scattered across 100 files.
*   **Secured Data Mutation (Write-Protection):** The primary handler for data changes (`admin/save_settings.php`) is strictly gated. It uses `CheckModifyPermission()` to verify privileges before any `INSERT`, `UPDATE`, or `DELETE` SQL is executed.
*   **Encrypted Identity Management:** User passwords utilize industry-standard `password_hash()` (Bcrypt) and `password_verify()`, preventing plain-text exposure in the event of a database leak.
*   **Global Synchronization:** Permission flags are synced to the `$GLOBALS` array in `view.php`, allowing for efficient permission checks throughout the rendering lifecycle without redundant database calls.

---

## **2. Weaknesses (The Cons)**

*   **Unsecured Read-Only Routes:** Many viewing pages (e.g., User Management, Employee Lists) rely on the Sidebar to hide links. However, the files themselves lack "Top-of-File" guards. A standard user who knows the URL (e.g., `/admin/user_management.php`) can bypass the sidebar and view sensitive information.
*   **Security through Obscurity:** The system leans heavily on "hiding" UI elements rather than "blocking" access.
*   **Client-Side "Security" Bypasses:** Some UI restrictions (like hiding "Delete" buttons) are enforced via CSS and JavaScript in `head.php`. A user with basic knowledge of browser "Inspect Element" tools can reveal these hidden elements (though the backend will still block the final action).
*   **Granularity Gaps:** While the backend can check for "Update Data," the UI often checks for binary roles (e.g., "is_employee") rather than granular capabilities, which leads to over-privileged access in some modules.

---

## **3. Recommended Roadmap for 95%+ Security**

### **Phase 1: Hardened Routing (Immediate)**
*   Implement mandatory server-side guards at the top of every PHP file in `/admin`, `/hris`, and `/payroll`.
*   *Template:* `if (!$manage_system) { header("Location: ../index.php?access=denied"); exit; }`

### **Phase 2: API & Handler Lockdown**
*   Audit all AJAX handlers (e.g., `gaa_netpay_handler.php`) to ensure they validate `$_SESSION['uid']` against specific granular permissions, not just "is logged in."

### **Phase 3: Data Masking**
*   Implement PII (Personally Identifiable Information) masking. For users without "Full View" or "Update Data" permissions, sensitive fields like mobile numbers, bank details, or home addresses should be partially obscured (e.g., `091*****12`).

---
> [!IMPORTANT]
> This document serves as a baseline for future security enhancements. All new modules must reference the `AccessControl` class and implement server-side file guards by default.
