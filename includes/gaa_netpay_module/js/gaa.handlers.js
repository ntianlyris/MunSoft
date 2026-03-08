/**
 * gaa.handlers.js
 * ─────────────────────────────────────────────────────────
 *  GAA Net Pay Intelligence Module — Event Handler Bindings
 *
 *  Depends on: gaa.core.js, gaa.ui.js (load in that order)
 *
 *  This file is the "glue" layer. It:
 *    1. Detects special data-gaa-* attributes on DOM elements
 *    2. Binds the correct GAACore AJAX calls to their events
 *    3. Pipes results through GAAui to update the DOM
 *    4. Provides GAAHandlers.init() for manual bootstrapping
 *
 *  Auto-binding attributes:
 *
 *    data-gaa-action="validate_entry"
 *      — Binds 'input' event on a deduction field.
 *        Required siblings: data-gaa-employee-id, data-gaa-gross
 *        Target: data-gaa-target="#someBadgeDiv"
 *
 *    data-gaa-action="validate_payroll"
 *      — Binds to a Save Payroll button 'click'.
 *        Required attrs: data-gaa-employee-id, data-gaa-gross,
 *                        data-gaa-deductions, data-gaa-target
 *
 *    data-gaa-action="analyze_employee"
 *      — Triggers a full AI analysis. Same attrs as above.
 *        Renders a full status card + recommendations.
 *
 *    data-gaa-action="headroom"
 *      — Binds to proposed deduction input 'input' event.
 *        Renders headroom bar live.
 *
 *  Manual API (for code-driven use):
 *    GAAHandlers.onDeductionChange(inputEl, options)
 *    GAAHandlers.onSavePayroll(buttonEl, options)
 *    GAAHandlers.onAnalyzeEmployee(employeeId, gross, deductions, targets)
 *    GAAHandlers.onHeadroomChange(inputEl, options)
 *    GAAHandlers.onBatchAnalyze(periodId, targets)
 * ─────────────────────────────────────────────────────────
 */

const GAAHandlers = (() => {

    // ── Debounce utility ──────────────────────────────────
    const _debounce = (fn, ms = 400) => {
        let timer;
        return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), ms); };
    };

    // ── Loading state helper ──────────────────────────────
    const _setLoading = (el, loading) => {
        if (!el) return;
        el.setAttribute('data-gaa-loading', loading ? '1' : '0');
        if (loading) {
            el.innerHTML = '<span style="color:#9CA3AF; font-size:0.8rem;">⏳ Analyzing…</span>';
        }
    };

    // ── Read field value by selector or data attr ─────────
    const _val = (selector) => {
        if (!selector) return null;
        const el = document.querySelector(selector);
        return el ? parseFloat(el.value || el.textContent) : null;
    };

    // ─────────────────────────────────────────────────────
    //  HANDLER 1: Real-time deduction entry validation
    // ─────────────────────────────────────────────────────
    /**
     * Bind real-time validation to a deduction input field.
     *
     * @param {string|Element} inputSelector   The deduction amount input
     * @param {Object} options
     *   @param {number}          options.employeeId
     *   @param {number|string}   options.grossSelector   Selector for gross field OR a raw number
     *   @param {string}          options.badgeTarget     Selector for badge render target
     *   @param {string}          options.cardTarget      Optional selector for full status card
     *   @param {Function}        options.onBlock         Callback when BLOCKED (status=false)
     *   @param {Function}        options.onAllow         Callback when ALLOWED
     */
    const onDeductionChange = (inputSelector, options = {}) => {
        const input = typeof inputSelector === 'string'
            ? document.querySelector(inputSelector) : inputSelector;
        if (!input) return;

        const handler = _debounce(async () => {
            const deductions = parseFloat(input.value) || 0;
            if (deductions <= 0) return;

            const gross = typeof options.grossSelector === 'number'
                ? options.grossSelector
                : (_val(options.grossSelector) || null);

            const badgeEl = document.querySelector(options.badgeTarget);
            _setLoading(badgeEl, true);

            const res = await GAACore.validateEntry(options.employeeId, deductions, gross);

            // Render badge
            if (badgeEl) GAAui.renderBadge(badgeEl, res);

            // Render full card if target provided
            if (options.cardTarget) {
                // Fetch full analysis for card render
                const gross2 = gross ?? 0;
                const full   = await GAACore.analyzeEmployee(options.employeeId, gross2, deductions);
                GAAui.renderStatusCard(options.cardTarget, full);
            }

            // Callbacks
            if (!res.success && typeof options.onBlock === 'function') {
                options.onBlock(res);
            }
            if (res.success && typeof options.onAllow === 'function') {
                options.onAllow(res);
            }

        }, 350);

        input.addEventListener('input', handler);
        input.addEventListener('change', handler);
    };

    // ─────────────────────────────────────────────────────
    //  HANDLER 2: Save payroll button validation
    // ─────────────────────────────────────────────────────
    /**
     * Bind Stage-2 validation to a Save Payroll button.
     * Prevents the default action if GAA validation fails.
     *
     * @param {string|Element} buttonSelector
     * @param {Object} options
     *   @param {number}  options.employeeId
     *   @param {string}  options.grossSelector
     *   @param {string}  options.deductionsSelector
     *   @param {string}  options.resultTarget         DOM target for validation result
     *   @param {Function} options.onValidPass         Called on success
     *   @param {Function} options.onValidFail         Called on violation
     */
    const onSavePayroll = (buttonSelector, options = {}) => {
        const btn = typeof buttonSelector === 'string'
            ? document.querySelector(buttonSelector) : buttonSelector;
        if (!btn) return;

        btn.addEventListener('click', async (e) => {
            e.preventDefault();

            const gross      = _val(options.grossSelector) ?? 0;
            const deductions = _val(options.deductionsSelector) ?? 0;

            // Show loading on button
            const origText  = btn.innerHTML;
            btn.disabled    = true;
            btn.innerHTML   = '⏳ Validating…';

            const res = await GAACore.validatePayroll(options.employeeId, gross, deductions);

            btn.disabled  = false;
            btn.innerHTML = origText;

            if (options.resultTarget) {
                GAAui.renderBadge(options.resultTarget, res.data ?? res);
            }

            if (!res.success) {
                _showToast(res.message ?? 'GAA validation failed.', 'error');
                if (typeof options.onValidFail === 'function') options.onValidFail(res);
            } else {
                if (typeof options.onValidPass === 'function') options.onValidPass(res);
            }
        });
    };

    // ─────────────────────────────────────────────────────
    //  HANDLER 3: Full AI employee analysis (on-demand)
    // ─────────────────────────────────────────────────────
    /**
     * Trigger a full AI analysis and render results into target containers.
     *
     * @param {number} employeeId
     * @param {number} gross
     * @param {number} deductions
     * @param {Object} targets   Map of { badge, card, meter, recs, sparkline }
     */
    const onAnalyzeEmployee = async (employeeId, gross, deductions, targets = {}) => {
        // Set loading states
        Object.values(targets).forEach(sel => {
            const el = document.querySelector(sel);
            if (el) _setLoading(el, true);
        });

        const res = await GAACore.analyzeEmployee(employeeId, gross, deductions);

        if (!res.success) {
            _showToast(res.message ?? 'Analysis failed.', 'error');
            return res;
        }

        if (targets.badge)     GAAui.renderBadge(targets.badge, res);
        if (targets.card)      GAAui.renderStatusCard(targets.card, res);
        if (targets.meter)     GAAui.renderRiskMeter(targets.meter, res);
        if (targets.recs)      GAAui.renderRecommendations(targets.recs, res);
        if (targets.sparkline) GAAui.renderTrendSparkline(targets.sparkline, res);

        return res;
    };

    // ─────────────────────────────────────────────────────
    //  HANDLER 4: Live headroom calculator
    // ─────────────────────────────────────────────────────
    /**
     * Bind live headroom calculation to a proposed deduction input.
     *
     * @param {string|Element} inputSelector
     * @param {Object} options
     *   @param {number} options.employeeId
     *   @param {number} options.gross
     *   @param {number} options.currentDeductions
     *   @param {string} options.barTarget   Selector for headroom bar container
     */
    const onHeadroomChange = (inputSelector, options = {}) => {
        const input = typeof inputSelector === 'string'
            ? document.querySelector(inputSelector) : inputSelector;
        if (!input) return;

        const handler = _debounce(async () => {
            const proposed = parseFloat(input.value) || 0;
            const res = await GAACore.getHeadroom(
                options.employeeId,
                options.gross,
                options.currentDeductions,
                proposed
            );
            if (options.barTarget) GAAui.renderHeadroomBar(options.barTarget, res);
        }, 300);

        input.addEventListener('input', handler);
    };

    // ─────────────────────────────────────────────────────
    //  HANDLER 5: Batch period analysis
    // ─────────────────────────────────────────────────────
    /**
     * Run batch analysis for a period and render results.
     *
     * @param {number} periodId
     * @param {Object} targets   Map of { table, heatmap, summary }
     * @param {number|null} deptId
     */
    const onBatchAnalyze = async (periodId, targets = {}, deptId = null) => {
        if (targets.table)   _setLoading(document.querySelector(targets.table), true);
        if (targets.heatmap) _setLoading(document.querySelector(targets.heatmap), true);

        const res = await GAACore.analyzeBatch(periodId, deptId);

        if (!res.success) {
            _showToast(res.message ?? 'Batch analysis failed.', 'error');
            return res;
        }

        if (targets.table)   GAAui.renderBatchTable(targets.table, res);
        if (targets.heatmap) GAAui.renderDeptHeatmap(targets.heatmap, res);

        if (targets.summary) {
            const s  = res.data?.summary ?? {};
            const el = document.querySelector(targets.summary);
            if (el) {
                el.innerHTML = `
                  <div style="display:flex; gap:16px; flex-wrap:wrap; font-family:inherit;">
                    ${_summaryPill('Critical', s.critical_count ?? 0, '#DC2626')}
                    ${_summaryPill('Danger',   s.danger_count   ?? 0, '#EA580C')}
                    ${_summaryPill('Warning',  s.warning_count  ?? 0, '#D97706')}
                    ${_summaryPill('Avg Risk', (s.avg_risk_score ?? 0) + '/100', '#6B7280')}
                  </div>`;
            }
        }

        return res;
    };

    const _summaryPill = (label, value, color) => `
      <div style="background:${color}15; border:1px solid ${color}40; border-radius:8px;
           padding:8px 14px; text-align:center; min-width:90px;">
        <div style="font-size:1.2rem; font-weight:800; color:${color};">${value}</div>
        <div style="font-size:0.7rem; color:#6B7280;">${label}</div>
      </div>`;

    // ─────────────────────────────────────────────────────
    //  AUTO-INIT: scan DOM for data-gaa-action attributes
    // ─────────────────────────────────────────────────────
    /**
     * Scan the document for elements with data-gaa-action and auto-bind.
     * Call this after DOMContentLoaded or after dynamic content is injected.
     */
    const init = () => {
        document.querySelectorAll('[data-gaa-action]').forEach(el => {
            const action     = el.getAttribute('data-gaa-action');
            const employeeId = parseInt(el.getAttribute('data-gaa-employee-id'));
            const target     = el.getAttribute('data-gaa-target');

            switch (action) {

                case 'validate_entry':
                    onDeductionChange(el, {
                        employeeId,
                        grossSelector: el.getAttribute('data-gaa-gross-selector'),
                        badgeTarget:   target,
                    });
                    break;

                case 'validate_payroll':
                    onSavePayroll(el, {
                        employeeId,
                        grossSelector:      el.getAttribute('data-gaa-gross-selector'),
                        deductionsSelector: el.getAttribute('data-gaa-deductions-selector'),
                        resultTarget:       target,
                    });
                    break;

                case 'analyze_employee':
                    el.addEventListener('click', () => {
                        const gross      = _val(el.getAttribute('data-gaa-gross-selector'));
                        const deductions = _val(el.getAttribute('data-gaa-deductions-selector'));
                        onAnalyzeEmployee(employeeId, gross, deductions, {
                            card:  target,
                            meter: el.getAttribute('data-gaa-meter-target'),
                            recs:  el.getAttribute('data-gaa-recs-target'),
                        });
                    });
                    break;

                case 'headroom':
                    onHeadroomChange(el, {
                        employeeId,
                        gross:             parseFloat(el.getAttribute('data-gaa-gross')) || 0,
                        currentDeductions: parseFloat(el.getAttribute('data-gaa-current-deductions')) || 0,
                        barTarget:         target,
                    });
                    break;
            }
        });
    };

    // ─────────────────────────────────────────────────────
    //  TOAST NOTIFICATION (zero-dependency inline)
    // ─────────────────────────────────────────────────────
    const _showToast = (message, type = 'info') => {
        const colors = { error: '#DC2626', success: '#16A34A', info: '#2563EB', warning: '#D97706' };
        const toast  = document.createElement('div');
        toast.style.cssText = `
          position:fixed; bottom:24px; right:24px; z-index:9999;
          background:${colors[type] ?? '#374151'}; color:#fff;
          padding:12px 20px; border-radius:8px; font-size:0.85rem;
          font-family:inherit; box-shadow:0 4px 12px rgba(0,0,0,.2);
          max-width:380px; animation:gaaFadeIn .3s ease;`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4500);
    };

    // Auto-init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ── Public surface ────────────────────────────────────
    return {
        init,
        onDeductionChange,
        onSavePayroll,
        onAnalyzeEmployee,
        onHeadroomChange,
        onBatchAnalyze,
    };

})();

window.GAAHandlers = GAAHandlers;
