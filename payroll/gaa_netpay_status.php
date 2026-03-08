<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
?>
<!-- GAA Net Pay Status Styles -->
<link rel="stylesheet" href="../css/gaa_netpay_status.css">
<?php

    // ── Role & Employee ID Resolution ────────────────────────────
    $MyAdmin  = new Admin();
    $user_id  = $MyAdmin->getSessionUID();
    $roles    = $MyAdmin->initRoles($user_id);
    $role     = '';
    foreach ($roles as $k => $v) { $role = $k; }

    $is_employee     = ($role === 'Employee');
    $is_payroll_master = ($role === 'Payroll Master');

    // For Employee mode: get their employee_id from query param (validated)
    $employee_id_param = 0;
    if ($is_employee) {
        include_once '../includes/class/Employee.php';
        $MyEmployee = new Employee();
        $employee_id_param = intval($MyEmployee->getEmployeeIDByUserId($user_id));
    }

    // Access guard
    if (!$is_employee && !$is_payroll_master) {
        header('Location: ./');
        exit;
    }
?>

<!-- Content Wrapper -->
<div class="content-wrapper">

  <!-- Page Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0">
            <i class="fas fa-shield-alt mr-2 text-primary"></i>
            <?php echo $is_employee ? 'My Net Pay Status' : 'GAA Net Pay Status Monitor'; ?>
          </h1>
          <p class="text-muted mb-0" style="font-size:.85rem; margin-top:3px;">
            <?php if ($is_employee): ?>
              Your current projected net pay classification based on active earnings &amp; deductions.
            <?php else: ?>
              Live projected net pay classification computed from current active earnings &amp; deductions. Not from saved payroll records.
            <?php endif; ?>
          </p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
            <?php if ($is_payroll_master): ?>
              <li class="breadcrumb-item"><a href="payrolls.php">Payrolls</a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active">GAA Net Pay Status</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <?php if ($is_payroll_master): ?>
      <!-- ═══════════════════════════════════════════════════ -->
      <!--          PAYROLL MASTER MODE                        -->
      <!-- ═══════════════════════════════════════════════════ -->

      <!-- Legend / Tier Guide -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="gaa-legend-bar">
            <span class="gaa-legend-title"><i class="fas fa-info-circle mr-1"></i> GAA Tier Guide:</span>
            <span class="gaa-legend-item gaa-legend--critical"><span class="gaa-legend-dot"></span> Critical (≤ ₱5,000)</span>
            <span class="gaa-legend-item gaa-legend--danger"><span class="gaa-legend-dot"></span> Danger (≤ 10% above)</span>
            <span class="gaa-legend-item gaa-legend--warning"><span class="gaa-legend-dot"></span> Warning (≤ 20% above)</span>
            <span class="gaa-legend-item gaa-legend--caution"><span class="gaa-legend-dot"></span> Caution (≤ 30% above)</span>
            <span class="gaa-legend-item gaa-legend--stable"><span class="gaa-legend-dot"></span> Stable (≤ 50% above)</span>
            <span class="gaa-legend-item gaa-legend--safe"><span class="gaa-legend-dot"></span> Safe (&gt; 50% above)</span>
          </div>
        </div>
      </div>

      <!-- Summary Stats Row — populated by JS after fetch -->
      <div class="row mb-3" id="gaa-stats-row" style="display:none;">
        <div class="col-6 col-md-4 col-lg-2 mb-2">
          <div class="gaa-stat-card gaa-stat--critical">
            <div class="gaa-stat-count" id="stat-critical">0</div>
            <div class="gaa-stat-label">Critical</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-2">
          <div class="gaa-stat-card gaa-stat--danger">
            <div class="gaa-stat-count" id="stat-danger">0</div>
            <div class="gaa-stat-label">Danger</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-2">
          <div class="gaa-stat-card gaa-stat--warning">
            <div class="gaa-stat-count" id="stat-warning">0</div>
            <div class="gaa-stat-label">Warning</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-2">
          <div class="gaa-stat-card gaa-stat--caution">
            <div class="gaa-stat-count" id="stat-caution">0</div>
            <div class="gaa-stat-label">Caution</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-2">
          <div class="gaa-stat-card gaa-stat--stable">
            <div class="gaa-stat-count" id="stat-stable">0</div>
            <div class="gaa-stat-label">Stable</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-2">
          <div class="gaa-stat-card gaa-stat--safe">
            <div class="gaa-stat-count" id="stat-safe">0</div>
            <div class="gaa-stat-label">Safe</div>
          </div>
        </div>
      </div>

      <!-- Filter Card -->
      <div class="card card-outline card-primary mb-3">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filters</h3>
        </div>
        <div class="card-body">
          <form id="gaaFilterForm" class="row align-items-end">
            <div class="col-12 col-sm-6 col-md-4 mb-2">
              <label for="gaa-dept" class="font-weight-bold">Department <span class="text-danger">*</span></label>
              <select id="gaa-dept" class="form-control select2bs4" style="width:100%;">
                <option value="" selected hidden>Select Department...</option>
                <?php echo ViewDepartmentsDropdown(); ?>
              </select>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-2">
              <label for="gaa-emp-type" class="font-weight-bold">Employment Type</label>
              <select id="gaa-emp-type" class="form-control">
                <option value="Regular" selected>Regular</option>
                <option value="Casual">Casual</option>
              </select>
            </div>
            <div class="col-12 col-md-auto mb-2">
              <button type="button" id="btn-fetch-status" class="btn btn-primary btn-block" onclick="GAAStatus.fetchDepartment()">
                <i class="fas fa-sync-alt mr-1"></i> Fetch Net Pay Status
              </button>
            </div>
          </form>
          <!-- Period context badge — shown after fetch -->
          <div id="gaa-period-context" class="mt-2" style="display:none;">
            <small class="text-muted">
              <i class="fas fa-calendar-alt mr-1"></i>
              Period context: <strong id="gaa-period-label"></strong>
              &nbsp;|&nbsp; Frequency: <strong id="gaa-freq-label"></strong>
              &nbsp;|&nbsp; Half: <strong id="gaa-half-label"></strong>
            </small>
          </div>
        </div>
      </div>

      <!-- Results Card -->
      <div class="card card-outline card-primary" id="gaa-results-card" style="display:none;">
        <div class="card-header d-flex align-items-center">
          <h3 class="card-title mb-0"><i class="fas fa-table mr-2"></i>Employee Net Pay Status</h3>
          <div class="ml-auto d-flex align-items-center">
            <span class="badge badge-warning mr-2" id="gaa-results-count" style="font-size:.82rem;"></span>
            <button class="btn btn-sm btn-outline-secondary" id="btn-export-csv" onclick="GAAStatus.exportCSV()" style="display:none;">
              <i class="fas fa-download mr-1"></i> Export
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <!-- Loading state -->
          <div id="gaa-table-loading" class="text-center py-5" style="display:none;">
            <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
            <div class="mt-2 text-muted">Computing net pay statuses...</div>
          </div>
          <!-- Table -->
          <div class="table-responsive" id="gaa-table-wrap">
            <table class="table table-bordered table-hover mb-0" id="gaaStatusTable">
              <thead class="thead-light">
                <tr>
                  <th class="text-center" style="width:40px;">#</th>
                  <th>EIDN</th>
                  <th>Employee</th>
                  <th>Position</th>
                  <th class="text-right">Gross</th>
                  <th class="text-right">Deductions</th>
                  <th class="text-right">Net Pay</th>
                  <th class="text-center">Last Period</th>
                  <th class="text-center">Current Status</th>
                  <th class="text-center">Change</th>
                </tr>
              </thead>
              <tbody id="gaa-table-body">
                <!-- JS populates this -->
              </tbody>
            </table>
          </div>
          <!-- Empty state -->
          <div id="gaa-table-empty" class="text-center py-5" style="display:none;">
            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
            <p class="text-muted">No employees found for the selected filters.</p>
          </div>
        </div>
      </div>

      <?php else: ?>
      <!-- ═══════════════════════════════════════════════════ -->
      <!--          EMPLOYEE PERSONAL MODE                     -->
      <!-- ═══════════════════════════════════════════════════ -->

      <input type="hidden" id="employee-id-param" value="<?php echo $employee_id_param; ?>">

      <!-- Loading Skeleton -->
      <div id="emp-status-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
        <div class="mt-2 text-muted">Loading your net pay status...</div>
      </div>

      <!-- Employee Status Panel (hidden until data loads) -->
      <div id="emp-status-panel" style="display:none;">

        <!-- Main Status Card -->
        <div class="row justify-content-center mb-4">
          <div class="col-12 col-md-8 col-lg-6">
            <div class="card gaa-emp-main-card" id="emp-main-card">
              <div class="card-body text-center py-4">
                <div class="gaa-emp-status-icon mb-2" id="emp-status-icon"></div>
                <div class="gaa-emp-status-label" id="emp-status-label">—</div>
                <div class="gaa-emp-net-amount" id="emp-net-amount">₱0.00</div>
                <div class="text-muted mt-1" style="font-size:.82rem;" id="emp-period-context"></div>
                <div class="gaa-emp-message mt-3" id="emp-status-message"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Comparison Row: Last Period vs Current -->
        <div class="row justify-content-center mb-4">
          <div class="col-12 col-md-10 col-lg-8">
            <div class="card card-outline card-light">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Period Comparison</h3>
              </div>
              <div class="card-body">
                <div class="row align-items-center text-center">
                  <!-- Last Period -->
                  <div class="col-5">
                    <div class="gaa-compare-label text-muted mb-1">Last Payroll Period</div>
                    <div class="gaa-compare-period text-muted mb-2" id="emp-last-period-label" style="font-size:.8rem;"></div>
                    <div id="emp-last-badge" class="mb-1"></div>
                    <div class="gaa-compare-amount font-weight-bold" id="emp-last-net">—</div>
                  </div>
                  <!-- Arrow -->
                  <div class="col-2">
                    <div class="gaa-transition-arrow" id="emp-transition-arrow">
                      <i class="fas fa-arrow-right fa-2x text-muted"></i>
                    </div>
                  </div>
                  <!-- Current -->
                  <div class="col-5">
                    <div class="gaa-compare-label text-muted mb-1">Current Projected</div>
                    <div class="gaa-compare-period text-muted mb-2" id="emp-current-period-label" style="font-size:.8rem;"></div>
                    <div id="emp-current-badge" class="mb-1"></div>
                    <div class="gaa-compare-amount font-weight-bold" id="emp-current-net">—</div>
                  </div>
                </div>

                <!-- Trend Message -->
                <div class="gaa-trend-msg mt-3 p-3 rounded" id="emp-trend-msg" style="display:none;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Breakdown Card -->
        <div class="row justify-content-center mb-4">
          <div class="col-12 col-md-10 col-lg-8">
            <div class="card card-outline card-light">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list-ul mr-2"></i>Pay Breakdown (Current)</h3>
              </div>
              <div class="card-body p-0">
                <table class="table table-sm mb-0 gaa-breakdown-table">
                  <tbody id="emp-breakdown-body">
                    <tr><td colspan="2" class="text-center text-muted py-3">Loading...</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- #emp-status-panel -->

      <!-- No Data State -->
      <div id="emp-no-data" style="display:none;" class="row justify-content-center">
        <div class="col-12 col-md-6 text-center py-5">
          <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
          <p class="text-muted">No employment or earnings data found for your account.</p>
        </div>
      </div>

      <?php endif; ?>

    </div><!-- /.container-fluid -->
  </section>
</div><!-- /.content-wrapper -->

<!-- Footer -->
<footer class="main-footer">
  <div class="float-right d-none d-sm-block"><b>MunSoft</b> HR &amp; Payroll</div>
  GAA Net Pay Intelligence Module
</footer>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- Scripts                                                      -->
<!-- ═══════════════════════════════════════════════════════════ -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/select2/js/select2.full.min.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../dist/js/adminlte.js"></script>
<script src="system.js"></script>

<script>
  // Pass PHP mode flag to JS
  window.GAA_PAGE_MODE = '<?php echo $is_employee ? 'employee' : 'master'; ?>';
  window.GAA_EMPLOYEE_ID = <?php echo $employee_id_param; ?>;
</script>
<script src="js/gaa_netpay.js"></script>

<script>
$(function(){
  // Init Select2 for department dropdown (master mode only)
  if (window.GAA_PAGE_MODE === 'master') {
    $('#gaa-dept').select2({ theme: 'bootstrap4', placeholder: 'Select Department...' });
  }

  // Auto-trigger employee mode on load
  if (window.GAA_PAGE_MODE === 'employee') {
    GAAStatus.fetchEmployee(window.GAA_EMPLOYEE_ID);
  }
});
</script>

</body>
</html>
