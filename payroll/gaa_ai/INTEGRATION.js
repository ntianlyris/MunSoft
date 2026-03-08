/**
 * INTEGRATION QUICKSTART
 * ─────────────────────────────────────────────────────────────
 *  File     : INTEGRATION.md (read-only reference)
 *  Location : Munsoft/payroll/
 *
 *  This file shows the exact 2–4 lines to add to your existing
 *  payroll page so GAAPayroll hooks in non-destructively.
 * ─────────────────────────────────────────────────────────────
 *
 *  ═══════════════════════════════════════════════════════════
 *  STEP 1 — Add the widget to your payroll page PHP file
 *  ═══════════════════════════════════════════════════════════
 *
 *  Find the section of your payroll page that renders the
 *  computation area (usually just above or below the payroll
 *  table container). Add this ONE line:
 *
 *    <?php include 'gaa_status_widget.php'; ?>
 *
 *  The widget outputs:
 *    • The GAA Intelligence panel (collapses/expands)
 *    • The Save Guard modal (hidden until needed)
 *    • The Employee Detail slide-in panel
 *    • All CSS and JS <link>/<script> tags (once per page)
 *
 *  ═══════════════════════════════════════════════════════════
 *  STEP 2 — Configure GAAPayroll for YOUR table structure
 *  ═══════════════════════════════════════════════════════════
 *
 *  Add this <script> block AFTER the widget include.
 *  Adjust the selectors/column indices to match your table.
 *
 *  <script>
 *  GAAPayroll.init({
 *
 *      // The payroll results table (rendered by your AJAX response)
 *      tableSelector:        '#payroll-result-table',
 *
 *      // Each data row must have data-employee-id="N"
 *      // If your rows use a different attribute, change this:
 *      rowSelector:          'tbody tr[data-employee-id]',
 *      employeeIdAttr:       'data-employee-id',
 *
 *      // 0-based column indices of each value in your <td> cells:
 *      //   Name | ID Num | Position | Gross | Deductions | Net
 *      //    0       1         2        3         4          5
 *      nameColIndex:         0,
 *      grossColIndex:        3,
 *      deductionsColIndex:   4,
 *
 *      // Your Save Payroll button selector
 *      saveButtonSelector:   '#btn-save-payroll',
 *
 *      // Point to the payroll handler (same folder)
 *      handlerUrl:           '/Munsoft/payroll/gaa_payroll_handler.php',
 *
 *      // 'monthly' or 'semi-monthly'
 *      // Or use frequencySelector to read it from the DOM:
 *      frequencySelector:    '#active-frequency-display',
 *  });
 *  </script>
 *
 *  ═══════════════════════════════════════════════════════════
 *  STEP 3 — Hook into your existing AJAX compute callback
 *  ═══════════════════════════════════════════════════════════
 *
 *  Find your existing AJAX call that renders the payroll table.
 *  It probably looks something like this (jQuery or vanilla):
 *
 *  // ── YOUR EXISTING CODE ──────────────────────────────────
 *  $.ajax({
 *      url: 'some_compute_handler.php',
 *      data: { period_id: ..., dept_id: ..., emp_type: ... },
 *      success: function(html) {
 *          $('#payroll-table-container').html(html);
 *          // ↑ This renders the table
 *
 *          // ── ADD THIS ONE LINE ──────────────────────────
 *          GAAPayroll.scanTable();
 *          // ──────────────────────────────────────────────
 *      }
 *  });
 *
 *  OR — if you prefer zero modification to existing JS:
 *  Use watchTable() instead and GAAPayroll auto-detects rendering:
 *
 *  // Add this once after GAAPayroll.init():
 *  GAAPayroll.watchTable('#payroll-table-container');
 *  // That's it. No other changes needed.
 *
 *  ═══════════════════════════════════════════════════════════
 *  STEP 4 — Add data-employee-id to your table rows
 *  ═══════════════════════════════════════════════════════════
 *
 *  GAAPayroll reads employee IDs from the TR's data attribute.
 *  In your server-side PHP (wherever you render the <tr> tags):
 *
 *  <tr data-employee-id="<?= $row['employee_id'] ?>">
 *      <td><?= $row['full_name'] ?></td>
 *      <td>₱<?= number_format($row['gross'], 2) ?></td>
 *      <td>₱<?= number_format($row['total_deductions'], 2) ?></td>
 *      <td>₱<?= number_format($row['net_pay'], 2) ?></td>
 *  </tr>
 *
 *  ═══════════════════════════════════════════════════════════
 *  COMPLETE EXAMPLE (minimal page integration)
 *  ═══════════════════════════════════════════════════════════
 *
 *  <?php
 *    // Your existing session/auth check
 *    // Your existing Payroll class usage
 *  ?>
 *
 *  <!-- Your existing payroll form / controls -->
 *  <div id="payroll-controls">...</div>
 *
 *  <!-- GAA Widget: ONE new line -->
 *  <?php include 'gaa_status_widget.php'; ?>
 *
 *  <!-- Your existing payroll table container -->
 *  <div id="payroll-table-container">
 *      <!-- AJAX renders the table here -->
 *  </div>
 *
 *  <script>
 *  // Configure GAAPayroll for your table
 *  GAAPayroll.init({
 *      tableSelector:        '#payroll-result-table',
 *      grossColIndex:        3,
 *      deductionsColIndex:   4,
 *      saveButtonSelector:   '#btn-save-payroll',
 *      handlerUrl:           '/Munsoft/payroll/gaa_payroll_handler.php',
 *      frequencySelector:    '#frequency-badge',
 *  });
 *
 *  // Auto-watch the container — no other changes needed
 *  GAAPayroll.watchTable('#payroll-table-container');
 *  </script>
 *
 * ─────────────────────────────────────────────────────────────
 *  WHAT YOU GET AUTOMATICALLY
 * ─────────────────────────────────────────────────────────────
 *
 *  1. Status badge injected into every payroll table row
 *     🔴 CRITICAL | 🟠 DANGER | 🟡 WARNING | 🔵 CAUTION | 🟢 STABLE | ✅ SAFE
 *
 *  2. GAA Intelligence widget panel showing:
 *     • Tier count pills (Critical: 2, Danger: 3, …)
 *     • Period-level risk band & score
 *     • At-risk employees list with sortable order
 *     • Department heat map (if period_scan used)
 *
 *  3. Click-to-expand employee detail panel:
 *     • AI risk score with component breakdown
 *     • Net pay trend sparkline (6-period history)
 *     • Next-period breach prediction
 *     • Prioritized corrective recommendations
 *
 *  4. Save Payroll guard:
 *     • CRITICAL employees → hard block modal, save disabled
 *     • DANGER/WARNING employees → soft warning, can proceed
 *     • All SAFE → save proceeds normally, no interruption
 *
 * ─────────────────────────────────────────────────────────────
 *  FILE DEPLOYMENT CHECKLIST
 * ─────────────────────────────────────────────────────────────
 *
 *  MODULE FIXES (overwrite existing files):
 *  [ ] includes/gaa_netpay_module/core/GAANetPayValidator.php
 *  [ ] includes/gaa_netpay_module/config/GAAConfig.php
 *  [ ] includes/gaa_netpay_module/api/gaa_api.php
 *
 *  NEW FILES IN payroll/:
 *  [ ] payroll/gaa_payroll_handler.php
 *  [ ] payroll/gaa_status_widget.php
 *  [ ] payroll/js/gaa_payroll.js
 *
 *  EXISTING FILES TO EDIT (minimal):
 *  [ ] Add data-employee-id="N" to your payroll <tr> tags
 *  [ ] Add <?php include 'gaa_status_widget.php'; ?> to page
 *  [ ] Add GAAPayroll.scanTable() to AJAX success callback
 *      OR GAAPayroll.watchTable('#your-container')
 */
