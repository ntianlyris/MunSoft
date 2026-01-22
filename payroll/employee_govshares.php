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
          <h1>Employee Government Share Setup</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
            <li class="breadcrumb-item active">Employee Government Share</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- Employee Government Share Form -->
      <div class="card card-outline card-success">
        <div class="card-header">
          <h3 class="card-title">Setup Employee Government Share</h3>
        </div>
        <div class="card-body">
          <form id="govShareFormEmployee">
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="employee_id">Select Employee</label>
                <select name="employee_id" id="employee_id" class="form-control select2" style="width: 100%;" required>
                  <option value="">-- Select Employee --</option>
                  <?php echo ViewEmployedEmployeesWithEarningsDropdown(); ?>
                  <!-- This should be populated from the database in production -->
                </select>
              </div>
              <div class="col-md-6">
                <label for="monthly_rate">Monthly Rate</label>
                <input type="text" class="form-control" id="monthly_rate" name="monthly_rate" placeholder="0.00" readonly required>
              </div>
            </div>

            <hr>

            <!-- Government Share Computation -->
            <div class="row" id="govshare-container">
              <!-- Govshare rows will be injected here dynamically -->
            </div>

            <div class="mt-4 text-right">
              <button type="submit" name="save" value="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Government Shares
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Table of Government Share Records -->
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">Employee Government Share Records</h3>
        </div>
        <div class="card-body">
          <?php ViewEmployeeGovShareRecords(); ?>
        </div>
      </div>

    </div>
  </section>
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

<!-- Govshare Engine -->
<script src="scripts/govshares.js"></script>

<script>
  $(function () {
    // Initialize DataTable
    $('#govShareTable').DataTable({
      pageLength: -1,
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

  });
</script>
</body>
</html>

