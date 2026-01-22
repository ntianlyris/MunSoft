<?php
include_once '../includes/layout/head.php';
include_once '../includes/layout/navbar.php';
include_once '../includes/layout/sidebar.php';
include_once '../includes/view/view.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Page Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Manage Leave Balances</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Leave Balances</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Page Content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Employee + Year Filter -->
        <div class="card card-outline card-primary">
          <div class="card-body">
            <form class="form-inline">
              <label class="mr-2">Select Employee:</label>
              <select id="employeeSelect" class="form-control form-control-sm select2" name="employee_id" style="width:250px;">
                <option selected hidden value="">-- Choose Employee --</option>
                <!-- dynamically load employees -->
                <?php echo ViewEmployeesDropdown(); ?>
              </select>

              <label class="ml-3 mr-2">Year:</label>
              <select id="yearSelect" class="form-control form-control-sm" name="year">
                <?php 
                  $year = date("Y");
                  for($i=$year; $i>=$year-5; $i--){
                      echo "<option value='$i'>$i</option>";
                  }
                ?>
              </select>

              <button type="button" id="loadBalances" class="btn btn-primary btn-sm ml-3">
                <i class="fas fa-search"></i> Load Balances
              </button>
              <button type="button" id="initBalances" class="btn btn-success btn-sm ml-2 pull-right">
                <i class="fas fa-sync-alt"></i> Initialize Leave Balances
              </button>
            </form>
          </div>
        </div>

        <!-- Balances Table -->
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Leave Balances</h3>
          </div>
          <div class="card-body">
            <table id="leaveBalancesTable" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>Leave Type</th>
                  <th>Yearly Allotment</th>
                  <th>Accumulated</th>
                  <th>Used</th>
                  <th>Remaining</th>
                  <th style="width: 200px;">Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- data loaded via AJAX -->
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </section>
</div>

<!-- Adjustment Modal -->
<div class="modal fade" id="adjustBalanceModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="adjustBalanceForm">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Adjust Leave Balance</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="employee_id" id="adjEmployeeId">
          <input type="hidden" name="leave_type_id" id="adjLeaveTypeId">

          <div class="form-group">
            <label>Leave Type</label>
            <input type="text" class="form-control" id="adjLeaveTypeName" readonly>
          </div>

          <div class="form-group">
            <label>Current Balance</label>
            <input type="text" class="form-control" id="adjCurrentBalance" readonly>
          </div>

          <div class="form-group">
            <label>Adjustment (+/-)</label>
            <input type="number" name="adjustment" class="form-control" placeholder="e.g., +2 or -1" required>
          </div>

          <div class="form-group">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Adjustment</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title">Leave History</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table id="historyTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Date</th>
              <th>Leave Type</th>
              <th>Days Used</th>
              <th>Balance Before</th>
              <th>Balance After</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <!-- data loaded dynamically -->
          </tbody>
        </table>
      </div>
    </div>
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
<script src="scripts/leave.js"></script>
<!-- JS -->
 <script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
  });
</script>
<script>
$(function(){

  // Initialize DataTables
  $("#leaveBalancesTable").DataTable({
    paging: false,
    searching: false,
    info: false
  });

  // Load employee balances (AJAX)
  $("#loadBalances").click(function(){
    let empId = $("#employeeSelect").val();
    let year = $("#yearSelect").val();
    if(!empId){
      Swal.fire({
        icon: 'warning',
        title: 'No Employee Selected',
        text: 'Please select an employee first.'
      });
      return;
    }
    $.ajax({
      url: 'leave_handler.php',
      type: 'POST',
      data: {
        action: 'fetch_employee_balances',
        employee_id: empId,
        year: year
      },
      dataType: 'json',
      success: function(response) {
        let tbody = '';
        if(response.status === "success" && response.data && response.data.length > 0){
          response.data.forEach(function(row){
            tbody += `<tr>
              <td>${row.leave_name}</td>
              <td>${row.yearly_allotment}</td>
              <td>${row.accumulated}</td>
              <td>${row.used}</td>
              <td>${row.remaining}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-warning adjust-btn" 
                  data-id="${row.leave_type_id}" 
                  data-name="${row.leave_name}" 
                  data-balance="${row.remaining}">
                  <i class="fas fa-edit"></i> 
                    Adjust
                </button>
                <button class="btn btn-sm btn-info history-btn" 
                  data-id="${row.leave_type_id}" 
                  data-name="${row.leave_name}">
                  <i class="fas fa-history"></i> 
                    History
                </button>
              </td>
            </tr>`;
          });
        } else {
          tbody = `<tr><td colspan="6" class="text-center">No leave balances found.</td></tr>`;
        }
        $("#leaveBalancesTable tbody").html(tbody);
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to load leave balances.'
        });
      }
    });
  });

  // Open adjustment modal
  $(document).on("click", ".adjust-btn", function(){
    let leaveId = $(this).data("id");
    let leaveName = $(this).data("name");
    let balance = $(this).data("balance");

    $("#adjLeaveTypeId").val(leaveId);
    $("#adjLeaveTypeName").val(leaveName);
    $("#adjCurrentBalance").val(balance);

    $("#adjustBalanceModal").modal("show");
  });

  // Open history modal
  $(document).on("click", ".history-btn", function(){
    $("#historyModal").modal("show");
    // TODO: AJAX call to fetch history
  });

});
</script>

</body>
</html>