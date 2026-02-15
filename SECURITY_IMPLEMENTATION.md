# Role-Based Access Control Security Implementation

## Overview
This document outlines the security measures implemented to restrict edit and delete actions for Employee users across the MunSoft application.

## Implementation Details

### 1. Frontend Security (UI-Level Protection)

#### CSS-Based Hidden Elements
- **Location**: `/includes/layout/head.php`
- **Method**: CSS attribute selectors with `!important` flags
- **Approach**: Uses data attribute on body tag to hide action elements dynamically
```css
body[data-user-role="Employee"] .action-buttons-group {
  display: none !important;
}
body[data-user-role="Employee"] .action-column {
  display: none !important;
}
```

#### JavaScript Fallback Hiding
- **Location**: `/includes/layout/head.php` and `/includes/view/view.php`
- **Method**: JavaScript functions that execute on DOM ready
- **Approach**: Multiple fallback mechanisms to ensure buttons are hidden
  - Element class-based hiding
  - Inline style assignment
  - Direct element visibility toggling

#### User Role Detection
- **Location**: `/includes/view/view.php`
- **Method**: Outputs user role as JavaScript variable and HTML data attribute
- **Variable**: `window.currentUserRole` (global JavaScript variable)
- **Data Attribute**: `data-user-role` on body tag

### 2. Backend Security (Server-Level Protection)

#### Security Check Function
- **Location**: Added to all `save_settings.php` files
  - `/hris/save_settings.php`
  - `/admin/save_settings.php`
  - `/payroll/save_settings.php`

#### Function: `CheckModifyPermission()`
```php
function CheckModifyPermission($action = 'modify') {
    global $current_user_role;
    
    // Employee users cannot modify/delete data
    if ($current_user_role === 'Employee') {
        http_response_code(403);
        return false;
    }
    return true;
}
```

#### Role Verification
- Checks user role from `Admin::initRoles()` method
- Compares against 'Employee' role
- Denies access with HTTP 403 status code if user is Employee

### 3. Protected Operations

All delete and modify operations check permissions before execution:

#### Hris Module
- `delete_dept` - Delete department
- `delete_position` - Delete position
- `delete_user` - Delete system user
- `SaveDepartment` - Create/update department
- `SavePositionItem` - Create/update position

#### Admin Module
- Same delete and save operations

#### Payroll Module
- Same delete and save operations

### 4. HTML Markup Changes

#### Action Button Groups
All action button divs contain the class `action-buttons-group`:
```html
<div class="btn-group action-buttons-group">
  <button...>Edit</button>
  <button...>Delete</button>
</div>
```

This allows CSS selectors to target and hide all action buttons for Employee users.

### 5. Security Measures Applied

| Component | Security Level | Method |
|-----------|----------------|--------|
| UI Buttons | Frontend | CSS `display: none` with data attribute |
| Action Columns | Frontend | CSS selector and JavaScript hiding |
| Delete Operations | Backend | Permission check via `CheckModifyPermission()` |
| Modify Operations | Backend | Permission check via `CheckModifyPermission()` |
| HTTP Response | Backend | 403 Forbidden status code |
| API Response | Backend | JSON error message with "Access Denied" |

## User Roles Affected

### Employee Role
- ✅ **Cannot** perform edit operations
- ✅ **Cannot** perform delete operations
- ✅ Edit/Delete buttons are hidden in UI
- ✅ Backend prevents operations even if buttons are bypassed

### Other Roles (Administrator, HR, Manager, etc.)
- ✅ Can perform all operations
- ✅ Action buttons remain visible
- ✅ Can modify and delete data

## Error Responses

### Frontend (JavaScript)
- Buttons are hidden with CSS and JavaScript
- No modal dialogs appear for editing/deleting

### Backend (API Response)
- HTTP Status: 403 Forbidden
- JSON Response: `{"result":"error", "message":"Access Denied: You do not have permission to modify data."}`

## Testing Recommendations

1. **Test as Employee User**
   - Verify edit/delete buttons are not visible
   - Attempt to directly call delete API endpoints
   - Verify 403 Forbidden response is received

2. **Test as Administrator User**
   - Verify edit/delete buttons are visible
   - Verify operations complete successfully
   - Verify data is modified/deleted correctly

3. **Security Audit**
   - Check browser console for errors
   - Monitor network requests for unauthorized attempts
   - Verify HTTP response codes are correct

## Files Modified

### Frontend Files
- `includes/layout/head.php` - Added CSS and JavaScript security layer
- `includes/view/view.php` - Added user role exposure and action-buttons-group class

### Backend Files
- `hris/save_settings.php` - Added permission checks
- `admin/save_settings.php` - Added permission checks
- `payroll/save_settings.php` - Added permission checks

### HTML Markup
- All view functions in `includes/view/view.php` updated to include `action-buttons-group` class

## Maintenance Notes

- If new edit/delete operations are added, ensure:
  1. Action button div contains `class="btn-group action-buttons-group"`
  2. Backend save_settings.php calls `CheckModifyPermission()` before operation
  3. User role is properly initialized at the top of save_settings.php

- If new user roles are introduced, update:
  1. `CheckModifyPermission()` function to include new role checks if needed
  2. View functions to properly set user role for frontend

## Future Enhancements

1. Implement role-based permission matrix
2. Add audit logging for attempted unauthorized operations
3. Implement per-operation permission checks (vs. role-based)
4. Add user activity monitoring and suspicious activity alerts
