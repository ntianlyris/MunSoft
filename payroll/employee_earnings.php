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
            <h1>Earnings</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Earnings</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="myButton">
                <button type="button" class="btn btn-primary btn-flat"  data-toggle="modal" data-target="#employee_earnings_modal"><i class="fas fa-plus-circle"></i> Add Employee Earnings</button>
            </div>
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Employee Earnings</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">EIDN</th>
                            <th class="text-center">Employee</th>
                            <th class="text-center">Effectivity Date</th>
                            <th class="text-center">Gross Pay</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php ViewAllEmployeesEarnings(); ?>
                    </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->

      <!-- Modals -->
      <?php ShowEmployeeEarningsModal(); ?>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

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
<!-- Employees Engine -->
<script src="scripts/earnings.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
  });
</script>
<script>
  $('#employee_earnings_modal').on('show.bs.modal', function () {
    // 1. Reset all static form fields
    $('#formEmployeeEarning')[0].reset();

    // 2. Clear all hidden input fields (just in case)
    $('#formEmployeeEarning').find('input[type="hidden"]').val('');

    // 3. Remove all dynamically injected earning code inputs
    $('#earningCodesInputs').empty();

    // 4. Reset dropdowns if you're using any (e.g. select2)
    $('#formEmployeeEarning select').val('').trigger('change');

    // 5. Remove validation states and messages (optional)
    $('#formEmployeeEarning .is-invalid').removeClass('is-invalid');
    $('#formEmployeeEarning .invalid-feedback').remove();

    // Re-initialize Select2
    $('#cmbEmployee').select2({
        dropdownParent: $('#employee_earnings_modal'),
        width: '100%' // optional: ensure full width inside modal
    });
    $('#cmbEmployee').prop('disabled',false);
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

