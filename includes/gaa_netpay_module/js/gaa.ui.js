/**
 * gaa.ui.js
 * ─────────────────────────────────────────────────────────
 *  GAA Net Pay Intelligence Module — UI Renderer
 *
 *  Depends on: gaa.core.js (must be loaded first)
 *
 *  Provides the GAAui namespace with methods that accept
 *  an intelligence profile object and render it into DOM.
 *
 *  Available renderers:
 *    GAAui.renderBadge(selector, profile)      → inline status badge
 *    GAAui.renderStatusCard(selector, profile) → full status card widget
 *    GAAui.renderRiskMeter(selector, profile)  → animated risk score meter
 *    GAAui.renderRecommendations(sel, profile) → actionable recommendations list
 *    GAAui.renderTrendSparkline(sel, profile)  → mini trend chart (SVG)
 *    GAAui.renderBatchTable(sel, batchData)    → at-risk employees table
 *    GAAui.renderDeptHeatmap(sel, batchData)   → department risk heat map
 *    GAAui.renderHeadroomBar(sel, headroomData)→ deduction headroom progress bar
 *
 *  All render methods also accept a raw API response envelope
 *  (with .data property) or a naked data object directly.
 * ─────────────────────────────────────────────────────────
 */

const GAAui = (() => {

    // ── Utility: extract data from API envelope or raw object ─
    const _unwrap = (res) => (res && res.data) ? res.data : res;

    // ── Utility: resolve selector to DOM element ──────────
    const _el = (selector) =>
        typeof selector === 'string' ? document.querySelector(selector) : selector;

    // ── Utility: escape HTML to prevent XSS ──────────────
    const _esc = (str) => String(str ?? '')
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');

    // ── Utility: format peso ──────────────────────────────
    const _peso = (v) => '₱' + parseFloat(v).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // ─────────────────────────────────────────────────────
    //  1. STATUS BADGE  (inline, one-liner)
    // ─────────────────────────────────────────────────────
    /**
     * Render a compact inline status badge into a container.
     * Accepts either a classify() response or a full analyzeEmployee() response.
     *
     * @param {string|Element} selector
     * @param {Object} res   API response or data object
     */
    const renderBadge = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const data = _unwrap(res);

        // Support both classify() shape and analyzeEmployee().classification shape
        const cls   = data.classification ?? data;
        const status = cls.status ?? 'UNKNOWN';
        const color  = cls.color  ?? '#6B7280';
        const icon   = cls.icon   ?? '❓';
        const label  = cls.label  ?? status;

        el.innerHTML = `
          <span class="gaa-badge gaa-badge--${status.toLowerCase()}"
                style="background:${color}; color:#fff; padding:3px 10px; border-radius:12px;
                       font-size:0.78rem; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
            <span>${icon}</span>
            <span>${_esc(label)}</span>
          </span>`;
    };

    // ─────────────────────────────────────────────────────
    //  2. STATUS CARD  (full informational widget)
    // ─────────────────────────────────────────────────────
    /**
     * Render a full status card showing financial summary + AI status.
     *
     * @param {string|Element} selector
     * @param {Object} res   analyzeEmployee() response
     */
    const renderStatusCard = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const d    = _unwrap(res);
        const cls  = d.classification ?? {};
        const fin  = d.financials ?? {};
        const risk = d.risk_profile ?? {};

        const status = cls.status ?? 'UNKNOWN';
        const color  = cls.color  ?? '#6B7280';

        el.innerHTML = `
          <div class="gaa-card" style="border-left:5px solid ${color}; background:#fff;
               border-radius:8px; padding:18px 20px; box-shadow:0 2px 8px rgba(0,0,0,.1);
               font-family:inherit;">

            <!-- Header -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
              <div>
                <span class="gaa-badge gaa-badge--${status.toLowerCase()}"
                      style="background:${color}; color:#fff; padding:4px 12px;
                             border-radius:12px; font-size:0.8rem; font-weight:700;">
                  ${_esc(cls.icon ?? '')} ${_esc(cls.label ?? status)}
                </span>
              </div>
              <div style="text-align:right;">
                <div style="font-size:0.72rem; color:#6B7280;">AI Risk Score</div>
                <div style="font-size:1.5rem; font-weight:800; color:${risk.risk_color ?? '#374151'};">
                  ${risk.risk_score ?? '–'}<span style="font-size:0.8rem;">/100</span>
                </div>
              </div>
            </div>

            <!-- Financials Grid -->
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:14px;">
              ${_cardStat('Gross Pay', _peso(fin.gross ?? 0), '#374151')}
              ${_cardStat('Total Deductions', _peso(fin.total_deductions ?? 0), '#DC2626')}
              ${_cardStat('Net Pay', _peso(fin.net_pay ?? 0), color)}
            </div>

            <!-- Buffer bar -->
            ${_bufferBar(fin.net_pay ?? 0)}

            <!-- Description -->
            <p style="font-size:0.82rem; color:#6B7280; margin:10px 0 0; line-height:1.5;">
              ${_esc(cls.description ?? '')}
            </p>
          </div>`;
    };

    // Internal card stat cell
    const _cardStat = (label, value, color) => `
      <div style="text-align:center; padding:8px; background:#F9FAFB; border-radius:6px;">
        <div style="font-size:0.7rem; color:#9CA3AF; margin-bottom:2px;">${_esc(label)}</div>
        <div style="font-size:1rem; font-weight:700; color:${color};">${_esc(value)}</div>
      </div>`;

    // Internal threshold proximity bar
    const _bufferBar = (netPay) => {
        const threshold = 5000;
        const pct       = Math.min(100, Math.max(0, ((netPay - threshold) / threshold) * 100));
        const barColor  = pct <= 0 ? '#DC2626' : pct <= 10 ? '#EA580C' : pct <= 30 ? '#D97706' : '#16A34A';
        const barWidth  = Math.min(100, Math.abs(pct));
        return `
          <div style="margin:10px 0;">
            <div style="display:flex; justify-content:space-between; font-size:0.72rem; color:#6B7280; margin-bottom:4px;">
              <span>GAA Threshold ₱5,000</span>
              <span>${pct >= 0 ? '+' : ''}${pct.toFixed(1)}% buffer</span>
            </div>
            <div style="height:8px; background:#E5E7EB; border-radius:4px; overflow:hidden;">
              <div style="height:100%; width:${barWidth}%; background:${barColor};
                          border-radius:4px; transition:width .5s ease;"></div>
            </div>
          </div>`;
    };

    // ─────────────────────────────────────────────────────
    //  3. RISK SCORE METER  (animated arc meter)
    // ─────────────────────────────────────────────────────
    const renderRiskMeter = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const d     = _unwrap(res);
        const rp    = d.risk_profile ?? d;
        const score = parseFloat(rp.risk_score ?? 0);
        const color = rp.risk_color ?? '#6B7280';
        const band  = rp.risk_label ?? rp.risk_band ?? '';

        // SVG arc meter
        const r   = 54;
        const circ = 2 * Math.PI * r;
        const dash  = circ * (score / 100);

        el.innerHTML = `
          <div class="gaa-risk-meter" style="text-align:center; padding:16px;">
            <svg width="140" height="80" viewBox="0 0 140 80">
              <!-- Background track (half-circle) -->
              <path d="M 10 70 A 60 60 0 0 1 130 70"
                    fill="none" stroke="#E5E7EB" stroke-width="14" stroke-linecap="round"/>
              <!-- Score arc -->
              <path d="M 10 70 A 60 60 0 0 1 130 70"
                    fill="none" stroke="${color}" stroke-width="14" stroke-linecap="round"
                    stroke-dasharray="${(circ * score / 100).toFixed(1)} ${circ}"
                    stroke-dashoffset="0"
                    style="transition:stroke-dasharray 1s ease;"
                    class="gaa-arc"/>
              <!-- Score label -->
              <text x="70" y="62" text-anchor="middle"
                    style="font-size:22px; font-weight:800; fill:${color};">${score}</text>
              <text x="70" y="76" text-anchor="middle"
                    style="font-size:9px; fill:#9CA3AF;">out of 100</text>
            </svg>
            <div style="font-size:0.82rem; font-weight:700; color:${color}; margin-top:4px;">
              ${_esc(band)}
            </div>
          </div>`;
    };

    // ─────────────────────────────────────────────────────
    //  4. RECOMMENDATIONS LIST
    // ─────────────────────────────────────────────────────
    const renderRecommendations = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const d    = _unwrap(res);
        const recs = d.risk_profile?.recommendations ?? d.recommendations ?? [];

        const priorityColors = {
            URGENT: '#DC2626', HIGH: '#EA580C', MEDIUM: '#D97706',
            LOW: '#2563EB', NONE: '#16A34A',
        };

        const items = recs.map(r => {
            const color = priorityColors[r.priority] ?? '#6B7280';
            return `
              <li style="display:flex; gap:12px; padding:12px; border-radius:6px;
                          background:#F9FAFB; margin-bottom:8px; list-style:none;">
                <span style="background:${color}; color:#fff; padding:2px 8px; border-radius:10px;
                             font-size:0.68rem; font-weight:700; white-space:nowrap; height:fit-content;">
                  ${_esc(r.priority)}
                </span>
                <div>
                  <div style="font-size:0.84rem; font-weight:600; color:#1F2937;">${_esc(r.action)}</div>
                  <div style="font-size:0.76rem; color:#6B7280; margin-top:3px;">${_esc(r.rationale)}</div>
                </div>
              </li>`;
        }).join('');

        el.innerHTML = `<ul style="margin:0; padding:0;">${items || '<li style="color:#6B7280; font-size:0.85rem;">No recommendations at this time.</li>'}</ul>`;
    };

    // ─────────────────────────────────────────────────────
    //  5. TREND SPARKLINE  (SVG mini chart)
    // ─────────────────────────────────────────────────────
    const renderTrendSparkline = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const d       = _unwrap(res);
        const trend   = d.risk_profile?.trend ?? d.trend ?? {};
        const history = trend.history ?? [];

        if (history.length < 2) {
            el.innerHTML = '<span style="font-size:0.78rem; color:#9CA3AF;">Insufficient data for trend chart.</span>';
            return;
        }

        const W = 200, H = 60, pad = 8;
        const min = Math.min(...history, 5000) - 200;
        const max = Math.max(...history) + 200;
        const xStep = (W - pad * 2) / (history.length - 1);
        const yScale = (v) => H - pad - ((v - min) / (max - min)) * (H - pad * 2);

        const points = history.map((v, i) => `${pad + i * xStep},${yScale(v)}`).join(' ');
        const threshold_y = yScale(5000);

        const dirColor = trend.direction === 'DECLINING' ? '#DC2626'
            : trend.direction === 'IMPROVING' ? '#16A34A' : '#2563EB';

        el.innerHTML = `
          <div class="gaa-sparkline">
            <svg width="${W}" height="${H}" style="overflow:visible;">
              <!-- Threshold line -->
              <line x1="${pad}" y1="${threshold_y}" x2="${W - pad}" y2="${threshold_y}"
                    stroke="#DC2626" stroke-width="1" stroke-dasharray="4,3" opacity="0.6"/>
              <!-- Trend polyline -->
              <polyline points="${points}" fill="none"
                        stroke="${dirColor}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
              <!-- Data points -->
              ${history.map((v, i) => `
                <circle cx="${pad + i * xStep}" cy="${yScale(v)}" r="3"
                        fill="${dirColor}" stroke="#fff" stroke-width="1.5"/>
              `).join('')}
            </svg>
            <div style="font-size:0.72rem; color:${dirColor}; margin-top:4px;">
              ${_esc(trend.direction ?? '')} · Slope: ₱${trend.slope ?? 0}/period
            </div>
          </div>`;
    };

    // ─────────────────────────────────────────────────────
    //  6. BATCH AT-RISK TABLE
    // ─────────────────────────────────────────────────────
    const renderBatchTable = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const d    = _unwrap(res);
        const rows = d.at_risk_employees ?? [];

        if (!rows.length) {
            el.innerHTML = '<p style="color:#16A34A; font-size:0.85rem;">✅ No at-risk employees in this period.</p>';
            return;
        }

        const statusColors = { CRITICAL:'#DC2626', DANGER:'#EA580C', WARNING:'#D97706' };

        const trs = rows.map(r => {
            const sc = statusColors[r.status] ?? '#6B7280';
            return `
              <tr style="border-bottom:1px solid #F3F4F6;">
                <td style="padding:10px 12px; font-size:0.82rem;">${_esc(r.employee_id_num ?? r.employee_id)}</td>
                <td style="padding:10px 12px; font-size:0.82rem; font-weight:600;">${_esc(r.employee_name)}</td>
                <td style="padding:10px 12px; font-size:0.82rem;">${_esc(r.department ?? '—')}</td>
                <td style="padding:10px 12px; font-size:0.82rem; font-weight:700; color:${sc};">${_peso(r.net_pay)}</td>
                <td style="padding:10px 12px;">
                  <span style="background:${sc}; color:#fff; padding:2px 9px; border-radius:10px; font-size:0.72rem; font-weight:700;">
                    ${_esc(r.status)}
                  </span>
                </td>
                <td style="padding:10px 12px; font-size:0.82rem; font-weight:700;">${r.risk_score}/100</td>
              </tr>`;
        }).join('');

        el.innerHTML = `
          <table style="width:100%; border-collapse:collapse; font-family:inherit;">
            <thead>
              <tr style="background:#F9FAFB; border-bottom:2px solid #E5E7EB;">
                <th style="padding:10px 12px; text-align:left; font-size:0.75rem; color:#6B7280; font-weight:600;">ID</th>
                <th style="padding:10px 12px; text-align:left; font-size:0.75rem; color:#6B7280; font-weight:600;">Employee</th>
                <th style="padding:10px 12px; text-align:left; font-size:0.75rem; color:#6B7280; font-weight:600;">Department</th>
                <th style="padding:10px 12px; text-align:left; font-size:0.75rem; color:#6B7280; font-weight:600;">Net Pay</th>
                <th style="padding:10px 12px; text-align:left; font-size:0.75rem; color:#6B7280; font-weight:600;">Status</th>
                <th style="padding:10px 12px; text-align:left; font-size:0.75rem; color:#6B7280; font-weight:600;">Risk</th>
              </tr>
            </thead>
            <tbody>${trs}</tbody>
          </table>`;
    };

    // ─────────────────────────────────────────────────────
    //  7. DEPARTMENT HEAT MAP
    // ─────────────────────────────────────────────────────
    const renderDeptHeatmap = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const d    = _unwrap(res);
        const depts = d.department_heat_map ?? [];

        const cards = depts.map(dept => {
            const score = dept.avg_risk_score;
            const color = dept.risk_band?.color ?? '#6B7280';
            const pct   = Math.min(100, score);
            return `
              <div style="background:#F9FAFB; border-radius:8px; padding:14px; min-width:160px; flex:1;">
                <div style="font-size:0.78rem; font-weight:700; color:#1F2937; margin-bottom:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  ${_esc(dept.department)}
                </div>
                <div style="height:6px; background:#E5E7EB; border-radius:3px; margin-bottom:6px; overflow:hidden;">
                  <div style="height:100%; width:${pct}%; background:${color}; border-radius:3px; transition:width .6s;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.72rem; color:#6B7280;">
                  <span>Avg Risk: <strong style="color:${color};">${score}</strong></span>
                  <span>${dept.employee_count} emp</span>
                </div>
                ${dept.violations > 0 ? `<div style="font-size:0.7rem; color:#DC2626; margin-top:4px;">⚠ ${dept.violations} violation(s)</div>` : ''}
              </div>`;
        }).join('');

        el.innerHTML = `
          <div style="display:flex; flex-wrap:wrap; gap:12px;">${cards || '<span style="color:#6B7280; font-size:0.85rem;">No department data.</span>'}</div>`;
    };

    // ─────────────────────────────────────────────────────
    //  8. HEADROOM PROGRESS BAR
    // ─────────────────────────────────────────────────────
    const renderHeadroomBar = (selector, res) => {
        const el = _el(selector);
        if (!el) return;

        const d    = _unwrap(res);
        const pct  = Math.min(100, Math.max(0, d.headroom_pct ?? 0));
        const color = pct > 40 ? '#16A34A' : pct > 20 ? '#D97706' : '#DC2626';
        const safe  = d.can_add_deduction !== false;

        el.innerHTML = `
          <div class="gaa-headroom-bar" style="font-family:inherit;">
            <div style="display:flex; justify-content:space-between; font-size:0.78rem; color:#6B7280; margin-bottom:6px;">
              <span>Deduction Headroom</span>
              <span style="font-weight:700; color:${color};">${_peso(d.headroom_amount ?? 0)}</span>
            </div>
            <div style="height:10px; background:#E5E7EB; border-radius:5px; overflow:hidden;">
              <div style="height:100%; width:${pct}%; background:${color}; border-radius:5px; transition:width .6s;"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.72rem; color:#6B7280; margin-top:5px;">
              <span>₱0</span>
              <span>${_peso(d.current_net_pay ?? 0)} net pay</span>
            </div>
            ${d.proposed_deduction > 0 ? `
              <div style="margin-top:8px; padding:8px 12px; border-radius:6px;
                   background:${safe ? '#F0FDF4' : '#FEF2F2'}; border:1px solid ${safe ? '#BBF7D0' : '#FECACA'};
                   font-size:0.8rem; color:${safe ? '#15803D' : '#DC2626'}; font-weight:600;">
                ${safe ? '✅' : '🚫'} Proposed deduction ${_peso(d.proposed_deduction)}: ${_esc(d.recommendation ?? '')}
              </div>` : ''}
          </div>`;
    };

    // ── Public surface ────────────────────────────────────
    return {
        renderBadge,
        renderStatusCard,
        renderRiskMeter,
        renderRecommendations,
        renderTrendSparkline,
        renderBatchTable,
        renderDeptHeatmap,
        renderHeadroomBar,
    };

})();

window.GAAui = GAAui;
