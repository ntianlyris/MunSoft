/**
 * gaa_payroll.js
 * ─────────────────────────────────────────────────────────────
 *  Location : Munsoft/payroll/js/
 *
 *  PURPOSE:
 *  The page-specific JS glue layer for the payroll computation
 *  screen. Bridges the existing AJAX-rendered payroll table with
 *  the GAA Net Pay Intelligence module.
 *
 *  DEPENDENCIES (must be loaded before this file):
 *    gaa.core.js     → GAACore  (AJAX engine)
 *    gaa.ui.js       → GAAui   (DOM renderers)
 *    gaa.handlers.js → GAAHandlers (event bindings)
 *
 *  ─── HOW TO INTEGRATE WITH YOUR EXISTING PAYROLL JS ────────
 *
 *  After your existing AJAX call renders the payroll table,
 *  call one of these:
 *
 *  Option A — Auto-detect (recommended):
 *    GAAPayroll.watchTable('#your-table-container');
 *    // MutationObserver fires automatically when rows appear.
 *
 *  Option B — Manual call from your AJAX success callback:
 *    // Inside your existing success: function(response) { ... }
 *    GAAPayroll.scanTable();
 *
 *  Option C — Register a hook that fires on every compute:
 *    GAAPayroll.onComputeComplete(function(employees) {
 *        // 'employees' is the array GAAPayroll extracted from the table
 *    });
 *
 *  ─── CONFIGURATION ──────────────────────────────────────────
 *
 *  Call GAAPayroll.init(config) to match your table structure.
 *  Defaults work for a standard payroll table where:
 *    • Each TR has data-employee-id="N"
 *    • Columns: name(0), id_num(1), position(2), gross(3),
 *               deductions(4), net(5)
 *
 *  Example:
 *    GAAPayroll.init({
 *        tableSelector:      '#payroll-result-table',
 *        rowSelector:        'tbody tr[data-employee-id]',
 *        employeeIdAttr:     'data-employee-id',
 *        grossColIndex:      3,
 *        deductionsColIndex: 4,
 *        netColIndex:        5,
 *        nameColIndex:       0,
 *        saveButtonSelector: '#btn-save-payroll',
 *        handlerUrl:         '/Munsoft/payroll/gaa_payroll_handler.php',
 *        frequency:          'monthly',   // or read from page
 *    });
 *    GAAPayroll.watchTable('#payroll-table-container');
 * ─────────────────────────────────────────────────────────────
 */

const GAAPayroll = (() => {

    // ═══════════════════════════════════════════════════════════
    //  MODULE STATE
    // ═══════════════════════════════════════════════════════════

    let _cfg = {
        // ── Table selectors ────────────────────────────────────
        tableSelector:        '#payroll-result-table',
        rowSelector:          'tbody tr[data-employee-id]',
        employeeIdAttr:       'data-employee-id',
        employeeNameAttr:     'data-employee-name',   // optional fallback

        // ── Column indices (0-based, in <td> order) ───────────
        nameColIndex:         0,
        idNumColIndex:        1,
        grossColIndex:        3,
        deductionsColIndex:   4,
        netColIndex:          5,

        // ── GAA badge injection ───────────────────────────────
        // The badge column is inserted AFTER the net pay column.
        // If your table already has a GAA column, set its index here.
        badgeColIndex:        null,   // null = auto-append new column

        // ── Button selectors ──────────────────────────────────
        saveButtonSelector:   '#btn-save-payroll',
        computeButtonSelector: '#btn-compute-payroll',

        // ── Handler URL ───────────────────────────────────────
        handlerUrl:           '/Munsoft/payroll/gaa_payroll_handler.php',

        // ── Payroll frequency ─────────────────────────────────
        // 'monthly' or 'semi-monthly'. Read from page if possible.
        frequency:            'monthly',
        frequencySelector:    null,   // e.g. '#frequency-display'

        // ── Behavior flags ────────────────────────────────────
        blockSaveOnCritical:  true,   // Hard block if CRITICAL exists
        warnOnDanger:         true,   // Soft warn dialog if DANGER exists
        autoInjectHeaderCol:  true,   // Add "GAA Status" th to table header
        colHeaderLabel:       'GAA Status',
        showAtRiskInWidget:   true,
        maxAtRiskDisplayed:   8,
    };

    // Cached last scan results
    let _lastResults     = null;
    let _lastSummary     = null;
    let _pendingSaveCallback = null;
    let _observer        = null;
    let _computeHooks    = [];
    let _widgetCollapsed = false;

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC: INIT
    // ═══════════════════════════════════════════════════════════

    /**
     * Initialize GAAPayroll with custom configuration.
     * Call this once after the page loads, before any table renders.
     *
     * @param {Object} userConfig  Overrides for _cfg
     */
    const init = (userConfig = {}) => {
        Object.assign(_cfg, userConfig);

        // Read frequency from DOM if selector provided
        if (_cfg.frequencySelector) {
            const freqEl = document.querySelector(_cfg.frequencySelector);
            if (freqEl) {
                _cfg.frequency = (freqEl.textContent || freqEl.value || '').toLowerCase().trim()
                    || _cfg.frequency;
            }
        }

        // Bind to save button immediately if it exists
        _bindSaveGuard();

        // Set the endpoint meta tag for GAACore
        _ensureEndpointMeta();

        console.log('[GAAPayroll] Initialized.', {
            table: _cfg.tableSelector,
            frequency: _cfg.frequency,
            handler: _cfg.handlerUrl,
        });
    };

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC: WATCH TABLE (MutationObserver)
    //
    //  Observes a container element for new TR children.
    //  Fires scanTable() automatically when rows appear.
    //  Perfect for AJAX-rendered tables.
    // ═══════════════════════════════════════════════════════════

    /**
     * Watch a container for AJAX-rendered table rows.
     *
     * @param {string} containerSelector  The wrapper that holds the table
     *                                    (not the table itself — its parent)
     */
    const watchTable = (containerSelector) => {
        const container = document.querySelector(containerSelector);
        if (!container) {
            console.warn('[GAAPayroll] watchTable: container not found:', containerSelector);
            return;
        }

        // Disconnect any existing observer
        if (_observer) _observer.disconnect();

        let _debounceTimer = null;

        _observer = new MutationObserver((mutations) => {
            let hasNewRows = false;

            for (const mutation of mutations) {
                if (mutation.type !== 'childList') continue;
                for (const node of mutation.addedNodes) {
                    if (node.nodeType !== Node.ELEMENT_NODE) continue;
                    // Check if a TABLE, TBODY, or TR was added
                    if (
                        node.tagName === 'TABLE' ||
                        node.tagName === 'TBODY' ||
                        node.tagName === 'TR' ||
                        node.querySelector?.('tr[data-employee-id], tbody tr')
                    ) {
                        hasNewRows = true;
                        break;
                    }
                }
                if (hasNewRows) break;
            }

            if (hasNewRows) {
                // Debounce: wait for all rows to render before scanning
                clearTimeout(_debounceTimer);
                _debounceTimer = setTimeout(() => {
                    console.log('[GAAPayroll] Table mutation detected — scanning…');
                    scanTable();
                }, 300);
            }
        });

        _observer.observe(container, { childList: true, subtree: true });
        console.log('[GAAPayroll] Watching container:', containerSelector);
    };

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC: SCAN TABLE
    //
    //  Reads all employee rows from the rendered table,
    //  sends to gaa_payroll_handler.php classify_batch,
    //  injects GAA badges into each row,
    //  and updates the widget panel.
    // ═══════════════════════════════════════════════════════════

    /**
     * Scan the payroll table and inject GAA status badges.
     * Call this manually from your existing AJAX success callback.
     *
     * @param {Object} [overrideData]  Optional pre-parsed employee array
     *                                 (skip if you want auto-extraction)
     */
    const scanTable = async (overrideData = null) => {
        const employees = overrideData ?? _extractEmployeesFromTable();

        if (!employees || employees.length === 0) {
            console.warn('[GAAPayroll] scanTable: no employee rows found.');
            return;
        }

        // Show scanning state
        _setWidgetScanning(true);

        // ── Call the handler ────────────────────────────────────
        let response;
        try {
            response = await _post('classify_batch', {
                employees,
                payroll_frequency: _cfg.frequency,
            });
        } catch (e) {
            console.error('[GAAPayroll] classify_batch error:', e);
            _setWidgetScanning(false);
            return;
        }

        if (!response.success || !response.data) {
            console.error('[GAAPayroll] classify_batch failed:', response.message);
            _setWidgetScanning(false);
            return;
        }

        _lastResults = response.data.results;
        _lastSummary = response.data.summary;

        // ── Inject badges into table ────────────────────────────
        _injectBadgesIntoTable(response.data.results, employees);

        // ── Update widget panel ─────────────────────────────────
        _updateWidget(response.data);

        _setWidgetScanning(false);

        // Fire compute hooks
        _computeHooks.forEach(fn => {
            try { fn(employees, response.data); } catch(e) {}
        });

        console.log('[GAAPayroll] Scan complete.', response.data.summary);
    };

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: EXTRACT EMPLOYEES FROM TABLE
    // ═══════════════════════════════════════════════════════════

    const _extractEmployeesFromTable = () => {
        const rows = document.querySelectorAll(_cfg.rowSelector);
        if (!rows.length) return [];

        const employees = [];

        rows.forEach(row => {
            const empId = parseInt(row.getAttribute(_cfg.employeeIdAttr));
            if (!empId) return;

            const cells = row.querySelectorAll('td');

            const _cellText = (idx) => {
                const td = cells[idx];
                if (!td) return '0';
                // Strip currency symbols, commas, and whitespace
                return td.textContent.replace(/[₱,\s]/g, '').trim();
            };

            const gross      = parseFloat(_cellText(_cfg.grossColIndex))      || 0;
            const deductions = parseFloat(_cellText(_cfg.deductionsColIndex)) || 0;
            const name       = cells[_cfg.nameColIndex]?.textContent?.trim()
                               ?? row.getAttribute(_cfg.employeeNameAttr)
                               ?? `Employee ${empId}`;

            employees.push({
                employee_id:      empId,
                name:             name,
                gross:            gross,
                total_deductions: deductions,
            });
        });

        return employees;
    };

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: INJECT BADGES INTO TABLE ROWS
    // ═══════════════════════════════════════════════════════════

    const _injectBadgesIntoTable = (results, employees) => {
        const table = document.querySelector(_cfg.tableSelector);
        if (!table) return;

        // ── Add header column (once) ──────────────────────────
        if (_cfg.autoInjectHeaderCol) {
            const thead = table.querySelector('thead tr');
            if (thead && !thead.querySelector('.gaa-th')) {
                const th = document.createElement('th');
                th.className = 'gaa-th';
                th.style.cssText = 'white-space:nowrap; font-size:.75rem; color:#6B7280; padding:8px 10px;';
                th.textContent = _cfg.colHeaderLabel;
                thead.appendChild(th);
            }
        }

        // ── Inject badge per employee row ──────────────────────
        document.querySelectorAll(_cfg.rowSelector).forEach(row => {
            const empId = parseInt(row.getAttribute(_cfg.employeeIdAttr));
            if (!empId || !results[empId]) return;

            const result = results[empId];

            // Remove existing badge TD if present (re-scan guard)
            const existing = row.querySelector('.gaa-table-badge-td');
            if (existing) existing.remove();

            const td = document.createElement('td');
            td.className = 'gaa-table-badge-td';
            td.style.cssText = 'padding:6px 10px; white-space:nowrap;';

            // Build badge HTML
            td.innerHTML = `
              <span class="gaa-badge gaa-badge--${result.status.toLowerCase()} gaa-table-badge"
                    style="cursor:pointer;"
                    onclick="GAAPayroll.openDetailPanel(${empId}, ${result.gross}, ${result.total_deductions})"
                    title="Click for AI analysis — Net: ₱${result.net_pay.toFixed(2)}">
                ${result.icon} ${result.status}
              </span>`;

            // Append or insert at configured column
            if (_cfg.badgeColIndex !== null && row.cells[_cfg.badgeColIndex]) {
                row.cells[_cfg.badgeColIndex].replaceWith(td);
            } else {
                row.appendChild(td);
            }

            // Highlight CRITICAL rows
            if (result.status === 'CRITICAL') {
                row.style.background = '#FFF5F5';
            } else if (result.status === 'DANGER') {
                row.style.background = '#FFF7ED';
            }
        });
    };

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: UPDATE WIDGET PANEL
    // ═══════════════════════════════════════════════════════════

    const _updateWidget = (data) => {
        const s = data.summary ?? {};

        // ── Tier count pills ──────────────────────────────────
        ['critical','danger','warning','caution','stable','safe'].forEach(tier => {
            const countEl = document.getElementById(`gaa-count-${tier}`);
            if (countEl) countEl.textContent = s[tier.toUpperCase()] ?? 0;
        });

        // ── Risk band ─────────────────────────────────────────
        // Compute a simple period score: weighted average of tier positions
        const tierWeights = { CRITICAL:100, DANGER:75, WARNING:55, CAUTION:35, STABLE:15, SAFE:5 };
        const total = Object.values(s).reduce((a, b) => a + b, 0);
        let weightedSum = 0;
        Object.entries(s).forEach(([tier, count]) => {
            weightedSum += (tierWeights[tier] ?? 0) * count;
        });
        const periodScore = total > 0 ? Math.round(weightedSum / total) : 0;

        const riskBand = _getRiskBand(periodScore);
        const labelEl  = document.getElementById('gaa-period-risk-label');
        const barEl    = document.getElementById('gaa-period-risk-bar');
        const scoreEl  = document.getElementById('gaa-period-risk-score');

        if (labelEl) {
            labelEl.textContent = riskBand.label;
            labelEl.style.color = riskBand.color;
        }
        if (barEl) {
            barEl.style.width      = Math.min(100, periodScore) + '%';
            barEl.style.background = riskBand.color;
        }
        if (scoreEl) scoreEl.textContent = `${periodScore}/100`;

        // ── At-risk employees list ─────────────────────────────
        const atRiskContainer = document.getElementById('gaa-at-risk-rows');
        if (atRiskContainer) {
            const atRisk = Object.values(data.results ?? {})
                .filter(r => ['CRITICAL','DANGER','WARNING'].includes(r.status))
                .sort((a, b) => {
                    const order = { CRITICAL:0, DANGER:1, WARNING:2 };
                    return (order[a.status] ?? 9) - (order[b.status] ?? 9);
                })
                .slice(0, _cfg.maxAtRiskDisplayed);

            if (!atRisk.length) {
                atRiskContainer.innerHTML =
                    '<div class="gaa-at-risk-empty">✅ All employees are above the GAA threshold.</div>';
            } else {
                const statusColors = { CRITICAL:'#DC2626', DANGER:'#EA580C', WARNING:'#D97706' };
                atRiskContainer.innerHTML = atRisk.map(r => {
                    const sc = statusColors[r.status] ?? '#6B7280';
                    const netFmt = '₱' + r.net_pay.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    return `
                      <div class="gaa-at-risk-row"
                           onclick="GAAPayroll.openDetailPanel(${r.employee_id}, ${r.gross}, ${r.total_deductions})"
                           title="Click for AI analysis">
                        <span class="gaa-badge gaa-badge--${r.status.toLowerCase()}" style="font-size:.72rem; padding:2px 8px;">
                          ${r.icon} ${r.status}
                        </span>
                        <span class="gaa-emp-name">Employee #${r.employee_id}</span>
                        <span class="gaa-emp-net" style="color:${sc};">${netFmt}</span>
                        ${r.status === 'CRITICAL'
                            ? `<span style="font-size:.72rem; color:#DC2626;">-₱${r.shortfall ?? Math.abs(r.buffer_amount).toFixed(2)}</span>`
                            : `<span class="gaa-risk-num">+₱${r.buffer_amount.toFixed(2)}</span>`
                        }
                      </div>`;
                }).join('');
            }
        }
    };

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC: SAVE GUARD
    //
    //  Called automatically when the Save Payroll button is clicked.
    //  Sends current employee data to pre_save_validate.
    //  Blocks or warns based on GAA status.
    // ═══════════════════════════════════════════════════════════

    /**
     * Intercept the Save Payroll button and run GAA pre-validation.
     * This replaces the button's default action until validation passes.
     */
    const _bindSaveGuard = () => {
        const btn = document.querySelector(_cfg.saveButtonSelector);
        if (!btn) return;

        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();

            await runPreSaveValidation(() => {
                // Validation passed — re-trigger the original save
                // Remove guard temporarily, click, re-add guard
                btn.removeEventListener('click', arguments.callee);
                btn.click();
                _bindSaveGuard();
            });
        }, true); // useCapture ensures this fires first
    };

    /**
     * Run pre-save GAA validation.
     * @param {Function} onPass  Callback fired if validation passes
     */
    const runPreSaveValidation = async (onPass) => {
        const employees = _extractEmployeesFromTable();
        if (!employees.length) {
            if (typeof onPass === 'function') onPass();
            return;
        }

        // Show loading on save button
        const saveBtn = document.querySelector(_cfg.saveButtonSelector);
        const origLabel = saveBtn?.innerHTML;
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '⏳ Validating GAA…';
        }

        let response;
        try {
            response = await _post('pre_save_validate', {
                employees,
                payroll_frequency: _cfg.frequency,
                period_label: document.querySelector('#period-label, .period-label')
                              ?.textContent?.trim() ?? '',
            });
        } catch(e) {
            // Network error — allow save to proceed with console warning
            console.error('[GAAPayroll] pre_save_validate error:', e);
            if (saveBtn) { saveBtn.disabled = false; saveBtn.innerHTML = origLabel; }
            if (typeof onPass === 'function') onPass();
            return;
        }

        if (saveBtn) { saveBtn.disabled = false; saveBtn.innerHTML = origLabel; }

        const data = response.data ?? {};

        // ── Hard block: CRITICAL violations exist ─────────────
        if (!data.can_save && data.violation_count > 0) {
            _showSaveModal(data, false, onPass);
            return;
        }

        // ── Soft warn: DANGER/WARNING employees ───────────────
        if (_cfg.warnOnDanger && data.warning_count > 0) {
            _showSaveModal(data, true, onPass);
            return;
        }

        // ── All clear ─────────────────────────────────────────
        if (typeof onPass === 'function') onPass();
    };

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: SAVE GUARD MODAL
    // ═══════════════════════════════════════════════════════════

    let _savedOnPassCallback = null;

    const _showSaveModal = (data, isSoftWarning, onPass) => {
        _savedOnPassCallback = onPass;

        const modal       = document.getElementById('gaa-save-modal');
        const violations  = document.getElementById('gaa-modal-violations');
        const warningsDiv = document.getElementById('gaa-modal-warnings');
        const closeBtn    = document.getElementById('gaa-modal-close-btn');
        const proceedBtn  = document.getElementById('gaa-modal-proceed-btn');
        const titleEl     = document.getElementById('gaa-modal-title');

        if (!modal) return;

        if (isSoftWarning) {
            // Soft warning — can proceed
            titleEl.innerHTML = '⚠️ GAA Threshold Warning';
            titleEl.style.color = '#D97706';
            closeBtn.textContent = 'Cancel';
            if (proceedBtn) proceedBtn.style.display = 'inline-block';
            violations.innerHTML = '';
            if (warningsDiv) {
                warningsDiv.style.display = 'block';
                warningsDiv.innerHTML = `
                  <p style="font-size:.83rem; color:#92400E; margin:0 0 8px;">
                    The following employee(s) are near the GAA threshold:
                  </p>
                  ${(data.warnings ?? []).map(w => `
                    <div style="padding:8px 10px; background:#FFFBEB; border:1px solid #FDE68A;
                                border-radius:6px; margin-bottom:6px; font-size:.82rem;">
                      <strong>${_esc(w.employee_name)}</strong>
                      — Net: ₱${w.net_pay.toFixed(2)}
                      <span class="gaa-badge gaa-badge--${w.status.toLowerCase()}"
                            style="font-size:.68rem; padding:1px 7px; margin-left:6px;">
                        ${w.status}
                      </span>
                      <br><small style="color:#92400E;">Buffer: ₱${w.buffer.toFixed(2)} above threshold</small>
                    </div>
                  `).join('')}`;
            }
        } else {
            // Hard block — cannot proceed
            titleEl.innerHTML = '🚫 Cannot Save — GAA Threshold Violations';
            titleEl.style.color = '#DC2626';
            closeBtn.textContent = 'Review Deductions';
            if (proceedBtn) proceedBtn.style.display = 'none';
            if (warningsDiv) warningsDiv.style.display = 'none';
            violations.innerHTML = `
              <p style="font-size:.83rem; color:#991B1B; margin:0 0 8px;">
                ${data.violation_count} employee(s) must have deductions reduced before saving:
              </p>
              ${(data.violations ?? []).map(v => `
                <div style="padding:10px 12px; background:#FEF2F2; border:1px solid #FECACA;
                            border-radius:6px; margin-bottom:7px; font-size:.83rem;">
                  <div style="display:flex; justify-content:space-between; align-items:center;">
                    <strong style="color:#991B1B;">${_esc(v.employee_name)}</strong>
                    <span class="gaa-badge gaa-badge--critical" style="font-size:.68rem; padding:1px 7px;">
                      CRITICAL
                    </span>
                  </div>
                  <div style="margin-top:4px; color:#374151;">
                    Net Pay: <strong style="color:#DC2626;">₱${v.net_pay.toFixed(2)}</strong>
                    &nbsp;|&nbsp;
                    Shortfall: <strong style="color:#DC2626;">₱${v.shortfall.toFixed(2)}</strong>
                  </div>
                  <div style="font-size:.76rem; color:#9CA3AF; margin-top:2px;">
                    Gross: ₱${v.gross.toFixed(2)} | Deductions: ₱${v.total_deductions.toFixed(2)}
                  </div>
                </div>
              `).join('')}`;
        }

        modal.classList.add('gaa-modal-open');
    };

    const closeSaveModal = () => {
        const modal = document.getElementById('gaa-save-modal');
        if (modal) modal.classList.remove('gaa-modal-open');
        _savedOnPassCallback = null;
    };

    const proceedSave = () => {
        closeSaveModal();
        if (typeof _savedOnPassCallback === 'function') {
            _savedOnPassCallback();
        }
    };

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC: EMPLOYEE DETAIL PANEL
    // ═══════════════════════════════════════════════════════════

    /**
     * Open the slide-in AI detail panel for an employee.
     * Fetches the full intelligence profile with trend & recommendations.
     *
     * @param {number} employeeId
     * @param {number} gross
     * @param {number} totalDeductions
     */
    const openDetailPanel = async (employeeId, gross, totalDeductions) => {
        const panel   = document.getElementById('gaa-employee-detail');
        const overlay = document.getElementById('gaa-detail-overlay');
        const nameEl  = document.getElementById('gaa-detail-emp-name');
        const cardEl  = document.getElementById('gaa-detail-card');
        const meterEl = document.getElementById('gaa-detail-meter');
        const sparkEl = document.getElementById('gaa-detail-sparkline');
        const recsEl  = document.getElementById('gaa-detail-recs');

        if (!panel) return;

        // Open panel
        panel.classList.add('gaa-panel-open');
        if (overlay) overlay.style.display = 'block';

        // Show loading
        if (nameEl) nameEl.textContent = `Employee #${employeeId}`;
        if (cardEl) cardEl.innerHTML = '<div class="gaa-scanning"><span class="gaa-spinner"></span> Loading AI analysis…</div>';
        if (meterEl) meterEl.innerHTML = '';
        if (sparkEl) sparkEl.innerHTML = '';
        if (recsEl)  recsEl.innerHTML  = '';

        // Fetch full profile
        let response;
        try {
            response = await _post('analyze_employee', {
                employee_id:      employeeId,
                gross:            gross,
                total_deductions: totalDeductions,
                payroll_frequency: _cfg.frequency,
            });
        } catch(e) {
            if (cardEl) cardEl.innerHTML = `<div style="color:#DC2626; font-size:.84rem;">⚠ Failed to load analysis: ${e.message}</div>`;
            return;
        }

        if (!response.success) {
            if (cardEl) cardEl.innerHTML = `<div style="color:#DC2626; font-size:.84rem;">⚠ ${response.message}</div>`;
            return;
        }

        const profile = response.data;
        const empName = profile?.financials ? `Employee #${employeeId}` : `Employee #${employeeId}`;

        if (nameEl) nameEl.textContent = empName;

        // Render all UI components via GAAui
        if (typeof GAAui !== 'undefined') {
            GAAui.renderStatusCard(cardEl,        response);
            GAAui.renderRiskMeter(meterEl,        response);
            GAAui.renderTrendSparkline(sparkEl,   response);
            GAAui.renderRecommendations(recsEl,   response);
        }
    };

    const closeDetailPanel = () => {
        const panel   = document.getElementById('gaa-employee-detail');
        const overlay = document.getElementById('gaa-detail-overlay');
        if (panel)   panel.classList.remove('gaa-panel-open');
        if (overlay) overlay.style.display = 'none';
    };

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC: WIDGET TOGGLE
    // ═══════════════════════════════════════════════════════════

    const toggleWidget = () => {
        const body = document.getElementById('gaa-widget-body');
        const icon = document.getElementById('gaa-toggle-icon');
        if (!body) return;
        _widgetCollapsed = !_widgetCollapsed;
        body.classList.toggle('gaa-collapsed', _widgetCollapsed);
        if (icon) icon.classList.toggle('gaa-rotated', _widgetCollapsed);
    };

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC: REGISTER COMPUTE HOOK
    // ═══════════════════════════════════════════════════════════

    /**
     * Register a callback fired after every successful table scan.
     * @param {Function} fn  Called with (employees, gaaData)
     */
    const onComputeComplete = (fn) => {
        if (typeof fn === 'function') _computeHooks.push(fn);
    };

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: HTTP POST to payroll handler
    // ═══════════════════════════════════════════════════════════

    const _post = async (action, payload = {}) => {
        const body = JSON.stringify({ action, ...payload });
        const resp = await fetch(_cfg.handlerUrl, {
            method:  'POST',
            headers: {
                'Content-Type':    'application/json',
                'X-Requested-With':'XMLHttpRequest',
            },
            body,
            credentials: 'same-origin',
        });
        return resp.json();
    };

    // ═══════════════════════════════════════════════════════════
    //  PRIVATE: HELPERS
    // ═══════════════════════════════════════════════════════════

    const _setWidgetScanning = (active) => {
        const el = document.getElementById('gaa-widget-scanning');
        if (el) el.style.display = active ? 'inline-flex' : 'none';
    };

    const _ensureEndpointMeta = () => {
        if (!document.querySelector('meta[name="gaa-api-endpoint"]')) {
            const meta = document.createElement('meta');
            meta.name    = 'gaa-api-endpoint';
            meta.content = _cfg.handlerUrl;
            document.head.appendChild(meta);
        }
    };

    const _getRiskBand = (score) => {
        if (score >= 80) return { label: 'Critical Risk',  color: '#DC2626' };
        if (score >= 60) return { label: 'High Risk',      color: '#EA580C' };
        if (score >= 40) return { label: 'Moderate Risk',  color: '#D97706' };
        if (score >= 20) return { label: 'Low Risk',       color: '#2563EB' };
        return              { label: 'Minimal Risk',   color: '#16A34A' };
    };

    const _esc = (str) => String(str ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

    // ═══════════════════════════════════════════════════════════
    //  PUBLIC SURFACE
    // ═══════════════════════════════════════════════════════════
    return {
        init,
        watchTable,
        scanTable,
        runPreSaveValidation,
        openDetailPanel,
        closeDetailPanel,
        closeSaveModal,
        proceedSave,
        toggleWidget,
        onComputeComplete,

        // Expose for debugging / external access
        getLastResults:  () => _lastResults,
        getLastSummary:  () => _lastSummary,
    };

})();

// ── Auto-init with sensible defaults on DOM ready ─────────────
// Override by calling GAAPayroll.init({...}) in your payroll page.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => GAAPayroll.init());
} else {
    GAAPayroll.init();
}

window.GAAPayroll = GAAPayroll;
