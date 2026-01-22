<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Leave Applications</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Leave Applications</li>
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

            <!-- Leave Records Table -->
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Employees Leave Applications</h3>
              </div>
              <div class="card-body">
                <table id="leaveTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="text-center">No.</th>
                      <th class="text-center">Employee</th>
                      <th class="text-center">Leave Type</th>
                      <th class="text-center">Date Filed</th>
                      <th class="text-center">Inclusive Dates</th>
                      <th class="text-center">No. of Days</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo ViewAllLeaveApplications(); ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>

      

    </section>
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/select2/js/select2.full.min.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="../dist/js/adminlte.js"></script>
<script src="../dist/js/demo.js"></script>
<script src="system.js"></script>
<script src="scripts/leave.js"></script>
<script>
    $(document).ready(function() {
        // Add new date row
        $('#add-date').click(function() {
            let newRow = `
            <div class="input-group mb-2 date-row">
                <input type="date" name="inclusive_dates[]" class="form-control" required>
                <div class="input-group-append">
                <button class="btn btn-danger remove-date" type="button"><i class="fas fa-times"></i></button>
                </div>
            </div>
            `;
            $('#date-container').append(newRow);
        });

        // Remove date row
        $(document).on('click', '.remove-date', function() {
            $(this).closest('.date-row').remove();
        });
    });
</script>

<script>
  $(function () {
    // Initialize DataTable
    $("#leaveTable").DataTable({
      "responsive": true, 
      "lengthChange": true, 
      "pageLength": 50,
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "autoWidth": false,
      "paging": true,
      "searching": true,
      "scrollX": true
    });

    // Initialize Select2
    $('.select2').select2();
  });
</script>
</body>
</html>