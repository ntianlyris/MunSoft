<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Payrolls</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
            <li class="breadcrumb-item active">Payrolls</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-primary card-outline card-tabs">
            <div class="card-header d-flex align-items-center">
                <ul class="nav nav-tabs card-header-tabs" id="payrollTabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link payroll-link active" id="tab-compute" data-toggle="tab" href="#pane-compute" role="tab" aria-controls="pane-compute" aria-selected="true">
                      Payroll Computation
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link payroll-link" id="tab-masterlist" data-toggle="tab" href="#pane-masterlist" role="tab" aria-controls="pane-masterlist" aria-selected="false">
                      Payroll Masterlist
                    </a>
                  </li>
                </ul>
            </div>

            <div class="card-body">
              <div class="tab-content mt-3" id="payrollTabsContent">
                
                <!-- Payroll Computation Pane -->
                <div class="tab-pane fade show active" id="pane-compute" role="tabpanel" aria-labelledby="tab-compute"> 
                  <!-- existing computation UI -->
                  <div class="mb-3">
                    <form id="payrollComputeForm" class="form-inline">
                      <label for="pay_frequency" class="mr-2 mb-0">Pay Frequency:</label>
                      <select id="pay_frequency" class="form-control form-control-sm mr-3" readonly disabled style="width: 180px;">
                        <?php echo ViewPayFrequenciesDropdown(); ?>
                      </select>
                      <label for="pay_period" class="mr-2">Pay Period:</label>
                      <select id="pay_period" class="form-control form-control-sm mr-3">
                        <option value="" selected hidden>Select Pay Period...</option>
                        <?php echo generatePayPeriodOptionsDropdown(); ?>
                      </select>
                      <label for="department" class="mr-2">Department:</label>
                      <select id="department" class="form-control form-control-sm mr-3">
                        <option value="" selected hidden>Select department...</option>
                        <?php echo ViewDepartmentsDropdown(); ?>
                      </select>
                      <label for="employment_type" class="mr-2">Type:</label>
                      <select id="employment_type" class="form-control form-control-sm mr-3">
                        <option value="Regular" selected>Regular</option>
                        <option value="Casual">Casual</option>
                      </select>

                      <button type="button" class="btn btn-primary btn-sm" onclick="computePayroll()"><i class="fas fa-play"></i> Generate Payroll</button>
                    </form>
                  </div>

                  <!-- Save Payroll Button -->
                  <div class="text-right mb-2">
                    <button id="savePayrollBtn" class="btn btn-success d-none">
                      <i class="fas fa-save"></i> Save Payroll
                    </button>
                  </div>

                  <table id="payrollTable" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center">EIDN</th>
                        <th class="text-center">Employee</th>
                        <th class="text-center">Position</th>
                        <th class="text-center">Earnings</th>
                        <th class="text-center">Deductions</th>
                        <th class="text-center">Net Pay</th>
                        <th class="text-center">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Dynamic payroll data -->
                    </tbody>
                  </table>
                </div>

                <!-- Payroll Masterlist Pane -->
                <div class="tab-pane fade" id="pane-masterlist" role="tabpanel" aria-labelledby="tab-masterlist">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <button id="selectAllEmployees" class="btn btn-sm btn-outline-primary">Select All</button>
                      <button id="includeSelected" class="btn btn-sm btn-primary">Include Selected</button>
                    </div>
                  </div>

                  <table id="payrollMasterlistTable" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">EIDN</th>
                        <th class="text-center">Employee</th>
                        <th class="text-center">Department</th>
                        <th class="text-center">Position</th>
                        <th class="text-center">Include to Payroll</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo ViewPayrollMasterlistEmployees(); ?>
                    </tbody>
                  </table>
                </div>
              </div> <!-- .tab-content -->
            </div> <!-- .card-body -->
          </div> <!-- .card -->
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Payroll Breakdown Modal -->
<?php ShowPayrollBreakdownModal(); ?>

<!-- Loader Overlay -->
<div id="Loader" style="display: none;">
  <div class="spinner text-primary" role="status" style="width: 3rem; height: 3rem;">
    <span class="sr-only">Loading...</span>
  </div>
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- System Engine -->
<script src="system.js"></script>
<!-- Payrolls Engine -->
<script src="scripts/payrolls.js"></script>

<!-- GAA Net Pay Validator Module -->
<script src="gaa_validator.js"></script>

<!-- Clear the input fields on form when adding new entry -->
<script>
    // Ensure the modal has the correct ID
    $('#config_earnings_modal').on('show.bs.modal', function () {
      // Reset the form when the modal is shown
      $('#formEarningConfig')[0].reset();
      // Explicitly reset the hidden field (if needed)
      $('#txtConfigEarningID').val(''); // Clears the hidden input value
    });
</script>
<script>
  $(function(){
    var $link = $('.payroll-link[href="#pane-compute"]');
    if ($link.length) {
      // prefer Bootstrap's tab show if available, otherwise simulate click and set ARIA/classes
      if (typeof $link.tab === 'function') {
        $link.tab('show');
      } else {
        $link.trigger('click');
        $link.addClass('active').attr('aria-selected','true');
        $('.payroll-link').not($link).removeClass('active').attr('aria-selected','false');
      }
    }
  });
</script>
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": false, 
      "lengthChange": false, 
      "autoWidth": false,
      "paging": true,
      "searching": true,
      "scrollX": true,
      "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</html>

