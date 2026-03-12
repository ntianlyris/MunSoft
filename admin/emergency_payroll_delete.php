<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';

    // Restricted access check - only Manage System (Administrators) can access
    if (!$manage_system) {
        echo "<script>window.location.href='./';</script>";
        exit;
    }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Emergency Payroll Deletion</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
            <li class="breadcrumb-item active">Emergency Delete</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <div class="card card-outline card-danger">
        <div class="card-header">
          <h3 class="card-title">Filter Selection</h3>
        </div>
        <div class="card-body">
          <form id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label>Department</label>
                    <select class="form-control select2" id="dept_id" name="dept_id" required>
                        <option value="" selected disabled>Select Department...</option>
                        <?php echo ViewDepartmentsDropdown(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Payroll Period</label>
                    <select class="form-control select2" id="payroll_period_id" name="payroll_period_id" required>
                        <option value="" selected disabled>Select Period...</option>
                        <?php echo generatePayPeriodOptionsDropdown(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Employment Type</label>
                    <select class="form-control" id="emp_type_stamp" name="emp_type_stamp" required>
                        <option value="Regular">Regular</option>
                        <option value="Casual">Casual</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block btn-flat">
                        <i class="fas fa-search"></i> Show Records
                    </button>
                </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card card-outline card-secondary" id="resultsCard" style="display:none;">
        <div class="card-header d-flex p-0">
          <h3 class="card-title p-3">Payroll Records Found</h3>
          <ul class="nav nav-pills ml-auto p-2">
            <li class="nav-item">
                <button class="btn btn-danger btn-flat" id="btnDeleteAll" style="display:none;">
                    <i class="fas fa-trash-alt"></i> DELETE ALL MATCHING RECORDS
                </button>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="payrollTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>ID Num</th>
                  <th>Employee Name</th>
                  <th>Position</th>
                  <th>Gross</th>
                  <th>Deductions</th>
                  <th>Net Pay</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <!-- AJAX Content -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php $page_title = 'Emergency Payroll Deletion'; include_once '../includes/layout/appfooter.php'; ?>
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/sweetalert2/sweetalert2.all.min.js"></script>

<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<script src="scripts/emergency_delete.js"></script>

</body>
</html>
