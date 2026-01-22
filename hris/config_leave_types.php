<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Page Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Leave Types Setup</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Leave Types</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Add Button -->
        <div class="mb-3">
          <button class="btn btn-primary btn-flat" data-toggle="modal" data-target="#leaveTypeModal">
            <i class="fas fa-plus-circle"></i> Add Leave Type
          </button>
        </div>

        <!-- Leave Types Table -->
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> Leave Types</h3>
          </div>
          <div class="card-body">
            <table id="leaveTypesTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                    <th>No.</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Yearly Allotment</th>
                    <th>Accumulative</th>
                    <th>Monthly Accrual</th>
                    <th>Gender Restriction</th>
                    <th>Reset Policy</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php ViewLeaveTypes(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
</div>

<!-- Modal: Add/Edit Leave Type -->
<div class="modal fade" id="leaveTypeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="leaveTypeForm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Manage Leave Type</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="leave_type_id" id="leaveTypeId">
          
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Code</label>
              <input type="text" class="form-control" name="code" id="leave_code" placeholder="VL, SL, SPL" required>
            </div>
            <div class="form-group col-md-8">
              <label>Name</label>
              <input type="text" class="form-control" name="name" id="leave_name" placeholder="Vacation Leave" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Yearly Allotment</label>
              <input type="number" class="form-control" name="yearly_allotment" id="yearly_allotment" value="0" required>
            </div>
            <div class="form-group col-md-4">
              <label>Monthly Accrual</label>
              <select class="form-control" name="monthly_accrual" id="monthly_accrual">
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>Accumulative</label>
              <select class="form-control" name="is_accumulative" id="is_accumulative">
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Max Accumulation</label>
              <input type="number" class="form-control" name="max_accumulation" id="max_accumulation" placeholder="e.g., 300 or leave blank">
            </div>
            <div class="form-group col-md-4">
              <label>Gender Restriction</label>
              <select class="form-control" name="gender_restriction" id="gender_restriction">
                <option value="all">All</option>
                <option value="male">Male only</option>
                <option value="female">Female only</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>Reset Policy</label>
              <select class="form-control" name="reset_policy" id="reset_policy">
                <option value="reset">Reset yearly</option>
                <option value="carry_over">Carry Over</option>
                <option value="none">No Reset</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Requires Attachment?</label>
              <select class="form-control" name="requires_attachment" id="requires_attachment">
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>Status</label>
              <select class="form-control" name="active" id="active">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>Frequency Limit (Optional)</label>
              <input type="text" class="form-control" name="frequency_limit" id="frequency_limit" placeholder="e.g., 3 per year">
            </div>
          </div>
         
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="description" id="description" rows="2" placeholder="Description or notes about this leave type..."></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save changes</button>
          
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
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
<!-- System Engine -->
<script src="system.js"></script>
<script src="scripts/leave.js"></script>

<script>
$(function () {
    $(".select2").select2();

    let table = $("#leaveTypesTable").DataTable({
        "pageLength": 50,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "lengthChange": true,  // <-- enable dropdown for number of entries
        "responsive": true,
        "autoWidth": false,
        "buttons": ["excel", "pdf", "print"]
    });

    table.buttons().container().appendTo('#leaveTypesTable_wrapper .col-md-6:eq(0)');
});
</script>