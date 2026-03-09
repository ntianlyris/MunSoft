<?php
/**
 * gaa_status_widget.php  —  GAA Intelligence Panel (Drop-in Partial)
 * ─────────────────────────────────────────────────────────────
 *  Location : Munsoft/payroll/
 *
 *  PURPOSE:
 *  A self-contained PHP partial that can be dropped into any
 *  payroll page with a single include:
 *
 *    <?php include 'gaa_status_widget.php'; ?>
 *
 *  It renders the HTML container structure and enqueues all
 *  required CSS and JS (once per page via a guard constant).
 *  The JavaScript (gaa_payroll.js) populates the containers
 *  after it scans the payroll table.
 *
 *  ASSET PATHS: All asset paths are root-relative so they work
 *  regardless of where in the application this file is included.
 *
 *  ─── WIDGET SECTIONS ────────────────────────────────────────
 *
 *  #gaa-widget              Outer collapsible container
 *  ├── #gaa-summary-bar     Tier count pills (Critical/Danger…)
 *  ├── #gaa-risk-band       Period-level overall risk band
 *  ├── #gaa-at-risk-list    Top at-risk employees list
 *  └── #gaa-dept-heatmap    Department risk heat map cards
 *
 *  #gaa-save-modal          Save guard modal (hidden by default)
 *  #gaa-employee-detail     Slide-in detail panel per employee
 * ─────────────────────────────────────────────────────────────
 */

// ── Asset enqueue guard (prevent duplicate CSS/JS per page) ──
if (!defined('GAA_WIDGET_ASSETS_LOADED')) {
    define('GAA_WIDGET_ASSETS_LOADED', true);
    $gaa_asset_block = true;
} else {
    $gaa_asset_block = false;
}
?>

<?php if ($gaa_asset_block): ?>
<!-- ── GAA Net Pay Intelligence — Stylesheets ──────────────── -->
<link rel="stylesheet"
      href="/MunSoft/includes/gaa_netpay_module/assets/css/gaa.css">
<style>
/* ── Widget shell ─────────────────────────────────────────── */
#gaa-widget {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin: 18px 0;
    font-family: inherit;
    overflow: hidden;
    transition: all .3s ease;
}
#gaa-widget-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 18px;
    background: #F8FAFC;
    border-bottom: 1px solid #E5E7EB;
    cursor: pointer;
    user-select: none;
}
#gaa-widget-header h4 {
    margin: 0;
    font-size: .92rem;
    font-weight: 700;
    color: #1F2937;
    display: flex;
    align-items: center;
    gap: 8px;
}
#gaa-widget-body {
    padding: 16px 18px;
    display: block;
}
#gaa-widget-body.gaa-collapsed { display: none; }

/* ── Summary bar ────────────────────────────────────────── */
#gaa-summary-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
}
.gaa-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 700;
    color: #fff;
    min-width: 80px;
    justify-content: center;
    cursor: default;
    transition: transform .15s;
}
.gaa-pill:hover { transform: scale(1.05); }
.gaa-pill .gaa-pill-count {
    font-size: 1.1rem;
    font-weight: 800;
    line-height: 1;
}
.gaa-pill--critical { background: #DC2626; }
.gaa-pill--danger   { background: #EA580C; }
.gaa-pill--warning  { background: #D97706; }
.gaa-pill--caution  { background: #CA8A04; }
.gaa-pill--stable   { background: #2563EB; }
.gaa-pill--safe     { background: #16A34A; }

/* ── Risk band bar ──────────────────────────────────────── */
#gaa-risk-band {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 14px;
    border-radius: 8px;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    margin-bottom: 14px;
}
#gaa-risk-band .gaa-risk-label {
    font-size: .8rem;
    color: #6B7280;
}
#gaa-risk-band .gaa-risk-value {
    font-size: 1rem;
    font-weight: 800;
}
#gaa-risk-band .gaa-risk-bar-wrap {
    flex: 1;
    height: 8px;
    background: #E5E7EB;
    border-radius: 4px;
    overflow: hidden;
}
#gaa-risk-band .gaa-risk-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width .8s ease;
}

/* ── At-risk list ───────────────────────────────────────── */
#gaa-at-risk-list {
    margin-bottom: 14px;
}
#gaa-at-risk-list h5 {
    font-size: .8rem;
    color: #6B7280;
    font-weight: 600;
    margin: 0 0 8px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.gaa-at-risk-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 6px;
    background: #F9FAFB;
    margin-bottom: 5px;
    font-size: .82rem;
    cursor: pointer;
    transition: background .15s;
}
.gaa-at-risk-row:hover { background: #F3F4F6; }
.gaa-at-risk-row .gaa-emp-name  { flex: 1; font-weight: 600; color: #1F2937; }
.gaa-at-risk-row .gaa-emp-dept  { font-size: .75rem; color: #9CA3AF; min-width: 80px; }
.gaa-at-risk-row .gaa-emp-net   { font-weight: 700; min-width: 90px; text-align: right; }
.gaa-at-risk-row .gaa-risk-num  { font-size: .75rem; min-width: 55px; text-align: right; color: #6B7280; }
.gaa-at-risk-empty  { font-size: .82rem; color: #16A34A; padding: 8px 0; }

/* ── Dept heat map ──────────────────────────────────────── */
#gaa-dept-heatmap {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

/* ── Table badge cells ──────────────────────────────────── */
.gaa-table-badge {
    white-space: nowrap;
}
.gaa-table-badge .gaa-badge {
    cursor: pointer;
}

/* ── Employee detail panel (slide-in) ──────────────────── */
#gaa-employee-detail {
    position: fixed;
    top: 0;
    right: -440px;
    width: 420px;
    height: 100vh;
    background: #fff;
    box-shadow: -4px 0 24px rgba(0,0,0,.12);
    z-index: 9900;
    overflow-y: auto;
    transition: right .35s cubic-bezier(.4,0,.2,1);
    padding: 0;
}
#gaa-employee-detail.gaa-panel-open {
    right: 0;
}
#gaa-detail-header {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
    padding: 16px 20px 12px;
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
#gaa-detail-header h3 {
    margin: 0;
    font-size: .95rem;
    font-weight: 700;
    color: #1F2937;
}
#gaa-detail-close {
    background: none;
    border: none;
    font-size: 1.3rem;
    cursor: pointer;
    color: #6B7280;
    padding: 0 4px;
    line-height: 1;
}
#gaa-detail-body {
    padding: 16px 20px;
}
#gaa-detail-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.25);
    z-index: 9899;
}

/* ── Save guard modal ───────────────────────────────────── */
#gaa-save-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
#gaa-save-modal.gaa-modal-open { display: flex; }
#gaa-save-modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.45);
}
#gaa-save-modal-box {
    position: relative;
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 520px;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    animation: gaaFadeIn .25s ease;
}
#gaa-save-modal-header {
    padding: 18px 22px 14px;
    border-bottom: 1px solid #E5E7EB;
}
#gaa-save-modal-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #DC2626;
    display: flex;
    align-items: center;
    gap: 8px;
}
#gaa-save-modal-body {
    padding: 16px 22px;
    font-size: .85rem;
}
#gaa-save-modal-footer {
    padding: 12px 22px 18px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    border-top: 1px solid #E5E7EB;
}
.gaa-btn {
    padding: 9px 20px;
    border-radius: 7px;
    font-size: .85rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: opacity .15s;
}
.gaa-btn:hover { opacity: .85; }
.gaa-btn--primary { background: #2563EB; color: #fff; }
.gaa-btn--danger  { background: #DC2626; color: #fff; }
.gaa-btn--ghost   { background: #F3F4F6; color: #374151; }

/* ── Collapsible toggle icon ────────────────────────────── */
.gaa-toggle-icon { transition: transform .25s; font-size: .85rem; color: #6B7280; }
.gaa-toggle-icon.gaa-rotated { transform: rotate(180deg); }

/* ── Loading state ──────────────────────────────────────── */
.gaa-scanning {
    font-size: .82rem;
    color: #9CA3AF;
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.gaa-spinner {
    width: 16px; height: 16px;
    border: 2px solid #E5E7EB;
    border-top-color: #2563EB;
    border-radius: 50%;
    animation: gaaSpinAnim .7s linear infinite;
    flex-shrink: 0;
}
@keyframes gaaSpinAnim { to { transform: rotate(360deg); } }
</style>
<?php endif; ?>

<!-- ════════════════════════════════════════════════════════════
     GAA INTELLIGENCE WIDGET
     ════════════════════════════════════════════════════════════ -->
<div id="gaa-widget" role="region" aria-label="GAA Net Pay Intelligence Panel">

    <!-- Header / Toggle -->
    <div id="gaa-widget-header" onclick="GAAPayroll.toggleWidget()" role="button"
         aria-expanded="true" aria-controls="gaa-widget-body">
        <h4>
            <span>🛡</span>
            <span>GAA Net Pay Intelligence</span>
            <span id="gaa-widget-scanning" class="gaa-scanning" style="display:none;">
                <span class="gaa-spinner"></span> Scanning…
            </span>
        </h4>
        <span class="gaa-toggle-icon" id="gaa-toggle-icon">▼</span>
    </div>

    <!-- Body -->
    <div id="gaa-widget-body">

        <!-- Summary Tier Pills -->
        <div id="gaa-summary-bar">
            <span class="gaa-pill gaa-pill--critical" id="gaa-pill-critical" title="Net pay below ₱5,000">
                🔴 <span class="gaa-pill-count" id="gaa-count-critical">–</span> Critical
            </span>
            <span class="gaa-pill gaa-pill--danger"   id="gaa-pill-danger" title="₱5,000 – ₱5,500">
                🟠 <span class="gaa-pill-count" id="gaa-count-danger">–</span> Danger
            </span>
            <span class="gaa-pill gaa-pill--warning"  id="gaa-pill-warning" title="₱5,500 – ₱6,500">
                🟡 <span class="gaa-pill-count" id="gaa-count-warning">–</span> Warning
            </span>
            <span class="gaa-pill gaa-pill--caution"  id="gaa-pill-caution" title="₱6,500 – ₱7,500">
                🔵 <span class="gaa-pill-count" id="gaa-count-caution">–</span> Caution
            </span>
            <span class="gaa-pill gaa-pill--stable"   id="gaa-pill-stable" title="₱7,500 – ₱10,000">
                🟢 <span class="gaa-pill-count" id="gaa-count-stable">–</span> Stable
            </span>
            <span class="gaa-pill gaa-pill--safe"     id="gaa-pill-safe" title="Above ₱10,000">
                ✅ <span class="gaa-pill-count" id="gaa-count-safe">–</span> Safe
            </span>
        </div>

        <!-- Overall Risk Band -->
        <div id="gaa-risk-band">
            <span class="gaa-risk-label">Period Risk:</span>
            <span class="gaa-risk-value" id="gaa-period-risk-label" style="color:#9CA3AF;">—</span>
            <div class="gaa-risk-bar-wrap">
                <div class="gaa-risk-bar-fill" id="gaa-period-risk-bar"
                     style="width:0%; background:#E5E7EB;"></div>
            </div>
            <span style="font-size:.78rem; color:#6B7280;" id="gaa-period-risk-score">–/100</span>
        </div>

        <!-- At-Risk Employees -->
        <div id="gaa-at-risk-list">
            <h5>⚠ At-Risk Employees</h5>
            <div id="gaa-at-risk-rows">
                <div class="gaa-scanning">
                    <span class="gaa-spinner"></span> Waiting for payroll data…
                </div>
            </div>
        </div>

        <!-- Department Heat Map -->
        <div id="gaa-dept-section" style="display:none;">
            <h5 style="font-size:.8rem; color:#6B7280; font-weight:600;
                       text-transform:uppercase; letter-spacing:.04em; margin:0 0 8px;">
                Department Risk Heat Map
            </h5>
            <div id="gaa-dept-heatmap"></div>
        </div>

    </div><!-- /gaa-widget-body -->
</div><!-- /gaa-widget -->


<!-- ════════════════════════════════════════════════════════════
     EMPLOYEE DETAIL SLIDE-IN PANEL
     ════════════════════════════════════════════════════════════ -->
<div id="gaa-detail-overlay" onclick="GAAPayroll.closeDetailPanel()"></div>
<div id="gaa-employee-detail" role="dialog" aria-label="Employee GAA Detail"
     aria-modal="true">
    <div id="gaa-detail-header">
        <h3 id="gaa-detail-emp-name">Employee Detail</h3>
        <button id="gaa-detail-close" onclick="GAAPayroll.closeDetailPanel()"
                aria-label="Close panel">✕</button>
    </div>
    <div id="gaa-detail-body">
        <!-- Populated by GAAPayroll.openDetailPanel() via gaa.ui.js renderers -->
        <div id="gaa-detail-card"></div>
        <div id="gaa-detail-meter" style="margin-top:14px;"></div>
        <div id="gaa-detail-sparkline" style="margin-top:14px;"></div>
        <div id="gaa-detail-recs" style="margin-top:14px;"></div>
    </div>
</div>


<!-- ════════════════════════════════════════════════════════════
     SAVE GUARD MODAL  (shown when CRITICAL violations found)
     ════════════════════════════════════════════════════════════ -->
<div id="gaa-save-modal" role="alertdialog" aria-modal="true"
     aria-labelledby="gaa-modal-title">
    <div id="gaa-save-modal-overlay"></div>
    <div id="gaa-save-modal-box">

        <div id="gaa-save-modal-header">
            <h3 id="gaa-modal-title">
                🚫 Cannot Save — GAA Threshold Violations
            </h3>
        </div>

        <div id="gaa-save-modal-body">
            <p style="color:#4B5563; margin:0 0 12px; font-size:.85rem;">
                The following employee(s) have a net pay below the
                <strong>₱5,000 GAA minimum</strong>.
                Payroll cannot be saved until their deductions are resolved.
            </p>
            <!-- Populated by JS with violation rows -->
            <div id="gaa-modal-violations"></div>

            <!-- Soft warning block (DANGER/WARNING) — visible even when can_save=true -->
            <div id="gaa-modal-warnings" style="display:none; margin-top:14px;"></div>
        </div>

        <div id="gaa-save-modal-footer">
            <!-- Buttons injected by JS based on can_save flag -->
            <button class="gaa-btn gaa-btn--ghost" id="gaa-modal-close-btn"
                    onclick="GAAPayroll.closeSaveModal()">
                Review Deductions
            </button>
            <!-- "Proceed Anyway" only shown when no CRITICAL — only soft warnings -->
            <button class="gaa-btn gaa-btn--primary" id="gaa-modal-proceed-btn"
                    style="display:none;"
                    onclick="GAAPayroll.proceedSave()">
                Save Anyway
            </button>
        </div>

    </div>
</div>


<?php if ($gaa_asset_block): ?>
<!-- ── GAA Module JS (in dependency order) ─────────────────── -->
<script src="/MunSoft/includes/gaa_netpay_module/js/gaa.core.js"></script>
<script src="/MunSoft/includes/gaa_netpay_module/js/gaa.ui.js"></script>
<script src="/MunSoft/includes/gaa_netpay_module/js/gaa.handlers.js"></script>
<!-- ── Payroll-specific GAA glue layer ─────────────────────── -->
<script src="/MunSoft/payroll/gaa_ai/js/gaa_payroll.js"></script>
<?php endif; ?>
