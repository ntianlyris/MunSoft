<?php
/**
 * AccessControl Class
 * 
 * This class provides a centralized management layer for Role-Based Access Control (RBAC).
 * It abstracts the complex permission mapping logic from the view files and provides
 * a clean, readable API for checking user capabilities throughout the application.
 */

include_once 'PrivilegedUser.php';

class AccessControl {
    
    /** @var PrivilegedUser The underlying user object with role data */
    private $user;

    /** @var int The unique ID of the currently logged-in user */
    public $userId;

    /** @var string The primary display name of the user's role (defaults to Guest) */
    public $roleName = 'Guest';

    /** 
     * PERMISSION FLAGS
     * These boolean properties allow for easy 'if ($Access->manage_hr)' style checks.
     * They are populated during instantiation based on the user's assigned permissions.
     */
    public $manage_system   = false;
    public $update_data     = false;
    public $manage_hr       = false;
    public $manage_payroll  = false;
    public $access_hr       = false;
    public $access_payroll  = false;
    public $can_read        = false;

    /**
     * Constructor
     * 
     * @param PrivilegedUser $user An instance of Admin or PrivilegedUser
     */
    public function __construct(PrivilegedUser $user) {
        $this->user = $user;
        $this->userId = $this->user->getSessionUID();
        
        // Return early if no user is authenticated
        if (!$this->userId) {
            return;
        }

        // Initialize user roles and permissions from database
        $roles = $this->user->initRoles($this->userId);
        
        // Set primary role name (takes the first key if available)
        if (!empty($roles)) {
            reset($roles);
            $this->roleName = (string)key($roles);
        }

        /**
         * INITIALIZE CORE PERMISSION FLAGS
         * 
         * Note: The hasPrivilege() method in PrivilegedUser already handles 
         * the 'Manage System' Master Bypass logic internally.
         */
        $this->manage_system  = $this->user->hasPrivilege('Manage System');
        $this->update_data     = $this->user->hasPrivilege('Update Data');
        $this->manage_hr       = $this->user->hasPrivilege('Manage HR');
        $this->manage_payroll  = $this->user->hasPrivilege('Manage Payroll');
        $this->access_hr       = $this->user->hasPrivilege('Access HR');
        $this->access_payroll  = $this->user->hasPrivilege('Access Payroll');
        $this->can_read        = $this->user->hasPrivilege('Read');
    }

    /**
     * Check if the user has a specific permission.
     * 
     * Use this for granular checks that aren't mapped to the primary properties.
     * Example: if ($Access->can('Export Reports')) { ... }
     * 
     * @param string $permission The name of the permission string to check
     * @return bool
     */
    public function can($permission) {
        return $this->user->hasPrivilege($permission);
    }

    /**
     * Check if the user has a specific role.
     * 
     * @param string $role The role name (e.g., 'Employee', 'Administrator')
     * @return bool
     */
    public function hasRole($role) {
        return $this->user->hasRole($role);
    }

    /**
     * Helper to export permissions to Global scope if needed for legacy code support.
     */
    public function syncToGlobals() {
        $GLOBALS['user_id']        = $this->userId;
        $GLOBALS['role']           = $this->roleName;
        $GLOBALS['manage_system']  = $this->manage_system;
        $GLOBALS['update_data']    = $this->update_data;
        $GLOBALS['manage_hr']      = $this->manage_hr;
        $GLOBALS['manage_payroll'] = $this->manage_payroll;
        $GLOBALS['access_hr']      = $this->access_hr;
        $GLOBALS['access_payroll'] = $this->access_payroll;
        $GLOBALS['user_role_for_js'] = $this->roleName;
        $GLOBALS['can_read'] = $this->can_read;
    }
}
