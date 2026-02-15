<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/class/Employee.php';
    
    $Employee = new Employee();
    $employee_id = $Employee->getEmployeeIDByUserID($user_id);

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
        
        <!-- Mobile-First Grid Layout -->
        <div class="row">
          
          <!-- PROFILE CARD -->
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
            <div class="card card-primary card-outline h-100 clickable-card" style="cursor: pointer;" id="profile-card" onclick="window.location.href='profile.php?emp_id=<?php echo $employee_id; ?>'">
              <div class="card-body text-center py-4">
                <div class="mb-3">
                  <i class="fas fa-user fa-3x text-primary"></i>
                </div>
                <h5 class="card-title mb-2">My Profile</h5>
                <p class="card-text text-muted small mb-3">
                  View and edit your personal information
                </p>
                <div class="d-grid gap-2">
                  <button class="btn btn-primary btn-sm" type="button">
                    <i class="fas fa-arrow-right"></i> View Profile
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- PAYROLLS CARD -->
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
            <div class="card card-success card-outline h-100 clickable-card" style="cursor: pointer;" onclick="showPayrollsModal()">
              <div class="card-body text-center py-4">
                <div class="mb-3">
                  <i class="fas fa-file-invoice fa-3x text-success"></i>
                </div>
                <h5 class="card-title mb-2">Payrolls</h5>
                <p class="card-text text-muted small mb-3">
                  <?php echo $payroll_records ? count($payroll_records) : '0'; ?> recent payrolls
                </p>
                <div class="d-grid gap-2">
                  <button class="btn btn-success btn-sm" type="button">
                    <i class="fas fa-arrow-right"></i> View All
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- PAYSLIP CARD -->
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
            <div class="card card-info card-outline h-100 clickable-card" style="cursor: pointer;" onclick="showPayslipsModal()">
              <div class="card-body text-center py-4">
                <div class="mb-3">
                  <i class="fas fa-file-pdf fa-3x text-info"></i>
                </div>
                <h5 class="card-title mb-2">Payslips</h5>
                <p class="card-text text-muted small mb-3">
                  Download your payslips
                </p>
                <div class="d-grid gap-2">
                  <button class="btn btn-info btn-sm" type="button">
                    <i class="fas fa-arrow-right"></i> View All
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- LEAVE CARD -->
          <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
            <div class="card card-warning card-outline h-100 clickable-card" style="cursor: pointer;" onclick="window.location.href='leave_application.php'">
              <div class="card-body text-center py-4">
                <div class="mb-3">
                  <i class="fas fa-calendar-check fa-3x text-warning"></i>
                </div>
                <h5 class="card-title mb-2">Leave</h5>
                <p class="card-text text-muted small mb-3">
                  Manage your leave requests
                </p>
                <div class="d-grid gap-2">
                  <button class="btn btn-warning btn-sm" type="button">
                    <i class="fas fa-arrow-right"></i> Apply Leave
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
    loadDashboardData();
});

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

  

// Update payslips UI
function updatePayslipsUI(payslips, employee_id) {
    // Update card count
    $('.card-info .card-text').text('Download your payslips');
    
    // Update Payslips Modal table
    var tableHTML = '';
    payslips.forEach(function(payslip) {
        tableHTML += '<tr>';
        tableHTML += '<td>' + payslip.period_label + '</td>';
        tableHTML += '<td>₱' + parseFloat(payslip.gross).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</td>';
        tableHTML += '<td><strong>₱' + parseFloat(payslip.net_pay).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</strong></td>';
        tableHTML += '<td>';
        tableHTML += '<button class="btn btn-sm btn-primary" onclick="downloadPayslip(\'' + payslip.employee_id + '\', \'' + payslip.year + '\', \'' + payslip.period_label + '\')">';
        tableHTML += '<i class="fas fa-download"></i> Download';
        tableHTML += '</button>';
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

// Download Payslip
function downloadPayslip(employee_id, year, period_label) {
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
    input3.value = period_label;
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
