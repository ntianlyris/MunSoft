<?php
/**
 * gaa_payroll_handler.php
 * ─────────────────────────────────────────────────────────────
 *  Location : Munsoft/payroll/
 *
 *  PURPOSE:
 *  The AJAX handler that the payroll UI communicates with.
 *  Sits between the payroll page JS and the GAA module, adding
 *  payroll-specific context (period info, frequency, department)
 *  that the raw module API doesn't carry.
 *
 *  WHY NOT CALL gaa_api.php DIRECTLY FROM JS?
 *  The module's raw API accepts employee_id + gross + deductions.
 *  The payroll UI works with computed arrays, period IDs, and
 *  department context. This handler bridges that gap, performs
 *  input normalization, and returns payroll-context-aware responses.
 *
 *  SESSION: Uses $_SESSION['uid'] (Munsoft standard)
 *
 *  ─── SUPPORTED ACTIONS ──────────────────────────────────────
 *
 *  classify_batch
 *    Fast: no DB calls. Classifies all employees in one shot.
 *    Use case: Inject status badges immediately after table renders.
 *    Input:  employees[] — [{employee_id, gross, total_deductions}]
 *    Output: results{employee_id: classification}, summary
 *
 *  analyze_employee
 *    Full AI profile for one employee (DB calls for history).
 *    Use case: On-demand detail card when user clicks a row.
 *    Input:  employee_id, gross, total_deductions, payroll_frequency
 *    Output: Full intelligence profile
 *
 *  pre_save_validate
 *    Hard validation gate before the Save Payroll AJAX fires.
 *    Blocks save if any employee is CRITICAL (net < ₱5,000).
 *    Warns if DANGER employees exist.
 *    Input:  employees[], payroll_frequency, period_label (optional)
 *    Output: {can_save, blocked, violations[], warnings[], summary}
 *
 *  headroom_check
 *    Live check: can a proposed new deduction be safely added?
 *    Use case: Bind to deduction input fields.
 *    Input:  employee_id, gross, current_deductions, proposed_deduction
 *    Output: headroom data + new status after proposed deduction
 *
 *  period_scan
 *    Batch AI analysis from the DB (uses stored payroll_entries).
 *    Use case: Period overview panel, batch approval screen.
 *    Input:  payroll_period_id, dept_id (optional), emp_type (optional)
 *    Output: Full batch intelligence report
 * ─────────────────────────────────────────────────────────────
 */

// ── Bootstrap ─────────────────────────────────────────────────
// Path: payroll/gaa_ai/ → ../../includes/gaa_netpay_module/GAAModule.php
require_once __DIR__ . '/../../includes/gaa_netpay_module/GAAModule.php';

// ── CORS headers ──────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('X-GAA-Handler: payroll');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Session guard ─────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['uid'])) {
    _respond(false, 'Unauthorized.', null, 401);
}

// ── Parse request ─────────────────────────────────────────────
$request = [];
$raw = file_get_contents('php://input');
if (!empty($raw)) {
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $request = $decoded ?? [];
    }
}
if (empty($request)) {
    $request = !empty($_POST) ? $_POST : $_GET;
}

$action = trim($request['action'] ?? '');
if (empty($action)) {
    _respond(false, 'Missing required parameter: action', null, 400);
}

// ── Route ─────────────────────────────────────────────────────
$gaa = GAAModule::getInstance();

switch ($action) {

    // ══════════════════════════════════════════════════════════
    //  CLASSIFY BATCH
    //  Fast tier classification for all employees in a computed
    //  payroll table. Zero DB calls — pure math.
    // ══════════════════════════════════════════════════════════
    case 'classify_batch':

        $employees = _requireArray($request, 'employees');
        if ($employees === null) {
            _respond(false, 'employees array is required.', null, 422);
        }

        $results  = [];
        $summary  = [
            'CRITICAL' => 0, 'DANGER' => 0, 'WARNING' => 0,
            'CAUTION'  => 0, 'STABLE' => 0, 'SAFE'    => 0,
        ];
        $has_critical  = false;
        $has_danger    = false;
        $critical_list = [];

        foreach ($employees as $emp) {
            $emp_id     = intval($emp['employee_id']  ?? 0);
            $gross      = floatval($emp['gross']      ?? 0);
            $deductions = floatval($emp['total_deductions'] ?? 0);

            // For semi-monthly: net_pay in payroll_entries is (gross-deductions)/2
            // but GAA validates against the FULL (gross - deductions).
            // gross and total_deductions sent from the UI are the FULL period values.
            $net_pay = $gross - $deductions;

            $cls = $gaa->classify($net_pay);

            $results[$emp_id] = [
                'employee_id'    => $emp_id,
                'gross'          => round($gross, 2),
                'total_deductions' => round($deductions, 2),
                'net_pay'        => round($net_pay, 2),
                'status'         => $cls['status'],
                'label'          => $cls['label'],
                'color'          => $cls['color'],
                'icon'           => $cls['icon'],
                'employee_name'  => $emp_name,
                'buffer_amount'  => $cls['buffer_amount'],
                'buffer_pct'     => $cls['buffer_pct'],
                'threshold'      => GAAConfig::MIN_NET_PAY,
                'description'    => $cls['description'],
            ];

            // Count per tier
            if (isset($summary[$cls['status']])) {
                $summary[$cls['status']]++;
            }

            if ($cls['status'] === 'CRITICAL') {
                $has_critical = true;
                $critical_list[] = [
                    'employee_id' => $emp_id,
                    'net_pay'     => round($net_pay, 2),
                    'shortfall'   => round(GAAConfig::MIN_NET_PAY - $net_pay, 2),
                ];
            }
            if ($cls['status'] === 'DANGER') {
                $has_danger = true;
            }
        }

        $total_at_risk = $summary['CRITICAL'] + $summary['DANGER'] + $summary['WARNING'];

        _respond(true, 'Classification complete.', [
            'results'       => $results,
            'summary'       => $summary,
            'total_at_risk' => $total_at_risk,
            'has_critical'  => $has_critical,
            'has_danger'    => $has_danger,
            'critical_list' => $critical_list,
            'total_employees' => count($employees),
        ]);
        break;


    // ══════════════════════════════════════════════════════════
    //  ANALYZE EMPLOYEE
    //  Full AI profile with DB history. Called on-demand when
    //  HR expands a row for detailed risk information.
    // ══════════════════════════════════════════════════════════
    case 'analyze_employee':

        $employee_id = _requireInt($request, 'employee_id');
        $gross       = _requireFloat($request, 'gross');
        $deductions  = _requireFloat($request, 'total_deductions');
        $frequency   = $request['payroll_frequency'] ?? 'monthly';

        $profile = $gaa->analyze($employee_id, $gross, $deductions, $frequency);

        _respond(true, "Analysis complete. Status: {$profile['classification']['status']}.", $profile);
        break;


    // ══════════════════════════════════════════════════════════
    //  PRE-SAVE VALIDATE
    //  Hard gate before the Save Payroll button fires.
    //
    //  Rules:
    //   • CRITICAL employees → can_save = false (HARD BLOCK)
    //   • DANGER employees   → can_save = true  (SOFT WARNING)
    //   • Others             → pass silently
    //
    //  The JS save guard checks can_save and blocks the form
    //  submission if false, or shows a warning dialog if soft.
    // ══════════════════════════════════════════════════════════
    case 'pre_save_validate':

        $employees     = _requireArray($request, 'employees');
        $frequency     = $request['payroll_frequency'] ?? 'monthly';
        $period_label  = htmlspecialchars($request['period_label'] ?? 'current period');

        if ($employees === null) {
            _respond(false, 'employees array is required.', null, 422);
        }

        $violations = [];   // CRITICAL — hard block
        $warnings   = [];   // DANGER/WARNING — soft warn
        $summary    = ['CRITICAL' => 0, 'DANGER' => 0, 'WARNING' => 0,
                       'CAUTION' => 0, 'STABLE' => 0, 'SAFE' => 0];

        foreach ($employees as $emp) {
            $emp_id     = intval($emp['employee_id']       ?? 0);
            $emp_name   = htmlspecialchars($emp['name']    ?? "Employee #{$emp_id}");
            $gross      = floatval($emp['gross']           ?? 0);
            $deductions = floatval($emp['total_deductions'] ?? 0);
            $net_pay    = $gross - $deductions;

            // Validate through base validator (also logs to GAA status table)
            $validation = $gaa->validate($emp_id, $gross, $deductions);
            $cls        = $gaa->classify($net_pay);

            $summary[$cls['status']] = ($summary[$cls['status']] ?? 0) + 1;

            if ($cls['status'] === 'CRITICAL') {
                $shortfall = round(GAAConfig::MIN_NET_PAY - $net_pay, 2);
                $violations[] = [
                    'employee_id'  => $emp_id,
                    'employee_name'=> $emp_name,
                    'net_pay'      => round($net_pay, 2),
                    'gross'        => round($gross, 2),
                    'total_deductions' => round($deductions, 2),
                    'shortfall'    => $shortfall,
                    'status'       => 'CRITICAL',
                    'message'      => sprintf(
                        '%s: Net pay ₱%.2f is ₱%.2f below the GAA minimum.',
                        $emp_name, $net_pay, $shortfall
                    ),
                ];
            } elseif (in_array($cls['status'], ['DANGER', 'WARNING'])) {
                $warnings[] = [
                    'employee_id'  => $emp_id,
                    'employee_name'=> $emp_name,
                    'net_pay'      => round($net_pay, 2),
                    'buffer'       => $cls['buffer_amount'],
                    'status'       => $cls['status'],
                    'message'      => sprintf(
                        '%s: Net pay ₱%.2f is in the %s zone (buffer: ₱%.2f).',
                        $emp_name, $net_pay, $cls['status'], $cls['buffer_amount']
                    ),
                ];
            }
        }

        $can_save = empty($violations);

        _respond(
            $can_save,
            $can_save
                ? (empty($warnings)
                    ? 'All employees pass GAA validation. Ready to save.'
                    : count($warnings) . ' employee(s) are near the GAA threshold. Save is allowed but review is recommended.')
                : count($violations) . ' employee(s) have net pay below the GAA ₱5,000 minimum. Payroll cannot be saved until deductions are resolved.',
            [
                'can_save'        => $can_save,
                'blocked'         => !$can_save,
                'period_label'    => $period_label,
                'violations'      => $violations,
                'violation_count' => count($violations),
                'warnings'        => $warnings,
                'warning_count'   => count($warnings),
                'summary'         => $summary,
                'total_employees' => count($employees),
                'threshold'       => GAAConfig::MIN_NET_PAY,
            ]
        );
        break;


    // ══════════════════════════════════════════════════════════
    //  HEADROOM CHECK
    //  Live calculation: can a proposed deduction be safely added?
    //  Bind to deduction amount inputs for real-time feedback.
    // ══════════════════════════════════════════════════════════
    case 'headroom_check':

        $employee_id = _requireInt($request, 'employee_id');
        $gross       = _requireFloat($request, 'gross');
        $current_ded = _requireFloat($request, 'current_deductions');
        $proposed    = floatval($request['proposed_deduction'] ?? 0);

        $result = $gaa->headroom($employee_id, $gross, $current_ded, $proposed);

        _respond(true, "Headroom: ₱{$result['headroom_amount']}.", $result);
        break;


    // ══════════════════════════════════════════════════════════
    //  PERIOD SCAN
    //  Batch AI analysis reading from payroll_entries in the DB.
    //  Use for period overview panel and batch approval screen.
    // ══════════════════════════════════════════════════════════
    case 'period_scan':

        $period_id = _requireInt($request, 'payroll_period_id');
        $dept_id   = isset($request['dept_id']) ? intval($request['dept_id']) : null;

        $batch = $gaa->analyzeBatch($period_id, $dept_id);

        _respond(
            true,
            "Period scan complete. {$batch['summary']['at_risk_total']} employee(s) at risk.",
            $batch
        );
        break;


    // ── Unknown action ────────────────────────────────────────
    default:
        _respond(false, "Unknown action: '{$action}'.", null, 404);
        break;
}

// ══════════════════════════════════════════════════════════════
//  HELPER FUNCTIONS
// ══════════════════════════════════════════════════════════════

/**
 * Emit a JSON response and exit.
 */
function _respond(bool $success, string $message, $data = null, int $http = 200): void {
    http_response_code($http);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
        'meta'    => [
            'handler'   => 'gaa_payroll_handler',
            'version'   => GAAConfig::MODULE_VERSION,
            'timestamp' => time(),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function _requireInt(array $data, string $key): int {
    if (!isset($data[$key]) || !is_numeric($data[$key])) {
        _respond(false, "Missing or invalid parameter: {$key}", null, 422);
    }
    return intval($data[$key]);
}

function _requireFloat(array $data, string $key): float {
    if (!isset($data[$key]) || !is_numeric($data[$key])) {
        _respond(false, "Missing or invalid parameter: {$key}", null, 422);
    }
    return floatval($data[$key]);
}

function _requireArray(array $data, string $key): ?array {
    if (!isset($data[$key])) return null;
    if (is_string($data[$key])) {
        $decoded = json_decode($data[$key], true);
        return is_array($decoded) ? $decoded : null;
    }
    return is_array($data[$key]) ? $data[$key] : null;
}
?>
