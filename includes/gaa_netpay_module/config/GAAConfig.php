<?php
/**
 * GAAConfig.php  —  Central Configuration (Project-Aligned)
 * ─────────────────────────────────────────────────────────────
 *  Location : Munsoft/includes/gaa_netpay_module/config/
 *
 *  CHANGES FROM ORIGINAL:
 *   • SESSION_KEY updated to 'uid' (matches Munsoft session system)
 *   • AJAX_ENDPOINT updated to reflect payroll/ handler location
 *   • DB path resolution note updated (handled by validator wrapper)
 * ─────────────────────────────────────────────────────────────
 */

if (defined('GAA_CONFIG_LOADED')) return;
define('GAA_CONFIG_LOADED', true);

class GAAConfig {

    // ── Module metadata ───────────────────────────────────────
    const MODULE_VERSION      = '1.0.0';
    const MODULE_NAME         = 'GAA Net Pay Intelligence';

    // ── GAA threshold (RA 11466 / current GAA minimum) ────────
    const MIN_NET_PAY         = 5000.00;

    // ── Tier band percentages above threshold ─────────────────
    //   DANGER  : 0 – 10%  above threshold  (₱5,000 – ₱5,500)
    //   WARNING : 10 – 30% above threshold  (₱5,500 – ₱6,500)
    //   CAUTION : 30 – 50% above threshold  (₱6,500 – ₱7,500)
    //   STABLE  : 50 – 100% above threshold (₱7,500 – ₱10,000)
    //   SAFE    : > 100% above threshold    (> ₱10,000)
    const TIER_DANGER_PCT     = 0.10;
    const TIER_WARNING_PCT    = 0.30;
    const TIER_CAUTION_PCT    = 0.50;
    const TIER_STABLE_PCT     = 1.00;

    // ── AI risk score weights (must sum to 1.00) ──────────────
    const W_PROXIMITY         = 0.35;
    const W_TREND             = 0.25;
    const W_DEDUCTION         = 0.20;
    const W_ANOMALY           = 0.10;
    const W_PREDICTION        = 0.10;

    // ── AI analysis settings ──────────────────────────────────
    const HISTORY_WINDOW      = 6;     // Rolling payroll periods
    const ANOMALY_ZSCORE      = 2.0;   // Z-score threshold

    // ── SESSION: matches Munsoft's session variable ───────────
    //   The project uses $_SESSION['uid'] for the logged-in user.
    //   This key is used by the API endpoint for authorization.
    const SESSION_KEY         = 'uid';

    // ── API endpoint ──────────────────────────────────────────
    //   The module API is at includes/gaa_netpay_module/api/
    //   The payroll handler is at payroll/gaa_payroll_handler.php
    //   JS files call the PAYROLL handler (not the raw API directly).
    const AJAX_ENDPOINT       = '/includes/gaa_netpay_module/api/gaa_api.php';
    const PAYROLL_HANDLER     = '/payroll/gaa_payroll_handler.php';

    // ── Logging ───────────────────────────────────────────────
    const LOG_ENABLED         = true;
    const API_REQUIRE_SESSION = true;
    const API_LOG_REQUESTS    = false;  // Set true for full audit
    const LOG_TABLE           = 'gaa_validation_audit';
    const LOG_STATUS_TABLE    = 'payroll_gaa_status';

    // ── Status color palette ──────────────────────────────────
    const STATUS_COLORS = [
        'CRITICAL' => '#DC2626',
        'DANGER'   => '#EA580C',
        'WARNING'  => '#D97706',
        'CAUTION'  => '#CA8A04',
        'STABLE'   => '#2563EB',
        'SAFE'     => '#16A34A',
    ];

    // ── Status icons ──────────────────────────────────────────
    const STATUS_ICONS = [
        'CRITICAL' => '🔴',
        'DANGER'   => '🟠',
        'WARNING'  => '🟡',
        'CAUTION'  => '🔵',
        'STABLE'   => '🟢',
        'SAFE'     => '✅',
    ];

    /**
     * Return absolute peso tier boundaries derived from MIN_NET_PAY.
     * Single source of truth — change MIN_NET_PAY and all tiers adjust.
     */
    public static function getTierBoundaries(): array {
        $t = self::MIN_NET_PAY;
        return [
            'danger_upper'  => $t * (1 + self::TIER_DANGER_PCT),
            'warning_upper' => $t * (1 + self::TIER_WARNING_PCT),
            'caution_upper' => $t * (1 + self::TIER_CAUTION_PCT),
            'stable_upper'  => $t * (1 + self::TIER_STABLE_PCT),
        ];
    }
}
?>
