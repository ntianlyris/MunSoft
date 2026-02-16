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
              <div class="icon" style="display: flex; align-items: center; justify-content: center; position: absolute; right: 0; top: 0; bottom: 0; width: 70px;">
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
              <div class="icon" style="display: flex; align-items: center; justify-content: center; position: absolute; right: 0; top: 0; bottom: 0; width: 70px;">
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
              <div class="icon" style="display: flex; align-items: center; justify-content: center; position: absolute; right: 0; top: 0; bottom: 0; width: 70px;">
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
            <div class="card card-primary card-outline h-100 clickable-card" style="cursor: pointer;" id="profile-card" onclick="window.location.href='profile.php?emp_id=<?php echo $employee_id; ?>'">
              <div class="card-body text-center py-2">
                <div class="mb-2">
                  <i class="fas fa-user fa-2x text-primary"></i>
                </div>
                <h6 class="card-title mb-1">My Profile</h6>
                <p class="card-text text-muted small mb-2" style="font-size: 0.75rem;">
                  View and edit your information
                </p>
                <div class="d-grid gap-1">
                  <button class="btn btn-primary btn-xs" type="button" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                    <i class="fas fa-arrow-right"></i> View
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- PAYROLLS CARD -->
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
            <div class="card card-success card-outline h-100 clickable-card" style="cursor: pointer;" onclick="showPayrollsModal()">
              <div class="card-body text-center py-2">
                <div class="mb-2">
                  <i class="fas fa-file-invoice fa-2x text-success"></i>
                </div>
                <h6 class="card-title mb-1">Payrolls</h6>
                <p class="card-text text-muted small mb-2" style="font-size: 0.75rem;">
                  <?php echo $payroll_records ? count($payroll_records) : '0'; ?> recent
                </p>
                <div class="d-grid gap-1">
                  <button class="btn btn-success btn-xs" type="button" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                    <i class="fas fa-arrow-right"></i> View
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- PAYSLIP CARD -->
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
            <div class="card card-info card-outline h-100 clickable-card" style="cursor: pointer;" onclick="showPayslipsModal()">
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
            <div class="card card-warning card-outline h-100 clickable-card" style="cursor: pointer;" onclick="window.location.href='leave_application.php'">
              <div class="card-body text-center py-2">
                <div class="mb-2">
                  <i class="fas fa-calendar-check fa-2x text-warning"></i>
                </div>
                <h6 class="card-title mb-1">Leave</h6>
                <p class="card-text text-muted small mb-2" style="font-size: 0.75rem;">
                  Manage your requests
                </p>
                <div class="d-grid gap-1">
                  <button class="btn btn-warning btn-xs" type="button" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
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
                    <button class="btn btn-light border w-100 py-3" onclick="window.location.href='profile.php'" title="Edit Profile">
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
                    <button class="btn btn-light border w-100 py-3" onclick="showPayslipsModal()" title="Download Payslip">
                      <i class="fas fa-download fa-2x mb-2"></i>
                      <div class="small">Download Payslip</div>
                    </button>
                  </div>
                  <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <button class="btn btn-light border w-100 py-3" onclick="window.location.href='leave_application.php'" title="Apply Leave">
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
<div class="modal fade" id="payrollsModal" tabindex="-1" role="dialog" aria-labelledby="payrollsModalLabel" aria-hidden="true">
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
              <tr><td colspan="4" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
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
<div class="modal fade" id="payslipsModal" tabindex="-1" role="dialog" aria-labelledby="payslipsModalLabel" aria-hidden="true">
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
              <tr><td colspan="4" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
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
// Load dashboard data via AJAX on page load
$(document).ready(function() {
    $('#home_li').addClass('active');
    loadPayrollSummary();
    loadDashboardData();
});

// Load payroll summary
function loadPayrollSummary() {
    $.ajax({
        url: 'get_payroll_summary.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updatePayrollSummaryUI(response.data);
            } else {
                console.log('Error loading payroll summary:', response.message);
                showPayrollSummaryError();
            }
        },
        error: function(err) {
            console.log('Error loading payroll summary:', err);
            showPayrollSummaryError();
        }
    });
}

// Update payroll summary UI
function updatePayrollSummaryUI(data) {
    var grossFormat = formatCurrency(data.total_gross);
    var deductionsFormat = formatCurrency(data.total_deductions);
    var netPayFormat = formatCurrency(data.total_net_pay);
    var periodLabel = data.period_label ? data.period_label : 'Current Period';
    
    // Display amounts in h3 with currency symbol
    $('#gross-pay-amount').html('₱' + grossFormat);
    $('#deductions-amount').html('₱' + deductionsFormat);
    $('#net-pay-amount').html('₱' + netPayFormat);
    
    // Display period/date in small elements
    $('#gross-pay-period').html(periodLabel);
    $('#deductions-date').html(periodLabel);
    $('#netpay-date').html(periodLabel);
}

// Show error in summary cards
function showPayrollSummaryError() {
    $('#gross-pay-amount').html('₱0.00');
    $('#deductions-amount').html('₱0.00');
    $('#net-pay-amount').html('₱0.00');
    $('#gross-pay-period').html('No data');
    $('#deductions-date').html('No data');
    $('#netpay-date').html('No data');
}

// Format currency helper function
function formatCurrency(amount) {
    return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Load all dashboard data
function loadDashboardData() {
    $.ajax({
      url: 'get_dashboard_data.php',
      type: 'GET',
      dataType: 'json',
      data: { action: 'all' },
      success: function(response) {
        if (response.success) {
          var employee_id = response.employee_id;
          
          // Update payrolls
          if (response.payrolls && response.payrolls.length > 0) {
            updatePayrollsUI(response.payrolls, employee_id);
          }
          
          // Update leaves
          if (response.leaves && response.leaves.length > 0) {
            updateLeavesUI(response.leaves);
          }
          
          // Update payslips
          if (response.payslips && response.payslips.length > 0) {
            updatePayslipsUI(response.payslips, employee_id);
          }
          
          // Update employments
          if (response.employments) {
            updateEmploymentsUI(response.employments);
          }
        }
      },
      error: function(err) {
        console.log('Error loading dashboard data:', err);
      }
    });
}

// Update payrolls UI
function updatePayrollsUI(payrolls, employee_id) {
    // Update card count
    $('.card-success .card-text').text(payrolls.length + ' recent payrolls');
    
    // Update Recent Payrolls section
    var payrollHTML = '';
    payrolls.forEach(function(payroll) {
        payrollHTML += '<div class="list-group-item p-3 border-bottom">';
        payrollHTML += '<div class="d-flex justify-content-between align-items-start">';
        payrollHTML += '<div class="flex-grow-1">';
        payrollHTML += '<h6 class="mb-1">' + payroll.period_label + '</h6>';
        payrollHTML += '<small class="text-muted">' + payroll.year + '</small>';
        payrollHTML += '</div>';
        payrollHTML += '<div class="text-right">';
        payrollHTML += '<div class="font-weight-bold">₱' + parseFloat(payroll.net_pay).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</div>';
        payrollHTML += '<small class="text-muted">Net Pay</small>';
        payrollHTML += '</div>';
        payrollHTML += '</div>';
        payrollHTML += '</div>';
    });
    $('#payrollsSection .list-group').html(payrollHTML);
    
    // Update Payrolls Modal table
    var tableHTML = '';
    payrolls.forEach(function(payroll) {
        tableHTML += '<tr>';
        tableHTML += '<td>' + payroll.period_label + '</td>';
        tableHTML += '<td>₱' + parseFloat(payroll.gross).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</td>';
        tableHTML += '<td>₱' + parseFloat(payroll.total_deductions).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</td>';
        tableHTML += '<td><strong>₱' + parseFloat(payroll.net_pay).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</strong></td>';
        tableHTML += '</tr>';
    });
    $('#payrollsModal table tbody').html(tableHTML);
}

// Update leaves UI
function updateLeavesUI(leaves) {
    var leaveHTML = '';
    leaves.forEach(function(leave) {
        var status_class = leave.status === 'Approved' ? 'success' : (leave.status === 'Disapproved' ? 'danger' : 'warning');
        leaveHTML += '<div class="list-group-item p-3 border-bottom">';
        leaveHTML += '<div class="d-flex justify-content-between align-items-start">';
        leaveHTML += '<div class="flex-grow-1">';
        leaveHTML += '<h6 class="mb-1">' + (leave.leave_name || 'Leave Request') + '</h6>';
        leaveHTML += '<small class="text-muted">' + leave.date_filed + '</small>';
        leaveHTML += '</div>';
        leaveHTML += '<div class="text-right">';
        leaveHTML += '<span class="badge badge-' + status_class + '">' + leave.status + '</span>';
        leaveHTML += '</div>';
        leaveHTML += '</div>';
        leaveHTML += '</div>';
    });
    $('#leavesSection .list-group').html(leaveHTML);
}

  // Update employments UI
  function updateEmploymentsUI(employments) {
    var empHTML = '';
    if (!employments || employments.length === 0) {
      empHTML = '<div class="p-3 text-center text-muted">No employment history available.</div>';
      $('#employmentsSection .list-group').html(empHTML);
      return;
    }

    employments.forEach(function(emp) {
      var start = emp.employment_start ? emp.employment_start : 'N/A';
      var end = emp.employment_end ? emp.employment_end : (emp.employment_status == '1' ? 'Present' : 'N/A');
      var position = emp.designation ? emp.designation : (emp.position_title ? emp.position_title : 'Position');
      var dept = emp.dept_name ? emp.dept_name : (emp.dept_assigned ? emp.dept_assigned : 'Department');
      var status_label = emp.employment_status == '1' ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Ended</span>';

      empHTML += '<div class="list-group-item p-3 border-bottom">';
      empHTML += '<div class="d-flex justify-content-between align-items-start">';
      empHTML += '<div class="flex-grow-1">';
      empHTML += '<h6 class="mb-1">' + position + ' <small class="text-muted">(' + dept + ')</small></h6>';
      empHTML += '<small class="text-muted">' + start + ' — ' + end + '</small>';
      empHTML += '</div>';
      empHTML += '<div class="text-right">';
      empHTML += '<div class="mb-2">' + status_label + '</div>';
      empHTML += '<a class="btn btn-sm btn-outline-primary" href="employment.php?emp_id=' + emp.employee_id + '&employment_id=' + emp.employment_id + '">';
      empHTML += '<i class="fas fa-eye"></i> View';
      empHTML += '</a>';
      empHTML += '</div>';
      empHTML += '</div>';
      empHTML += '</div>';
    });

    $('#employmentsSection .list-group').html(empHTML);
  }

  

// Update payslips UI - now shows monthly payslips
function updatePayslipsUI(payslips, employee_id) {
    // Update card count
    $('.card-info .card-text').text('Download your payslips');
    
    // Load available years for payslip download
    loadPayslipYears();
}

// Load available years for payslip selection
function loadPayslipYears() {
    $.ajax({
        url: 'get_payslip_months.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'get_available_years' },
        success: function(response) {
            console.log('Payslip years response:', response);
            
            if (response.success && response.data && response.data.length > 0) {
                createPayslipYearSelector(response.data);
                // Load current year's payslips
                loadPayslipsByYear(response.data[0]);
            } else {
                console.warn('No payslip years found');
                var message = 'No payslips available';
                if (response.debug) {
                    message += ' (Employee ID: ' + response.debug.employee_id + ', Count: ' + response.debug.count + ')';
                }
                $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">' + message + '</td></tr>');
            }
        },
        error: function(err) {
            console.log('Error loading payslip years:', err);
            $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">Error loading payslips</td></tr>');
        }
    });
}

// Create year selector for payslips modal
function createPayslipYearSelector(years) {
    var headerHTML = '<div class="row mb-3">';
    headerHTML += '<div class="col-md-3">';
    headerHTML += '<label>Select Year:</label>';
    headerHTML += '<select id="payslipYearSelector" class="form-control form-control-sm" onchange="loadPayslipsByYear(this.value)">';
    headerHTML += '<option value="">-- Choose Year --</option>';
    
    years.forEach(function(year) {
        headerHTML += '<option value="' + year + '">' + year + '</option>';
    });
    
    headerHTML += '</select>';
    headerHTML += '</div>';
    headerHTML += '</div>';
    
    // Insert or replace year selector above the table
    var tableContainer = $('#payslipsModal .table-responsive');
    var existingSelector = tableContainer.find('.year-selector-container');
    
    if (existingSelector.length === 0) {
        tableContainer.prepend('<div class="year-selector-container p-3 border-bottom">' + headerHTML + '</div>');
    } else {
        existingSelector.html(headerHTML);
    }
}

// Load payslips for selected year (transformed to monthly view)
function loadPayslipsByYear(year) {
    if (!year) {
        $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">Please select a year</td></tr>');
        return;
    }
    
    $.ajax({
        url: 'get_payslip_months.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'fetch_months_by_year', year: year },
        success: function(response) {
            console.log('Payslips for year ' + year + ':', response);
            
            if (response.success && response.data && response.data.length > 0) {
                // Check if any months have valid data
                var validMonths = response.data.filter(function(m) { return m.has_data && m.gross > 0; });
                
                if (validMonths.length > 0) {
                    populatePayslipsTable(response.data);
                } else {
                    console.warn('No valid payslip data found for ' + year);
                    $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">Payslips exist but financial data is unavailable for ' + year + '</td></tr>');
                }
            } else {
                console.warn('No payslips found for ' + year);
                $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">No payslips available for ' + year + '</td></tr>');
            }
        },
        error: function(err) {
            console.error('Error loading payslips for year:', err);
            $('#payslipsModal table tbody').html('<tr><td colspan="4" class="text-center text-muted">Error loading payslips. Please try again.</td></tr>');
        }
    });
}

// Populate payslips table with monthly data
function populatePayslipsTable(months) {
    var tableHTML = '';
    months.forEach(function(month) {
        // Format currency
        var grossFormatted = formatCurrency(month.gross);
        var netFormatted = formatCurrency(month.net_pay);
        
        // Determine if button should be enabled
        var hasValidData = month.has_data && month.gross > 0 && month.net_pay > 0;
        var buttonClass = hasValidData ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-secondary';
        var buttonDisabled = hasValidData ? '' : 'disabled';
        var buttonOnclick = hasValidData ? "downloadMonthlyPayslip('" + month.year + "', '" + month.month + "', '" + month.payroll_period + "')" : '';
        
        tableHTML += '<tr>';
        tableHTML += '<td>' + month.label + '</td>';
        tableHTML += '<td>₱' + grossFormatted + '</td>';
        tableHTML += '<td>';
        if (hasValidData) {
            tableHTML += '<strong>₱' + netFormatted + '</strong>';
        } else {
            tableHTML += '<span class="text-muted">—</span>';
        }
        tableHTML += '</td>';
        tableHTML += '<td>';
        
        if (hasValidData) {
            tableHTML += '<button class="' + buttonClass + '" onclick="' + buttonOnclick + '">';
            tableHTML += '<i class="fas fa-download"></i> Download';
            tableHTML += '</button>';
        } else {
            tableHTML += '<button class="' + buttonClass + '" ' + buttonDisabled + ' title="No payslip data available">';
            tableHTML += '<i class="fas fa-lock"></i> Locked';
            tableHTML += '</button>';
        }
        tableHTML += '</td>';
        tableHTML += '</tr>';
    });
    $('#payslipsModal table tbody').html(tableHTML);
}

// Show Payrolls Modal
function showPayrollsModal() {
    $('#payrollsModal').modal('show');
}

// Show Payslips Modal
function showPayslipsModal() {
    $('#payslipsModal').modal('show');
}

// Download Payslip - Monthly Version
function downloadPayslip(employee_id, year, date_start, date_end) {
    // Construct the payroll_period in the format expected: "YYYY-MM-DD_YYYY-MM-DD"
    var payroll_period = date_start + '_' + date_end;
    
    // Create a form to submit to print_payslip.php
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '../prints/print_payslip.php';
    form.target = '_blank';
    
    var input1 = document.createElement('input');
    input1.type = 'hidden';
    input1.name = 'employee_id';
    input1.value = employee_id;
    form.appendChild(input1);
    
    var input2 = document.createElement('input');
    input2.type = 'hidden';
    input2.name = 'year';
    input2.value = year;
    form.appendChild(input2);
    
    var input3 = document.createElement('input');
    input3.type = 'hidden';
    input3.name = 'payroll_period';
    input3.value = payroll_period;
    form.appendChild(input3);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Download Monthly Payslip (NEW - replaces downloadPayslip)
function downloadMonthlyPayslip(year, month, payroll_period) {
    var user_id = '<?php echo isset($user_id) ? $user_id : ""; ?>';
    
    if (!user_id) {
        alert('Error: User ID not found. Please refresh and try again.');
        return;
    }
    
    // Create a form to submit to print_payslip.php
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '../prints/print_payslip.php';
    form.target = '_blank';
    
    var input1 = document.createElement('input');
    input1.type = 'hidden';
    input1.name = 'user_id';
    input1.value = user_id;
    form.appendChild(input1);
    
    var input2 = document.createElement('input');
    input2.type = 'hidden';
    input2.name = 'year';
    input2.value = year;
    form.appendChild(input2);
    
    var input3 = document.createElement('input');
    input3.type = 'hidden';
    input3.name = 'payroll_period';
    input3.value = payroll_period;
    form.appendChild(input3);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Add hover effect to cards
document.querySelectorAll('.clickable-card').forEach(function(card) {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
        this.style.transition = 'all 0.3s ease';
    });
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
    });
});
</script>

<?php
    include_once '../includes/layout/footer.php';
?>
