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
          <h1>Government Share Configurations</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
            <li class="breadcrumb-item active">Gov Share Setup</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- Form -->
      <div class="myButton mb-2">
        <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#govShareModal">
          <i class="fas fa-plus-circle"></i> Add Contribution
        </button>
      </div>

      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">Government Share Contribution List</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="govShareTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Type</th>
                  <th>Contribution</th>
                  <th>Code</th>
                  <th>Acct Code</th>
                  <th>Rate/Fixed Amount</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php ViewGovShares(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<!-- Modal -->
<div class="modal fade" id="govShareModal" tabindex="-1" role="dialog" aria-labelledby="govShareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <form id="govShareForm">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title" id="govShareModalLabel">Add Government Share Configuration</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="govshare_id" id="govshare_id">
            <div class="col-md-3">
              <label>Type</label>
              <select class="form-control" id="deduction_type" name="deduction_type" style="width: 100%;" required>
                <option value="" selected hidden>Select Type...</option>
                <?php echo ViewDeductionTypesDropdown(); ?>
              </select>
            </div>
            <div class="col-md-3">
              <label>Contribution Name</label>
              <input type="text" name="govshare_name" id="govshare_name" class="form-control" placeholder="ex. GSIS Life & Retirement Ins. (GS)" required>
            </div>
            <div class="col-md-3">
              <label>Code</label>
              <!--<input type="text" name="govshare_code" id="govshare_code" class="form-control" placeholder="ex. L_R" required>  *instead of input,use select box-->
              <select class="form-control" name="govshare_code" id="govshare_code" required>
                  <option value="" selected hidden>Select Code...</option>
                  <option value="L_R">L_R</option>
                  <option value="HDMF">HDMF</option>
                  <option value="PHIC">PHIC</option>
                  <option value="ECC">ECC</option>
              </select>
            </div>
            <div class="col-md-3">
              <label>Acct Code</label>
              <input type="text" name="govshare_acctcode" id="govshare_acctcode" class="form-control" placeholder="ex. 50103010" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label>Rate/Fixed Amount</label>
              <input type="number" step="0.01" name="govshare_rate" id="govshare_rate" class="form-control">
              <small><i>*Enter amount if GS is fixed amount.</i></small>
            </div>
            <div class="col-md-6">
              <label>Calculation Type</label>
              <select name="is_percentage" id="is_percentage" class="form-control" required>
                <option value="1">Percentage (%)</option>
                <option value="0">Fixed Amount (₱)</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
          <button type="submit" class="btn btn-primary btn-flat"><i class="fas fa-save"></i> Save Contribution</button>
        </div>
      </div>
    </form>
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
<!-- Employees Engine -->
<script src="scripts/govshares.js"></script>

<script>
$(function () {
    $("#govShareTable").DataTable({
        "pageLength": 50,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "responsive": false, 
        "lengthChange": true,  // <-- enable dropdown for number of entries
        "autoWidth": false,
        "paging": true,
        "searching": true,
        "scrollX": true,
        "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#govShareTable_wrapper .col-md-6:eq(0)');
});
</script>
<script>

</script>
</body>
</html>

