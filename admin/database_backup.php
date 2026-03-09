<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Database Backup & Restore</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
            <li class="breadcrumb-item active">Database Backup & Restore</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <div class="row">
          <!-- Backup Card -->
          <div class="col-md-6">
              <div class="card card-outline card-primary">
                  <div class="card-header">
                      <h3 class="card-title">Generate System Backup</h3>
                  </div>
                  <div class="card-body">
                      <p>
                          Generate a full SQL snapshot of the MunSoft database, including all configurations, user records, and payroll transactions. Generating a backup may take some time depending on database size.
                      </p>
                      <button id="btnGenerateBackup" class="btn btn-primary btn-flat">
                          <i class="fas fa-database"></i> Create New Backup
                      </button>
                      <div id="backupProgress" class="mt-3 text-primary font-weight-bold" style="display:none;">
                          <i class="fas fa-spinner fa-spin"></i> Generating SQL extraction stream... please do not refresh the page.
                      </div>
                  </div>
              </div>

              <!-- Restore Card -->
              <div class="card card-outline card-danger">
                  <div class="card-header">
                      <h3 class="card-title">Restore Database</h3>
                  </div>
                  <div class="card-body">
                      <p class="text-danger font-weight-bold">
                          Warning: Restoring the database will overwrite all existing data. Please generate a backup prior to performing this action.
                      </p>
                      <form id="restoreForm">
                          <div class="form-group">
                              <label>Select SQL Backup File</label>
                              <div class="custom-file">
                                  <input type="file" class="custom-file-input" id="backupFile" accept=".sql" required>
                                  <label class="custom-file-label" for="backupFile">Choose SQL File...</label>
                              </div>
                          </div>
                          <button type="submit" id="btnRestoreBackup" class="btn btn-danger btn-flat">
                              <i class="fas fa-upload"></i> Restore Backup
                          </button>
                      </form>
                      <div id="restoreProgress" class="mt-3 text-danger font-weight-bold" style="display:none;">
                          <i class="fas fa-spinner fa-spin"></i> Restoring database stream block... this may take several minutes.
                      </div>
                  </div>
              </div>
          </div>

          <!-- Backup History Card -->
          <div class="col-md-6">
              <div class="card card-outline card-success">
                  <div class="card-header">
                      <h3 class="card-title">Secured Backend Snapshots</h3>
                  </div>
                  <div class="card-body">
                      <div class="table-responsive">
                          <table class="table table-bordered table-striped">
                              <thead>
                                  <tr>
                                      <th>Filename</th>
                                      <th>Created At</th>
                                      <th>Size</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                              <tbody id="backupListTable">
                                  <!-- Rendered via JS Ajax inside scripts/backup.js -->
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>

    </div>
  </section>
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Scripts -->
<script src="scripts/backup.js"></script>

<script>
    // Update custom-file-input label to selected file
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>

<?php include_once '../includes/layout/footer.php'; ?>
