/**
 * gaa_netpay.js  —  GAA Net Pay Status Monitor Client Logic
 * ─────────────────────────────────────────────────────────────
 *  Handles two modes:
 *    'master'   → Payroll Master: department-level status table
 *    'employee' → Employee: personal status card
 *
 *  Handler: /Munsoft/payroll/gaa_netpay_handler.php
 */

'use strict';

const GAAStatus = (() => {

    // ── Config ────────────────────────────────────────────────
    const HANDLER_URL = 'gaa_netpay_handler.php';

    // Tier display metadata
    const TIER_META = {
        CRITICAL: { label: 'CRITICAL', icon: '🔴', cls: 'gaa-status-critical', pill: 'danger' },
        DANGER: { label: 'DANGER', icon: '🟠', cls: 'gaa-status-danger', pill: 'warning' },
        WARNING: { label: 'WARNING', icon: '🟡', cls: 'gaa-status-warning', pill: 'warning' },
        CAUTION: { label: 'CAUTION', icon: '🟤', cls: 'gaa-status-caution', pill: 'secondary' },
        STABLE: { label: 'STABLE', icon: '🔵', cls: 'gaa-status-stable', pill: 'primary' },
        SAFE: { label: 'SAFE', icon: '🟢', cls: 'gaa-status-safe', pill: 'success' },
    };

    // ── Helpers ───────────────────────────────────────────────

    function money(v) {
        return '₱' + parseFloat(v || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function badge(status, size = '') {
        const m = TIER_META[status] || { label: status || '—', icon: '⚪', cls: '', pill: 'secondary' };
        const sz = size === 'lg' ? 'gaa-badge-lg' : '';
        return `<span class="gaa-badge ${m.cls} ${sz}" title="${m.label}">${m.icon} ${m.label}</span>`;
    }

    function trendIcon(trend) {
        if (trend === 'IMPROVED') return '<span class="gaa-trend-up"   title="Improved">&#8593; Improved</span>';
        if (trend === 'WORSENED') return '<span class="gaa-trend-down" title="Worsened">&#8595; Worsened</span>';
        return '<span class="gaa-trend-same" title="No change">&#8212; No Change</span>';
    }

    function trendArrow(trend) {
        if (trend === 'IMPROVED') return '<i class="fas fa-arrow-up fa-2x gaa-trend-up"></i>';
        if (trend === 'WORSENED') return '<i class="fas fa-arrow-down fa-2x gaa-trend-down"></i>';
        return '<i class="fas fa-arrow-right fa-2x text-muted"></i>';
    }

    function post(action, payload) {
        return $.ajax({
            url: HANDLER_URL,
            method: 'POST',
            data: Object.assign({ action }, payload),
            dataType: 'json'
        });
    }

    // ── Master Mode ───────────────────────────────────────────

    function fetchDepartment() {
        const deptId = $('#gaa-dept').val();
        const empType = $('#gaa-emp-type').val();

        if (!deptId) {
            toastr.warning('Please select a department first.', 'Filter Required');
            return;
        }

        // Show loading states
        $('#gaa-results-card').show();
        $('#gaa-table-loading').show();
        $('#gaa-table-wrap').hide();
        $('#gaa-table-empty').hide();
        $('#gaa-stats-row').hide();
        $('#gaa-period-context').hide();
        $('#btn-fetch-status').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Computing...');

        post('fetch_department_status', { department_id: deptId, employment_type: empType })
            .then(res => {
                if (!res.success) {
                    toastr.error(res.message || 'Failed to fetch statuses.', 'Error');
                    return;
                }
                renderMasterTable(res.data);
                renderSummaryStats(res.summary);
                renderPeriodContext(res.period_context);
            })
            .catch(err => {
                console.error('GAA fetch error:', err);
                const msg = err.responseJSON ? err.responseJSON.message : 'Server error. Please try again.';
                toastr.error(msg, 'Error');

                // Reset UI on error
                $('#gaa-table-loading').hide();
                $('#gaa-table-empty').show();
                $('#gaa-results-count').hide();
            })
            .always(() => {
                $('#btn-fetch-status').prop('disabled', false).html('<i class="fas fa-sync-alt mr-1"></i> Fetch Net Pay Status');
            });
    }

    function renderMasterTable(employees) {
        $('#gaa-table-loading').hide();

        // Destroy existing DataTables instance before DOM manipulation
        if ($.fn.DataTable.isDataTable('#gaaStatusTable')) {
            $('#gaaStatusTable').DataTable().destroy();
        }

        const $tbody = $('#gaa-table-body').empty();

        if (!employees || employees.length === 0) {
            $('#gaa-table-wrap').hide();
            $('#gaa-table-empty').show();
            return;
        }

        employees.forEach((emp, idx) => {
            const row = `
            <tr data-employee-id="${emp.employee_id}" class="gaa-master-row">
                <td class="text-center align-middle">${idx + 1}</td>
                <td class="align-middle"><small class="text-muted">${emp.employee_id_num || '—'}</small></td>
                <td class="align-middle font-weight-bold">${emp.full_name}</td>
                <td class="align-middle"><small>${emp.position || '—'}</small></td>
                <td class="text-right align-middle">${money(emp.gross)}</td>
                <td class="text-right align-middle">${money(emp.total_deductions)}</td>
                <td class="text-right align-middle font-weight-bold">${money(emp.net_pay)}</td>
                <td class="text-center align-middle">
                  ${emp.last_status ? badge(emp.last_status) : '<small class="text-muted">No record</small>'}
                  ${emp.last_monthly_net !== null ? `<br><small class="text-muted" title="Last Period Full Monthly Net: ${money(emp.last_monthly_net)}">Monthly: ${money(emp.last_monthly_net)}</small>` : ''}
                </td>
                <td class="text-center align-middle">
                    ${badge(emp.current_status)}
                    <br><small class="text-muted" style="font-size: 0.7rem;">Monthly: ${money(emp.monthly_net)}</small>
                </td>
                <td class="text-center align-middle">${trendIcon(emp.trend)}</td>
            </tr>`;
            $tbody.append(row);
        });

        $('#gaa-table-wrap').show();
        $('#gaa-results-count').text(employees.length + ' employee(s)').show();
        $('#btn-export-csv').show();

        // Init DataTable
        $('#gaaStatusTable').DataTable({
            responsive: true,
            lengthChange: false,
            pageLength: 25,
            order: [[8, 'asc']], // Sort by Current Status ascending (most critical first)
            language: { search: 'Search employee:' }
        });
    }

    function renderSummaryStats(summary) {
        if (!summary) return;
        $('#stat-critical').text(summary.CRITICAL || 0);
        $('#stat-danger').text(summary.DANGER || 0);
        $('#stat-warning').text(summary.WARNING || 0);
        $('#stat-caution').text(summary.CAUTION || 0);
        $('#stat-stable').text(summary.STABLE || 0);
        $('#stat-safe').text(summary.SAFE || 0);
        $('#gaa-stats-row').slideDown(300);
    }

    function renderPeriodContext(ctx) {
        if (!ctx) return;
        $('#gaa-period-label').text(ctx.period_label || 'Current Period');
        $('#gaa-freq-label').text(ctx.frequency || '—');
        $('#gaa-half-label').text(ctx.is_second_half ? '2nd Half (deductions not applied)' : '1st Half (all deductions applied)');
        $('#gaa-period-context').slideDown(200);
    }

    // ── Export CSV ────────────────────────────────────────────

    function exportCSV() {
        const rows = [['#', 'EIDN', 'Employee', 'Position', 'Gross', 'Deductions', 'Net Pay', 'Last Period Status', 'Current Status', 'Change']];
        $('#gaa-table-body tr').each((i, tr) => {
            const tds = $(tr).find('td');
            rows.push([
                tds.eq(0).text().trim(),
                tds.eq(1).text().trim(),
                tds.eq(2).text().trim(),
                tds.eq(3).text().trim(),
                tds.eq(4).text().trim(),
                tds.eq(5).text().trim(),
                tds.eq(6).text().trim(),
                tds.eq(7).text().trim(),
                tds.eq(8).text().trim(),
                tds.eq(9).text().trim(),
            ]);
        });
        const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'gaa_netpay_status_' + new Date().toISOString().slice(0, 10) + '.csv';
        a.click();
        URL.revokeObjectURL(url);
    }

    // ── Employee Mode ─────────────────────────────────────────

    function fetchEmployee(employeeId) {
        if (!employeeId) {
            $('#emp-status-loading').hide();
            $('#emp-no-data').show();
            return;
        }

        post('fetch_employee_status', { employee_id: employeeId })
            .then(res => {
                $('#emp-status-loading').hide();
                if (!res.success || !res.data) {
                    $('#emp-no-data').show();
                    return;
                }
                renderEmployeePanel(res.data);
            })
            .catch(() => {
                $('#emp-status-loading').hide();
                $('#emp-no-data').show();
            });
    }

    function renderEmployeePanel(data) {
        const meta = TIER_META[data.current_status] || TIER_META['SAFE'];

        // Main card
        const $card = $('#emp-main-card');
        $card.addClass('gaa-emp-card-' + data.current_status.toLowerCase());
        $('#emp-status-icon').html(
            `<span style="font-size:3.5rem;line-height:1;">${meta.icon}</span>`
        );
        $('#emp-status-label').html(
            `<span class="gaa-badge ${meta.cls} gaa-badge-lg">${meta.label}</span>`
        );
        $('#emp-net-amount').text(money(data.net_pay));
        $('#emp-period-context').html(
            `Projected for ${data.period_context.period_label} (${data.period_context.frequency}` +
            (data.period_context.is_second_half ? ' — 2nd Half' : ' — 1st Half') + ')<br>' +
            `<span class="text-primary font-weight-bold">Full Monthly Net: ${money(data.monthly_net)}</span>`
        );

        // Status message
        const msg = getEmployeeMessage(data);
        $('#emp-status-message').html(`<div class="alert ${msg.cls} py-2 px-3 mb-0 text-left" role="alert">${msg.text}</div>`);

        // Comparison section
        // Last period
        if (data.last_status) {
            $('#emp-last-badge').html(badge(data.last_status));
            $('#emp-last-net').html(`${money(data.last_net_pay)} <br><small class="text-muted" style="font-size: 0.7rem;">(Monthly Proj: ${money(data.last_monthly_net)})</small>`);
            $('#emp-last-period-label').text(data.last_period_label || '');
        } else {
            $('#emp-last-badge').html('<small class="text-muted">No prior record</small>');
            $('#emp-last-net').text('—');
            $('#emp-last-period-label').text('');
        }

        // Current side
        $('#emp-current-badge').html(badge(data.current_status));
        $('#emp-current-net').html(`${money(data.net_pay)} <br><small class="text-muted" style="font-size: 0.7rem;">(Monthly Proj: ${money(data.last_monthly_net)})</small>`); // Using monthly net for status alignment
        $('#emp-current-period-label').text(data.period_context.period_label || 'Now');

        // Transition arrow
        $('#emp-transition-arrow').html(trendArrow(data.trend));

        // Trend message
        if (data.trend !== 'SAME' && data.last_status) {
            const tMsg = getTrendMessage(data);
            $('#emp-trend-msg')
                .html(tMsg.text)
                .removeClass('alert-success alert-danger alert-secondary')
                .addClass(tMsg.cls)
                .show();
        }

        // Breakdown table
        const $breakdown = $('#emp-breakdown-body').empty();
        if (data.breakdown && data.breakdown.length > 0) {
            data.breakdown.forEach(item => {
                $breakdown.append(`
                <tr>
                    <td>${item.label}</td>
                    <td class="text-right ${item.type === 'deduction' ? 'text-danger' : 'text-success'}">
                        ${item.type === 'deduction' ? '−' : '+'}${money(item.amount)}
                    </td>
                </tr>`);
            });
            $breakdown.append(`
            <tr class="font-weight-bold" style="border-top: 2px solid #dee2e6;">
                <td>Projected Net Pay</td>
                <td class="text-right">${money(data.net_pay)}</td>
            </tr>`);
        } else {
            $breakdown.html('<tr><td colspan="2" class="text-center text-muted py-3">No breakdown data available.</td></tr>');
        }

        $('#emp-status-panel').fadeIn(300);
    }

    function getEmployeeMessage(data) {
        const threshold = 5000;
        const net = parseFloat(data.last_monthly_net || data.net_pay); // Base message on monthly projection
        switch (data.current_status) {
            case 'CRITICAL':
                return { cls: 'alert-danger', text: `<i class="fas fa-exclamation-triangle mr-2"></i><strong>Action Required:</strong> Your projected net pay of ${money(net)} is below the ₱5,000.00 GAA threshold. This may block payroll processing. Please coordinate with your Payroll Officer immediately.` };
            case 'DANGER':
                return { cls: 'alert-warning', text: `<i class="fas fa-exclamation-circle mr-2"></i><strong>Danger Zone:</strong> Your projected net pay is very close to the ₱5,000.00 minimum. A shortfall of ${money(threshold - net)} or more in additional deductions would breach the threshold.` };
            case 'WARNING':
                return { cls: 'alert-warning', text: `<i class="fas fa-bell mr-2"></i><strong>Warning:</strong> Your projected net pay has a limited buffer above ₱5,000.00. Monitor carefully if new deductions are being added.` };
            case 'CAUTION':
                return { cls: 'alert-info', text: `<i class="fas fa-info-circle mr-2"></i><strong>Caution:</strong> Your net pay is moderate. Continue monitoring if new deductions are expected this period.` };
            case 'STABLE':
                return { cls: 'alert-info', text: `<i class="fas fa-check-circle mr-2"></i><strong>Stable:</strong> Your projected net pay is above the minimum threshold with a comfortable buffer.` };
            default:
                return { cls: 'alert-success', text: `<i class="fas fa-shield-alt mr-2"></i><strong>Safe:</strong> Your projected net pay is well above the ₱5,000.00 GAA threshold.` };
        }
    }

    function getTrendMessage(data) {
        if (data.trend === 'IMPROVED') {
            return {
                cls: 'alert alert-success mb-0',
                text: `<i class="fas fa-arrow-up mr-1"></i> Your net pay improved from <strong>${badge(data.last_status)}</strong> to <strong>${badge(data.current_status)}</strong> compared to the last payroll period.`
            };
        }
        return {
            cls: 'alert alert-danger mb-0',
            text: `<i class="fas fa-arrow-down mr-1"></i> Your net pay status worsened from <strong>${badge(data.last_status)}</strong> to <strong>${badge(data.current_status)}</strong>. This may be due to new deductions added since the last payroll period.`
        };
    }

    // ── Public API ─────────────────────────────────────────────
    return { fetchDepartment, fetchEmployee, exportCSV };

})();
