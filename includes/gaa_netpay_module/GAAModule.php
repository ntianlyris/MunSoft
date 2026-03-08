<?php
/**
 * ============================================================
 *  GAA NET PAY INTELLIGENCE MODULE  —  Entry Point / Loader
 * ============================================================
 *  File    : GAAModule.php
 *  Purpose : Single-file auto-loader for the entire GAA Net Pay
 *            Intelligence module. Any external folder / feature
 *            that needs the module only needs ONE include:
 *
 *              require_once '/path/to/gaa_netpay_module/GAAModule.php';
 *              $gaa = GAAModule::getInstance();
 *
 *  Structure:
 *    gaa_netpay_module/
 *    ├── GAAModule.php                ← YOU ARE HERE (entry point)
 *    ├── config/
 *    │   └── GAAConfig.php           ← Centralized constants & settings
 *    ├── core/
 *    │   ├── GAANetPayValidator.php  ← Base validator (Stage 1–3)
 *    │   └── GAANetPayIntelligence.php ← AI intelligence layer
 *    ├── handlers/
 *    │   ├── GAABridgeHandler.php    ← PHP→UI bridge (AJAX dispatcher)
 *    │   └── GAAResponseBuilder.php ← Standardized JSON response builder
 *    ├── api/
 *    │   └── gaa_api.php             ← REST-style API endpoint
 *    ├── js/
 *    │   ├── gaa.core.js             ← AJAX request engine
 *    │   ├── gaa.ui.js               ← DOM badge/status renderers
 *    │   └── gaa.handlers.js         ← Event handler bindings
 *    ├── assets/css/
 *    │   └── gaa.css                 ← Status badge & widget styles
 *    └── utils/
 *        └── GAALogger.php           ← Module-level audit logger
 *
 *  Version : 1.0  |  Date: March 7, 2026
 * ============================================================
 */

// ── Guard against double-loading ──────────────────────────
if (defined('GAA_MODULE_LOADED')) return;
define('GAA_MODULE_LOADED', true);

// ── Resolve absolute module root (works from any include depth) ──
define('GAA_MODULE_ROOT', __DIR__);

// ── Load components in dependency order ──────────────────
$gaa_load_order = [
    '/config/GAAConfig.php',
    '/utils/GAALogger.php',
    '/handlers/GAAResponseBuilder.php',
    '/core/GAANetPayValidator.php',
    '/core/GAANetPayIntelligence.php',
    '/handlers/GAABridgeHandler.php',
];

foreach ($gaa_load_order as $file) {
    $path = GAA_MODULE_ROOT . $file;
    if (!file_exists($path)) {
        error_log("[GAAModule] Missing required file: {$path}");
        // Fail gracefully — do not crash the host application
        continue;
    }
    require_once $path;
}

// ═════════════════════════════════════════════════════════
//  GAAModule  —  Singleton Facade
//  Provides a clean, unified API so callers never need to
//  instantiate individual classes directly.
// ═════════════════════════════════════════════════════════
class GAAModule {

    private static ?GAAModule $instance = null;

    /** @var GAANetPayIntelligence */
    private GAANetPayIntelligence $intelligence;

    /** @var GAABridgeHandler */
    private GAABridgeHandler $bridge;

    /** @var GAALogger */
    private GAALogger $logger;

    // ── Constructor ──────────────────────────────────────
    private function __construct() {
        $this->intelligence = new GAANetPayIntelligence();
        $this->bridge       = new GAABridgeHandler($this->intelligence);
        $this->logger       = new GAALogger();
    }

    // ── Singleton accessor ───────────────────────────────
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ── Proxy: Intelligence layer ─────────────────────────

    /** Classify a net pay value into a status tier */
    public function classify(float $net_pay): array {
        return $this->intelligence->classifyNetPayStatus($net_pay);
    }

    /** Full AI risk profile for an employee */
    public function analyze(int $employee_id, float $gross, float $deductions, string $frequency = 'monthly'): array {
        return $this->intelligence->analyzeEmployee($employee_id, $gross, $deductions, $frequency);
    }

    /** Batch intelligence for an entire payroll period */
    public function analyzeBatch(int $period_id, ?int $dept_id = null): array {
        return $this->intelligence->analyzeBatch($period_id, $dept_id);
    }

    /** Validate a single payroll entry (inherited from base validator) */
    public function validate(int $employee_id, float $gross, float $deductions): array {
        return $this->intelligence->validatePayrollEntry($employee_id, $gross, $deductions);
    }

    /** How much more deduction headroom does this employee have? */
    public function headroom(int $employee_id, float $gross, float $current_deductions, float $proposed = 0): array {
        return $this->intelligence->calculateDeductionHeadroom($employee_id, $gross, $current_deductions, $proposed);
    }

    // ── Proxy: Bridge handler (AJAX dispatch) ────────────

    /** Dispatch an incoming AJAX request by action key */
    public function dispatch(array $request): void {
        $this->bridge->dispatch($request);
    }

    // ── Asset helpers ────────────────────────────────────

    /** Return path for enqueuing JS files */
    public static function jsPath(string $filename): string {
        return GAA_MODULE_ROOT . '/js/' . ltrim($filename, '/');
    }

    /** Return URL-relative path for <script src=""> tags */
    public static function jsUrl(string $filename, string $base_url = ''): string {
        $relative = '/gaa_netpay_module/js/' . ltrim($filename, '/');
        return rtrim($base_url, '/') . $relative;
    }

    /** Return URL-relative path for <link href=""> tags */
    public static function cssUrl(string $filename = 'gaa.css', string $base_url = ''): string {
        $relative = '/gaa_netpay_module/assets/css/' . ltrim($filename, '/');
        return rtrim($base_url, '/') . $relative;
    }

    // ── Version ──────────────────────────────────────────
    public static function version(): string {
        return GAAConfig::MODULE_VERSION;
    }
}
?>
