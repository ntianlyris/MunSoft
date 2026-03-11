<?php
include_once '../includes/layout/head.php';
include_once '../includes/layout/navbar.php';
include_once '../includes/layout/sidebar.php';
include_once '../includes/view/view.php';
include_once '../includes/class/Employee.php';

$Employee = new Employee();
$employee_id = $Employee->getEmployeeIDByUserId($user_id);

// Variables will be populated via AJAX
$employee_profile = null;
$payroll_records = [];
$leave_applications = [];
$payslip_history = [];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">My Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <!-- Payroll Summary Cards -->
      <div class="row mb-3">

        <!-- GROSS PAY CARD -->
        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
          <!-- small box -->
          <div class="small-box bg-primary" style="overflow: visible;">
            <div class="inner">
              <h3 id="gross-pay-amount">
                <i class="fas fa-spinner fa-spin"></i>
              </h3>
              <p>Current Gross Pay</p>
              <small id="gross-pay-period">Loading...</small>
            </div>
            <div class="icon"
              style="display: flex; align-items: center; justify-content: center; position: absolute; right: 0; top: 0; bottom: 0; width: 70px;">
              <i class="fas fa-money-bill-wave"></i>
            </div>
          </div>
        </div>

        <!-- DEDUCTIONS CARD -->
        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
          <!-- small box -->
          <div class="small-box bg-warning" style="overflow: visible;">
            <div class="inner">
              <h3 id="deductions-amount">
                <i class="fas fa-spinner fa-spin"></i>
              </h3>
              <p>Current Deductions</p>
              <small id="deductions-date">Loading...</small>
            </div>
            <div class="icon"
              style="display: flex; align-items: center; justify-content: center; position: absolute; right: 0; top: 0; bottom: 0; width: 70px;">
              <i class="fas fa-minus-circle"></i>
            </div>
          </div>
        </div>

        <!-- NET PAY CARD -->
        <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
          <!-- small box -->
          <div class="small-box bg-success" style="overflow: visible;">
            <div class="inner">
              <h3 id="net-pay-amount">
                <i class="fas fa-spinner fa-spin"></i>
              </h3>
              <p>Current Net Pay</p>
              <small id="netpay-date">Loading...</small>
            </div>
            <div class="icon"
              style="display: flex; align-items: center; justify-content: center; position: absolute; right: 0; top: 0; bottom: 0; width: 70px;">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
        </div>

      </div>
      <!-- /.row -->

      <!-- Mobile-First Grid Layout -->
      <div class="row">

        <!-- PROFILE CARD -->
        <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
          <div class="card card-primary card-outline h-100 clickable-card" style="cursor: pointer;" id="profile-card"
            onclick="window.location.href='profile.php?emp_id=<?php echo $employee_id; ?>'">
            <div class="card-body text-center py-2">
              <div class="mb-2">
                <i class="fas fa-user fa-2x text-primary"></i>
              </div>
              <h6 class="card-title mb-1">My Profile</h6>
              <p class="card-text text-muted small mb-2" style="font-size: 0.75rem;">
                View and edit your information
              </p>
              <div class="d-grid gap-1">
                <button class="btn btn-primary btn-xs" type="button"
                  style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                  <i class="fas fa-arrow-right"></i> View
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- PAYROLLS CARD -->
        <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
          <div class="card card-success card-outline h-100 clickable-card" style="cursor: pointer;"
            onclick="showPayrollsModal()">
            <div class="card-body text-center py-2">
              <div class="mb-2">
                <i class="fas fa-file-invoice fa-2x text-success"></i>
              </div>
              <h6 class="card-title mb-1">Payrolls</h6>
              <p class="card-text text-muted small mb-2" style="font-size: 0.75rem;">
                <?php echo $payroll_records ? count($payroll_records) : '0'; ?> recent
              </p>
              <div class="d-grid gap-1">
                <button class="btn btn-success btn-xs" type="button"
                  style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                  <i class="fas fa-arrow-right"></i> View
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- PAYSLIP CARD -->
        <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
          <div class="card card-info card-outline h-100 clickable-card" style="cursor: pointer;"
            onclick="showPayslipsModal()">
            <div class="card-body text-center py-2">
              <div class="mb-2">
                <i class="fas fa-file-pdf fa-2x text-info"></i>
              </div>
              <h6 class="card-title mb-1">Payslips</h6>
              <p class="card-text text-muted small mb-2" style="font-size: 0.75rem;">
                Download your payslips
              </p>
              <div class="d-grid gap-1">
                <button class="btn btn-info btn-xs" type="button" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                  <i class="fas fa-arrow-right"></i> View
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- LEAVE CARD -->
        <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
          <div class="card card-warning card-outline h-100 clickable-card" style="cursor: pointer;"
            onclick="window.location.href='leave_application.php'">
            <div class="card-body text-center py-2">
              <div class="mb-2">
                <i class="fas fa-calendar-check fa-2x text-warning"></i>
              </div>
              <h6 class="card-title mb-1">Leave</h6>
              <p class="card-text text-muted small mb-2" style="font-size: 0.75rem;">
                Manage your requests
              </p>
              <div class="d-grid gap-1">
                <button class="btn btn-warning btn-xs" type="button"
                  style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                  <i class="fas fa-arrow-right"></i> Apply
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- /.row -->

      <!-- Quick Actions Section -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-secondary card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
              <div class="row g-2">
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                  <button class="btn btn-light border w-100 py-3" onclick="window.location.href='profile.php'"
                    title="Edit Profile">
                    <i class="fas fa-edit fa-2x mb-2"></i>
                    <div class="small">Edit Profile</div>
                  </button>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                  <button class="btn btn-light border w-100 py-3" onclick="showPayrollsModal()" title="View Payrolls">
                    <i class="fas fa-history fa-2x mb-2"></i>
                    <div class="small">Payroll History</div>
                  </button>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                  <button class="btn btn-light border w-100 py-3" onclick="showPayslipsModal()"
                    title="Download Payslip">
                    <i class="fas fa-download fa-2x mb-2"></i>
                    <div class="small">Download Payslip</div>
                  </button>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                  <button class="btn btn-light border w-100 py-3" onclick="window.location.href='leave_application.php'"
                    title="Apply Leave">
                    <i class="fas fa-file-import fa-2x mb-2"></i>
                    <div class="small">File Leave</div>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->

      <!-- Recent Activity Section -->
      <div class="row mt-4">
        <div class="col-12 col-md-6">
          <!-- Recent Payrolls -->
          <div class="card card-success card-outline" id="payrollsSection">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-history"></i> Recent Payrolls</h3>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="p-3 text-center text-muted">
                  <i class="fas fa-spinner fa-spin"></i> Loading payroll data...
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <!-- Recent Leave Applications -->
          <div class="card card-warning card-outline" id="leavesSection">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-calendar"></i> Leave Applications</h3>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="p-3 text-center text-muted">
                  <i class="fas fa-spinner fa-spin"></i> Loading leave data...
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->

      <!-- Employment History Section -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-primary card-outline" id="employmentsSection">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-briefcase"></i> Employment History</h3>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="p-3 text-center text-muted">
                  <i class="fas fa-spinner fa-spin"></i> Loading employment history...
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Payrolls Modal -->
<div class="modal fade" id="payrollsModal" tabindex="-1" role="dialog" aria-labelledby="payrollsModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="payrollsModalLabel"><i class="fas fa-file-invoice"></i> Payroll History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>Period</th>
                <th>Gross</th>
                <th>Deductions</th>
                <th>Net Pay</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="4" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Payslips Modal -->
<div class="modal fade" id="payslipsModal" tabindex="-1" role="dialog" aria-labelledby="payslipsModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="payslipsModalLabel"><i class="fas fa-file-pdf"></i> Payslips</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>Period</th>
                <th>Gross</th>
                <th>Net Pay</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="4" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>


</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>

<script>
  // Initialize dashboard on page load
  $(document).ready(function () {
    $('#home_li').addClass('active');
    loadPayrollSummary();
    loadDashboardData();
  });

  /**
   * UI UTILITIES
   */
  function formatCurrency(amount) {
    var val = parseFloat(amount);
    if (isNaN(val)) return "0.00";
    return val.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function renderEmptyState(container, message) {
    $(container).html('<div class="p-3 text-center text-muted">' + (message || 'No records found.') + '</div>');
  }

  function renderErrorState(container, message) {
    $(container).html('<div class="p-3 text-center text-danger"><i class="fas fa-exclamation-circle"></i> ' + (message || 'Error loading data.') + '</div>');
  }

  /**
   * PAYROLL SUMMARY (Summary Cards)
   */
  function loadPayrollSummary() {
    $.ajax({
      url: 'get_payroll_summary.php',
      type: 'GET',
      dataType: 'json',
      success: function (response) {
        if (response.success && response.data) {
          updatePayrollSummaryUI(response.data);
        } else {
          showPayrollSummaryError();
        }
      },
      error: function (err) {
        console.error('Error loading payroll summary:', err);
        showPayrollSummaryError();
      }
    });
  }

  function updatePayrollSummaryUI(data) {
    $('#gross-pay-amount').html('₱' + formatCurrency(data.total_gross));
    $('#deductions-amount').html('₱' + formatCurrency(data.total_deductions));
    $('#net-pay-amount').html('₱' + formatCurrency(data.total_net_pay));

    var periodLabel = data.period_label || 'Current Period';
    $('#gross-pay-period, #deductions-date, #netpay-date').html(periodLabel);
  }

  function showPayrollSummaryError() {
    $('#gross-pay-amount, #deductions-amount, #net-pay-amount').html('₱0.00');
    $('#gross-pay-period, #deductions-date, #netpay-date').html('No data');
  }

  /**
   * DASHBOARD DATA (Recent Activity)
   */
  function loadDashboardData() {
    $.ajax({
      url: 'get_dashboard_data.php',
      type: 'GET',
      dataType: 'json',
      data: { action: 'all' },
      success: function (response) {
        if (response.success) {
          // Process Payrolls
          if (response.payrolls && response.payrolls.length > 0) {
            updatePayrollsUI(response.payrolls);
          } else {
            renderEmptyState('#payrollsSection .list-group', 'No recent payrolls found.');
            $('#payrollsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">No history found.</td></tr>');
          }

          // Process Leaves
          if (response.leaves && response.leaves.length > 0) {
            updateLeavesUI(response.leaves);
          } else {
            renderEmptyState('#leavesSection .list-group', 'No leave applications found.');
          }

          // Process Employment History
          if (response.employments && response.employments.length > 0) {
            updateEmploymentsUI(response.employments);
          } else {
            renderEmptyState('#employmentsSection .list-group', 'No employment history available.');
          }

          // Trigger Payslip logic (Modal based)
          updatePayslipsUI();
        } else {
          handleDashboardError();
        }
      },
      error: function (err) {
        console.error('Dashboard AJAX error:', err);
        handleDashboardError();
      }
    });
  }

  function handleDashboardError() {
    renderErrorState('#payrollsSection .list-group');
    renderErrorState('#leavesSection .list-group');
    renderErrorState('#employmentsSection .list-group');
  }

  function updatePayrollsUI(payrolls) {
    $('.card-success .card-text').text(payrolls.length + ' recent payroll' + (payrolls.length > 1 ? 's' : ''));

    var cardHtml = '';
    var modalHtml = '';

    payrolls.forEach(function (p) {
      cardHtml += '<div class="list-group-item p-3 border-bottom">' +
                    '<div class="d-flex justify-content-between align-items-start">' +
                      '<div><h6 class="mb-1">' + p.period_label + '</h6>' +
                      '<small class="text-muted">' + (p.year || '') + '</small></div>' +
                      '<div class="text-right"><div class="font-weight-bold">₱' + formatCurrency(p.net_pay) + '</div>' +
                      '<small class="text-muted">Net Pay</small></div>' +
                    '</div></div>';

      modalHtml += '<tr><td>' + p.period_label + '</td>' +
                     '<td>₱' + formatCurrency(p.gross) + '</td>' +
                     '<td>₱' + formatCurrency(p.total_deductions) + '</td>' +
                     '<td><strong>₱' + formatCurrency(p.net_pay) + '</strong></td></tr>';
    });

    $('#payrollsSection .list-group').html(cardHtml);
    $('#payrollsModal table tbody').html(modalHtml);
  }

  function updateLeavesUI(leaves) {
    var html = '';
    leaves.forEach(function (l) {
      var badge = l.status === 'Approved' ? 'success' : (l.status === 'Disapproved' ? 'danger' : 'warning');
      html += '<div class="list-group-item p-3 border-bottom">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                  '<div><h6 class="mb-1">' + (l.leave_name || 'Leave Request') + '</h6>' +
                  '<small class="text-muted">' + l.date_filed + '</small></div>' +
                  '<div class="text-right"><span class="badge badge-' + badge + '">' + l.status + '</span></div>' +
                '</div></div>';
    });
    $('#leavesSection .list-group').html(html);
  }

  function updateEmploymentsUI(employments) {
    var html = '';
    employments.forEach(function (e) {
      var start = e.employment_start || 'N/A';
      var end = e.employment_end ? e.employment_end : (e.employment_status == '1' ? 'Present' : 'N/A');
      var pos = e.designation || e.position_title || 'Position';
      var dept = e.dept_name || e.dept_assigned || 'Department';
      var badge = e.employment_status == '1' ? 'success' : 'secondary';
      var label = e.employment_status == '1' ? 'Active' : 'Ended';

      html += '<div class="list-group-item p-3 border-bottom">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                  '<div><h6 class="mb-1">' + pos + ' <small class="text-muted">(' + dept + ')</small></h6>' +
                  '<small class="text-muted">' + start + ' — ' + end + '</small></div>' +
                  '<div class="text-right"><div class="mb-2"><span class="badge badge-' + badge + '">' + label + '</span></div>' +
                  '<a class="btn btn-sm btn-outline-primary" href="employment.php?emp_id=' + e.employee_id + '&employment_id=' + e.employment_id + '">' +
                  '<i class="fas fa-eye"></i> View</a></div>' +
                '</div></div>';
    });
    $('#employmentsSection .list-group').html(html);
  }

  /**
   * PAYSLIP MODAL LOGIC
   */
  function updatePayslipsUI() {
    $('.card-info .card-text').text('Download your payslips');
    loadPayslipYears();
  }

  function loadPayslipYears() {
    $.ajax({
      url: 'get_payslip_months.php',
      type: 'GET',
      dataType: 'json',
      data: { action: 'get_available_years' },
      success: function (response) {
        if (response.success && response.data && response.data.length > 0) {
          createPayslipYearSelector(response.data);
          loadPayslipsByYear(response.data[0]); // Auto-load latest year
        } else {
          $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">No payslips available.</td></tr>');
        }
      },
      error: function (err) {
        console.error('Error fetching payslip years:', err);
        renderErrorState('#payslipsModal table tbody');
      }
    });
  }

  function createPayslipYearSelector(years) {
    var options = years.map(function(y) { return '<option value="' + y + '">' + y + '</option>'; }).join('');
    var html = '<div class="year-selector-container p-3 border-bottom"><div class="row align-items-center">' +
                 '<div class="col-auto"><label class="mb-0">Year:</label></div>' +
                 '<div class="col-md-3"><select id="payslipYearSelector" class="form-control form-control-sm" onchange="loadPayslipsByYear(this.value)">' +
                 options + '</select></div></div></div>';

    var container = $('#payslipsModal .table-responsive');
    container.find('.year-selector-container').remove();
    container.prepend(html);
  }

  function loadPayslipsByYear(year) {
    if (!year) return;
    
    $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading records for ' + year + '...</td></tr>');

    $.ajax({
      url: 'get_payslip_months.php',
      type: 'GET',
      dataType: 'json',
      data: { action: 'fetch_months_by_year', year: year },
      success: function (response) {
        if (response.success && response.data && response.data.length > 0) {
          populatePayslipsTable(response.data);
        } else {
          $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">No data found for ' + year + '.</td></tr>');
        }
      },
      error: function (err) {
        console.error('Error loading payslips for year:', err);
        renderErrorState('#payslipsModal table tbody');
      }
    });
  }

  function populatePayslipsTable(months) {
    var html = '';
    months.forEach(function (m) {
      var isReady = m.has_data && m.gross > 0;
      var btnClass = isReady ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-secondary disabled';
      var btnIcon = isReady ? 'fas fa-download' : 'fas fa-lock';
      var btnText = isReady ? 'Download' : 'Locked';
      var btnAttr = isReady ? 'onclick="downloadMonthlyPayslip(\'' + m.year + '\', \'' + m.month + '\', \'' + m.payroll_period + '\')"' : 'title="Not available"';

      // Use monthly_net for display as per requirement (Gross - Deductions)
      var displayNet = m.monthly_net || m.net_pay;

      html += '<tr>' +
                '<td>' + m.label + '</td>' +
                '<td>₱' + formatCurrency(m.gross) + '</td>' +
                '<td>' + (isReady ? '<strong>₱' + formatCurrency(displayNet) + '</strong>' : '<span class="text-muted">—</span>') + '</td>' +
                '<td><button class="' + btnClass + '" ' + btnAttr + '><i class="' + btnIcon + '"></i> ' + btnText + '</button></td>' +
              '</tr>';
    });
    $('#payslipsModal table tbody').html(html);
  }

  function downloadMonthlyPayslip(year, month, payroll_period) {
    var user_id = '<?php echo isset($user_id) ? $user_id : ""; ?>';
    if (!user_id) { alert('Session expired. Please re-login.'); return; }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '../prints/print_payslip.php';
    form.target = '_blank';

    [ ['user_id', user_id], ['year', year], ['payroll_period', payroll_period] ].forEach(function(pair) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = pair[0];
      input.value = pair[1];
      form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  }

  function showPayrollsModal() { $('#payrollsModal').modal('show'); }
  function showPayslipsModal() { $('#payslipsModal').modal('show'); }

  // Visual enhancement: Hover cards
  $(document).on('mouseenter', '.clickable-card', function() {
    $(this).css({ 'transform': 'translateY(-5px)', 'box-shadow': '0 5px 15px rgba(0,0,0,0.15)', 'transition': 'all 0.3s ease' });
  }).on('mouseleave', '.clickable-card', function() {
    $(this).css({ 'transform': 'translateY(0)', 'box-shadow': 'none' });
  });
</script>

<?php
include_once '../includes/layout/footer.php';
?>