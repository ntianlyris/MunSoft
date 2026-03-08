<?php
/**
 * gaa_api.php  —  GAA Module REST Endpoint (Project-Aligned)
 * ─────────────────────────────────────────────────────────────
 *  Location : Munsoft/includes/gaa_netpay_module/api/
 *
 *  CHANGE FROM ORIGINAL:
 *   • Session guard uses $_SESSION['uid'] (Munsoft's session key)
 *     instead of the generic $_SESSION['user_id'].
 *
 *  NOTE: The payroll UI does NOT call this endpoint directly.
 *  The payroll page uses payroll/gaa_payroll_handler.php which
 *  adds payroll-period context before dispatching here.
 *  This endpoint is available for other modules that need direct
 *  access to the GAA intelligence layer.
 * ─────────────────────────────────────────────────────────────
 *
 *  Actions accepted (POST JSON or GET params):
 *    classify          | net_pay
 *    validate_entry    | employee_id, proposed_total_deductions
 *    validate_payroll  | employee_id, gross, total_deductions
 *    validate_batch    | payroll_period_id [, dept_id]
 *    analyze_employee  | employee_id, gross, total_deductions
 *    analyze_batch     | payroll_period_id [, dept_id]
 *    risk_score        | employee_id, gross, total_deductions
 *    headroom          | employee_id, gross, current_deductions
 *    predict           | employee_id
 */

require_once __DIR__ . '/../GAAModule.php';

// ── CORS ──────────────────────────────────────────────────────
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Session guard (uses Munsoft's 'uid' key) ──────────────────
if (GAAConfig::API_REQUIRE_SESSION) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Munsoft uses $_SESSION['uid'] for the authenticated user
    if (empty($_SESSION['uid'])) {
        GAAResponseBuilder::error('Unauthorized. Please log in.', 401);
    }
}

// ── Parse request: JSON body > POST > GET ─────────────────────
$request = [];
$raw = file_get_contents('php://input');
if (!empty($raw)) {
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $request = $decoded;
    }
}
if (empty($request)) {
    $request = !empty($_POST) ? $_POST : $_GET;
}
if (empty($request)) {
    GAAResponseBuilder::error('Empty request. Provide JSON body or query parameters.', 400);
}

// ── Optional request logging ──────────────────────────────────
if (GAAConfig::API_LOG_REQUESTS) {
    $logger = new GAALogger();
    $logger->logApiRequest(
        $request['action'] ?? 'unknown',
        $_SESSION['uid'] ?? null,
        $request
    );
}

// ── Dispatch ──────────────────────────────────────────────────
GAAModule::getInstance()->dispatch($request);
?>
