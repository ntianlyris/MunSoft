/**
 * gaa_employee.js
 * ─────────────────────────────────────────────────────────────
 *  Location : employee/scripts/
 *
 *  PURPOSE:
 *  Integration logic for the employee-side GAA Net Pay Intelligence widget.
 *  Uses the GAA module's UI renderer to show the current employee's risk status.
 * ─────────────────────────────────────────────────────────────
 */

const GAAEmployee = (() => {

    let _lastData = null;

    /**
     * Load and render the GAA Risk Status for a single employee.
     * @param {number|string} empId 
     * @param {number} gross 
     * @param {number} deductions 
     */
    const loadRiskStatus = async (empId, gross, deductions) => {
        const container = document.getElementById('gaa-risk-widget-outer');
        if (!container) return;

        // Keep a reference to the original content in case we need to roll back or show error
        const originalContent = container.innerHTML;

        // Show loading state
        container.innerHTML = `
            <div class="card card-outline card-primary h-100">
                <div class="card-body text-center py-4">
                    <div class="spinner-border text-primary spinner-border-sm" role="status">
                        <span class="sr-only">Analyzing...</span>
                    </div>
                    <p class="mt-2 mb-0 text-muted small">AI Analysis in progress...</p>
                </div>
            </div>
        `;

        try {
            const response = await fetch('../payroll/gaa_ai/gaa_payroll_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: 'analyze_employee',
                    employee_id: empId,
                    gross: gross,
                    total_deductions: deductions,
                    payroll_frequency: 'monthly'
                })
            });

            const result = await response.json();

            if (result.success && result.data) {
                _lastData = result.data;
                renderWidget(container, result.data);
            } else {
                // If API fails, we could revert to the original Leave card or show error
                renderError(container, result.message || 'Analysis unavailable.');
            }
        } catch (error) {
            console.error('[GAAEmployee] Error:', error);
            renderError(container, 'Intelligence server offline.');
        }
    };

    /**
     * Custom dashboard rendering logic for GAA widget.
     * Focuses on AI Score and Net Pay, removes Gross/Deductions,
     * and optimizes space for the employee view.
     */
    const renderWidget = (container, data) => {
        container.innerHTML = '';

        const cls = data.classification || {};
        const fin = data.financials || {};
        const risk = data.risk_profile || {};
        const color = cls.color || '#6B7280';
        const status = (cls.status || 'UNKNOWN').toLowerCase();

        // Helper for peso formatting (internal to our module)
        const formatPeso = (v) => '₱' + parseFloat(v).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        const html = `
            <div class="card h-100 gaa-dashboard-widget" 
                 style="border: 1px solid rgba(0,0,0,.125); border-left: 5px solid ${color}; box-shadow: none; margin: 0;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    
                    <!-- Header: Status Badge & AI Score -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                             <span class="gaa-badge gaa-badge--${status}"
                                  style="background:${color}; color:#fff; padding:3px 10px; border-radius:12px;
                                         font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:5px;">
                                <span>${cls.icon || ''}</span>
                                <span>${cls.label || status.toUpperCase()}</span>
                             </span>
                        </div>
                        <div class="text-right">
                            <div style="font-size:0.65rem; color:#6B7280; text-transform: uppercase; font-weight:bold;">Risk Score</div>
                            <div style="font-size:1.4rem; font-weight:800; color:${risk.risk_color || '#374151'}; line-height: 1;">
                                ${risk.risk_score || '–'}<span style="font-size:0.75rem;">/100</span>
                            </div>
                        </div>
                    </div>

                    <!-- Highlight: Whole Month Net -->
                    <div class="bg-light rounded p-2 text-center mb-2">
                        <div style="font-size:0.65rem; color:#9CA3AF;">Whole Month Net</div>
                        <div style="font-size:1.50rem; font-weight:800; color:${color};">
                            ${formatPeso(fin.net_pay || 0)}
                        </div>
                    </div>

                    <!-- Analysis: Smaller Font -->
                    <div class="analysis-box">
                        <p class="mb-0 text-muted" style="font-size:0.65rem; line-height:1.4; font-style: italic;">
                            ${cls.description || ''}
                        </p>
                    </div>

                </div>
            </div>
        `;

        container.innerHTML = html;

        // Final layout adjustment: Trigger any Tooltips if AdminLTE provides them
        if (window.$ && window.$.fn && window.$.fn.tooltip) {
            $(container).find('[data-toggle="tooltip"]').tooltip();
        }
    };

    const renderError = (container, message) => {
        container.innerHTML = `
            <div class="card card-outline card-danger h-100">
                <div class="card-body text-center py-3">
                    <i class="fas fa-exclamation-circle text-danger mb-2"></i>
                    <p class="mb-0 text-muted" style="font-size:0.7rem;">${message}</p>
                    <button class="btn btn-xs btn-default mt-2" onclick="location.reload()">Retry</button>
                </div>
            </div>
        `;
    };

    return {
        loadRiskStatus
    };

})();
