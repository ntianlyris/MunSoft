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
            <h1>Signatories</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Signatories</li>
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
            <div class="myButton mb-2">
              <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#signatory_modal">
                <i class="fas fa-plus-circle"></i> Add Signatory
              </button>
            </div>

            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Signatories List</h3>
              </div>
              <div class="card-body">
                <table id="tbl_signatories" class="table table-bordered table-striped table-hover" width="100%">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th class="text-center">Full Name</th>
                      <th class="text-center">Position Title</th>
                      <th class="text-center">Role</th>
                      <th class="text-center">Department</th>
                      <th class="text-center">Report Type</th>
                      <th class="text-center">Order</th>
                      <th class="text-center">Particulars</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo ViewSignatoriesList(); ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Signatory Modal -->
      <div class="modal fade" id="signatory_modal" tabindex="-1" role="dialog" aria-labelledby="signatoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <form id="signatoryForm">
              <div class="modal-header bg-primary">
                <h5 class="modal-title" id="signatoryModalLabel">Add Signatory</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-body">
                <div class="row">
                  <input type="hidden" name="signatory_id" id="signatory_id">
                  <div class="col-md-6">
                    <label>Employee</label>
                    <input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="e.g. Juan Dela Cruz, CPA">
                  </div>
                  <div class="col-md-6">
                    <label>Position Title</label>
                    <input type="text" name="position_title" id="position_title" class="form-control" placeholder="e.g. Municipal Accountant">
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="col-md-6">
                    <label>Role</label>
                    <select name="role_type" id="role_type" class="form-control select2" style="width:100%;">
                      <option value="" selected hidden>-- Select Role --</option>
                      <option value="HEAD">Department Head</option>
                      <option value="MAYOR">Municipal Mayor</option>
                      <option value="TREASURER">Municipal Treasurer</option>
                      <option value="ACCOUNTANT">Municipal Accountant</option>
                      <option value="DISBURSING">Disbursing Officer</option>
                      <option value="PREPARATION">Preparation</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label>Department</label>
                    <select name="dept_id" id="dept_id" class="form-control select2" style="width:100%;">
                      <option value="0" selected hidden>-- Optional --</option>
                      <?php echo ViewDepartmentsDropdown(); ?>
                    </select>
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-md-6">
                    <label>Report Type</label>
                    <select name="report_code" id="report_code" class="form-control select2" style="width:100%;">
                      <option hidden selected value="">-- Select Report --</option>
                      <option value="PAYROLL">Payroll</option>
                      <option value="REMITTANCE">Remittance</option>
                      <option value="JOURNAL">Journal Entry</option>
                      <option value="ACCTG">Accounting Reports</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label>Signatory Order</label>
                    <select name="sign_order" id="sign_order" class="form-control select2" style="width:100%;">
                      <option hidden selected value="">-- Select Order --</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                    </select>
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-md-8">
                    <label>Signatory Particulars</label>
                    <textarea name="sign_particulars" id=sign_particulars class="form-control" rows="3"></textarea>
                  </div>
                  <div class="col-md-3 text-right">
                    <label>Active Status</label><br>
                    <!--<input type="checkbox" id="is_active" name="is_active" checked>-->
                    <input type="checkbox" id="is_active" name="is_active" data-bootstrap-switch data-off-color="danger" data-on-color="success" checked>
                  </div>
                </div>
              </div>

              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Signatory</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- /.modal -->

    </section>
  </div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap Switch -->
<script src="../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- DataTables -->
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
<!-- AdminLTE -->
<script src="../dist/js/adminlte.js"></script>
<!-- System -->
<script src="system.js"></script>
<!-- Signatories Script -->
<script src="scripts/signatories.js"></script>

<script>
  $(function () {
    $('.select2').select2();
    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
  });
</script>
</body>
</html>

