<?php

/**
 * GAA Validator Handler
 * 
 * AJAX endpoint for GAA net pay threshold validation
 * Bridges frontend requests to the GAANetPayValidator class
 * 
 * Routes:
 * - action=validate_deduction_entry  → Real-time deduction validation
 * - action=validate_computation      → Payroll save-time validation
 * - action=validate_batch_approval   → Batch period approval validation
 * - action=get_audit_log             → Retrieve compliance audit trail
 * 
 * @author Development Team
 * @version 1.0.0
 * @since 2026-03-06
 */

// ────────────────────────────────────────────────────────────
// BOOTSTRAP & CONFIGURATION
// ────────────────────────────────────────────────────────────

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define relative paths
define('BASE_PATH', realpath(__DIR__ . '/..'));

// ────────────────────────────────────────────────────────────
// AUTHENTICATION GUARD
// ────────────────────────────────────────────────────────────

// Check if user is authenticated
if (empty($_SESSION['user_id']) && empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Unauthorized - User not authenticated',
        'error_code' => 'AUTH_REQUIRED'
    ]);
    exit;
}

// Get current user ID (support both student/employee and admin sessions)
$currentUserId = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? 0;

// ────────────────────────────────────────────────────────────
// LOAD DEPENDENCIES
// ────────────────────────────────────────────────────────────

try {
    // Load database connection
    require_once(BASE_PATH . '/class/DB_conn.php');
    
    // Load validator class
    require_once(BASE_PATH . '/class/GAANetPayValidator.php');
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Failed to load required dependencies',
        'error_code' => 'DEPENDENCY_ERROR'
    ]);
    exit;
}

// ────────────────────────────────────────────────────────────
// REQUEST ROUTING
// ────────────────────────────────────────────────────────────

try {
    // Get action from request
    $action = trim($_POST['action'] ?? $_GET['action'] ?? '');
    
    if (empty($action)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Missing required parameter: action',
            'error_code' => 'MISSING_ACTION'
        ]);
        exit;
    }
    
    // Initialize database connection
    $db = new DB_conn();
    
    // Route to handler function
    switch ($action) {
        case 'validate_deduction_entry':
            handleValidateDeductionEntry($db, $currentUserId);
            break;
            
        case 'validate_computation':
            handleValidateComputation($db, $currentUserId);
            break;
            
        case 'validate_batch_approval':
            handleValidateBatchApproval($db, $currentUserId);
            break;
            
        case 'get_audit_log':
            handleGetAuditLog($db, $currentUserId);
            break;
            
        default:
            http_response_code(400);
            echo json_encode([
                'status' => 'ERROR',
                'message' => 'Unknown action: ' . htmlspecialchars($action),
                'error_code' => 'UNKNOWN_ACTION'
            ]);
            break;
    }
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Server error - ' . $e->getMessage(),
        'error_code' => 'SERVER_ERROR'
    ]);
    error_log('[GAA Handler Error] ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
}

exit;

// ────────────────────────────────────────────────────────────
// HANDLER FUNCTIONS
// ────────────────────────────────────────────────────────────

/**
 * Handler: validate_deduction_entry
 * 
 * Called via AJAX when HR enters a deduction amount
 * Validates impact WITHOUT saving to database (preview mode)
 * 
 * POST Parameters:
 * - payroll_entry_id (required): Payroll record ID
 * - proposed_amount (required):  New deduction amount being entered
 * - deduction_type (optional):   'mandatory' or 'authorized' (default: 'authorized')
 * 
 * Response: Validation result with can_proceed flag
 */
function handleValidateDeductionEntry($db, $userId) {
    // Extract and validate parameters
    $payrollEntryId = (int) ($_POST['payroll_entry_id'] ?? 0);
    $proposedAmount = (float) ($_POST['proposed_amount'] ?? 0);
    $deductionType = trim($_POST['deduction_type'] ?? 'authorized');
    
    // Parameter validation
    if ($payrollEntryId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Invalid payroll_entry_id',
            'error_code' => 'INVALID_ENTRY_ID'
        ]);
        return;
    }
    
    if ($proposedAmount < 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Deduction amount must be non-negative',
            'error_code' => 'INVALID_AMOUNT'
        ]);
        return;
    }
    
    // Instantiate validator
    $validator = new GAANetPayValidator($db->conn ?? $db, $userId);
    
    // Validate deduction entry
    $result = $validator->validateDeductionEntry($payrollEntryId, $proposedAmount, $deductionType);
    
    // Set HTTP status code based on result
    if ($result['status'] === 'BLOCKED') {
        http_response_code(422); // Unprocessable Entity
    }
    
    echo json_encode($result);
}

/**
 * Handler: validate_computation
 * 
 * Called when payroll computation is saved
 * Validates the full net pay calculation against GAA threshold
 * 
 * POST Parameters:
 * - payroll_entry_id (required):      Payroll record ID
 * - employee_id (required):           Employee ID
 * - gross_earnings (required):        Total gross earnings
 * - mandatory_deductions (required):  GSIS, PhilHealth, Pag-IBIG, Tax
 * - authorized_deductions (required): Loans, consignations, etc.
 * 
 * Response: Validation result - if blocked, frontend should not save
 */
function handleValidateComputation($db, $userId) {
    // Extract and validate parameters
    $payrollEntryId = (int) ($_POST['payroll_entry_id'] ?? 0);
    $employeeId = (int) ($_POST['employee_id'] ?? 0);
    $grossEarnings = (float) ($_POST['gross_earnings'] ?? 0);
    $mandatoryDeductions = (float) ($_POST['mandatory_deductions'] ?? 0);
    $authorizedDeductions = (float) ($_POST['authorized_deductions'] ?? 0);
    
    // Parameter validation
    if ($payrollEntryId <= 0 || $employeeId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Missing required parameters: payroll_entry_id or employee_id',
            'error_code' => 'MISSING_PARAMS'
        ]);
        return;
    }
    
    // Instantiate validator
    $validator = new GAANetPayValidator($db->conn ?? $db, $userId);
    
    // Compute net pay
    $netPay = $validator->computeNetPay(
        $grossEarnings,
        $mandatoryDeductions,
        $authorizedDeductions
    );
    
    // Validate against threshold
    $result = $validator->validateThreshold(
        $netPay,
        $employeeId,
        $payrollEntryId,
        GAANetPayValidator::POINT_PAYROLL_SAVE
    );
    
    // Attach computed net pay to response
    $result['computed_net_pay'] = $netPay;
    
    // Set HTTP status code based on result
    if ($result['status'] === 'BLOCKED') {
        http_response_code(422); // Unprocessable Entity - prevent save
    }
    
    echo json_encode($result);
}

/**
 * Handler: validate_batch_approval
 * 
 * Called before approving entire payroll period
 * Validates ALL employee records in the period
 * Blocks if ANY record falls below threshold
 * 
 * POST Parameters:
 * - payroll_period_id (required): Period ID to validate all records for
 * 
 * Response: can_approve flag + list of violating employees (if any)
 */
function handleValidateBatchApproval($db, $userId) {
    // Extract and validate parameters
    $payrollPeriodId = (int) ($_POST['payroll_period_id'] ?? 0);
    
    if ($payrollPeriodId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Invalid payroll_period_id',
            'error_code' => 'INVALID_PERIOD_ID'
        ]);
        return;
    }
    
    // Instantiate validator
    $validator = new GAANetPayValidator($db->conn ?? $db, $userId);
    
    // Perform batch validation
    $result = $validator->validatePayrollBatch($payrollPeriodId);
    
    // Set HTTP status code based on result
    if (!$result['can_approve']) {
        http_response_code(422); // Unprocessable Entity - block approval
    } else {
        http_response_code(200); // OK - proceed with approval
    }
    
    echo json_encode($result);
}

/**
 * Handler: get_audit_log
 * 
 * Retrieves paginated GAA compliance audit log
 * Used for compliance reporting and COA audits
 * 
 * GET Parameters:
 * - payroll_period_id (optional): Filter by period
 * - employee_id (optional):       Filter by employee
 * - action (optional):            Filter by action type
 * - page (optional):              Page number (default: 1)
 * - limit (optional):             Records per page (default: 50, max: 100)
 * 
 * Response: Array of audit log entries with pagination info
 */
function handleGetAuditLog($db, $userId) {
    // Extract and validate pagination parameters
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $limit = min(100, max(1, (int) ($_GET['limit'] ?? 50)));
    $offset = ($page - 1) * $limit;
    
    // Extract optional filter parameters
    $payrollPeriodId = (int) ($_GET['payroll_period_id'] ?? 0);
    $employeeId = (int) ($_GET['employee_id'] ?? 0);
    $action = trim($_GET['action'] ?? '');
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $paramTypes = '';
    
    if ($payrollPeriodId > 0) {
        $whereConditions[] = 'pr.payroll_period_id = ?';
        $params[] = $payrollPeriodId;
        $paramTypes .= 'i';
    }
    
    if ($employeeId > 0) {
        $whereConditions[] = 'l.employee_id = ?';
        $params[] = $employeeId;
        $paramTypes .= 'i';
    }
    
    if (!empty($action)) {
        $whereConditions[] = 'l.action = ?';
        $params[] = $action;
        $paramTypes .= 's';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Prepare query
    $sql = "SELECT 
                l.log_id,
                l.payroll_entry_id,
                l.employee_id,
                l.action,
                l.computed_net_pay,
                l.threshold_amount,
                l.shortfall_amount,
                l.validation_point,
                l.performed_at,
                l.remarks,
                et.full_name as employee_name,
                pr.payroll_period_id,
                au.username as performed_by_username
            FROM gaa_audit_log l
            LEFT JOIN employees_tbl et ON et.employee_id = l.employee_id
            LEFT JOIN payroll_entries pr ON pr.payroll_entry_id = l.payroll_entry_id
            LEFT JOIN admin_users au ON au.admin_id = l.performed_by
            {$whereClause}
            ORDER BY l.performed_at DESC
            LIMIT ? OFFSET ?";
    
    // Add limit and offset parameters
    $params[] = $limit;
    $params[] = $offset;
    $paramTypes .= 'ii';
    
    // Execute query
    $stmt = $db->query($sql) or die($db->error);
    $logs = [];
    
    // For simplicity, using non-prepared query since DB_conn doesn't fully support prepared statements in all contexts
    // This is a limitation we'll work with
    $sqlSafe = $sql;
    $sqlSafe .= ';'; // Add semicolon for safety
    
    // Build safe WHERE clause without parameters (simplified approach)
    $whereClauseSafe = '';
    if ($payrollPeriodId > 0) {
        $whereClauseSafe .= "AND pr.payroll_period_id = {$payrollPeriodId} ";
    }
    if ($employeeId > 0) {
        $whereClauseSafe .= "AND l.employee_id = {$employeeId} ";
    }
    if (!empty($action)) {
        $whereClauseSafe .= "AND l.action = '" . $db->escape_string($action) . "' ";
    }
    
    // Simplified query
    $sqlSimple = "SELECT 
                l.log_id,
                l.payroll_entry_id,
                l.employee_id,
                l.action,
                l.computed_net_pay,
                l.threshold_amount,
                l.shortfall_amount,
                l.validation_point,
                l.performed_at,
                l.remarks,
                et.full_name as employee_name,
                pr.payroll_period_id
            FROM gaa_audit_log l
            LEFT JOIN employees_tbl et ON et.employee_id = l.employee_id
            LEFT JOIN payroll_entries pr ON pr.payroll_entry_id = l.payroll_entry_id
            WHERE 1=1 {$whereClauseSafe}
            ORDER BY l.performed_at DESC
            LIMIT {$limit} OFFSET {$offset}";
    
    $result = $db->query($sqlSimple);
    
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetch_array($result)) {
            $logs[] = $row;
        }
    }
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM gaa_audit_log l
                LEFT JOIN employees_tbl et ON et.employee_id = l.employee_id
                LEFT JOIN payroll_entries pr ON pr.payroll_entry_id = l.payroll_entry_id
                WHERE 1=1 {$whereClauseSafe}";
    
    $countResult = $db->query($countSql);
    $countRow = $db->fetch_array($countResult);
    $totalRecords = $countRow['total'] ?? 0;
    $totalPages = ceil($totalRecords / $limit);
    
    // Return response
    echo json_encode([
        'status' => 'OK',
        'logs' => $logs,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages,
            'offset' => $offset
        ]
    ]);
}

?>
