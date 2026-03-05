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
          <h1>Payroll Records</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
            <li class="breadcrumb-item active">Payroll Records</li>
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
          <div class="card card-outline card-primary">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0 mr-3">Payroll Data</h3>
                <label for="pay_frequency" class="mr-2 mb-0">Pay Frequency:</label>
                <select id="pay_frequency" class="form-control form-control-sm mr-3" readonly disabled style="width: 180px;">
                    <?php echo ViewPayFrequenciesDropdown(); ?>
                </select>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <form id="payrollComputeForm" class="form-inline">
                  
                  <label for="payrollYearDropdown" class="mr-2">Payroll Year:</label>
                  <select id="payrollYearDropdown" class="form-control form-control-sm mr-3">
                    <option value="" selected hidden>Select Year...</option>
                    <?php echo ViewPayPeriodYearsDropdown(); ?>
                  </select>
                  <label for="payrollPeriodYearDropdown" class="mr-2">Pay Period:</label>
                  <select id="payrollPeriodYearDropdown" class="form-control form-control-sm mr-3">
                    <option value="" selected hidden>Select Pay Period...</option>
                    
                    <!-- Dynamically populated -->
                  </select>
                  <label for="department" class="mr-2">Department:</label>
                  <select id="department" class="form-control form-control-sm mr-3">
                    <option value="" selected hidden>Select Department...</option>
                    <?php echo ViewDepartmentsDropdown(); ?>
                  </select>
                  <label for="employment_type" class="mr-2">Type:</label>
                  <select id="employment_type" class="form-control form-control-sm mr-3">
                    <option value="Regular" selected>Regular</option>
                    <option value="Casual">Casual</option>
                  </select>

                  <button type="button" class="btn btn-primary btn-sm" onclick="RetrievePayroll()"><i class="fas fa-sync-alt"></i> Retrieve Payroll</button>
                </form>
              </div>

              <!-- Payroll Action Buttons -->
              <div class="text-right mb-2">
                <button id="printPayrollBtn" class="btn btn-success d-none">
                  <i class="fas fa-print"></i> Print Payroll
                </button>
                <button id="deletePayrollRecordsBtn" class="btn btn-danger d-none ml-2" onclick="deleteAllPayrollRecords()">
                  <i class="fas fa-trash"></i> Delete Payroll Records
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
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Dynamic payroll data -->
              
                </tbody>
              </table>
            </div>
          </div>
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

<!-- Edit Payroll Modal -->
<div id="editPayrollModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editPayrollModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPayrollModalLabel">Edit Payroll Entry</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editPayrollId">
        <div class="form-group">
          <label for="editEmployeeName">Employee:</label>
          <input type="text" class="form-control" id="editEmployeeName" readonly>
        </div>
        <div class="form-group">
          <label for="editGross">Gross Pay:</label>
          <input type="number" class="form-control" id="editGross" step="0.01" min="0">
        </div>
        <div class="form-group">
          <label for="editDeductions">Total Deductions:</label>
          <input type="number" class="form-control" id="editDeductions" step="0.01" min="0">
        </div>
        <div class="form-group">
          <label for="editNetPay">Net Pay:</label>
          <input type="number" class="form-control" id="editNetPay" step="0.01" min="0" readonly>
        </div>
        <div class="form-group">
          <label for="editStatus">Status:</label>
          <input type="text" class="form-control" id="editStatus" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveEditedPayroll()">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Auto-calculate Net Pay -->
<script>
$(document).ready(function() {
  $('#editGross, #editDeductions').on('change', function() {
    const gross = parseFloat($('#editGross').val()) || 0;
    const deductions = parseFloat($('#editDeductions').val()) || 0;
    const net = gross - deductions;
    $('#editNetPay').val(net.toFixed(2));
  });
});
</script>

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

