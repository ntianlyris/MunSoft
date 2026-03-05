<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
    include_once '../includes/class/Employee.php';

    $employee_id = "";
    if (isset($_GET['emp_id']) && !empty($_GET['emp_id'])) {
      $employee_id = $_GET['emp_id'];
    }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Employment Details</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="profile.php?emp_id=<?php echo $employee_id; ?>">Profile</a></li>
              <li class="breadcrumb-item active">Employment</li>
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
            <div class="card card-primary card-outline">
              <div class="card-header" style="width: 100%;">
                <div class="row">
                  <div class="col-6">
                    <h5 class="card-title m-0">Employment Record</h5>
                  </div>
                  <div class="col-6">
                    <button type="button" class="btn btn-primary btn-flat btn-sm" style="float:right;" onclick="GetEmployeeDetailsForEmployment(<?php echo $employee_id; ?>)" data-toggle="modal" data-target="#employment_modal"><i class="fas fa-plus-circle"></i> Add Employment</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                  
                  <table id="employmentTable" class="table table-bordered table-hover">
                      <thead>
                          <tr>
                              <th class="text-center">No.</th>
                              <th class="text-center">Reference No.</th>
                              <th class="text-center">Position</th>
                              <th class="text-center">Employment Type</th>
                              <th class="text-center">Start of Employment</th>
                              <th class="text-center">End of Employment</th>
                              <th class="text-center">Department</th>
                              <th class="text-center">Office Assignment</th>
                              <th class="text-center">Designation</th>
                              <th class="text-center">Particulars</th>
                              <th class="text-center">Rate</th>
                              <th class="text-center">Status</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php ViewEmployeeEmployments($employee_id); ?>
                      </tbody>
                  </table>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->

      <!-- Modals -->
      <?php ShowEmploymentModal(); ?>
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
<!-- Employees Engine -->
<script src="../payroll/scripts/employees.js"></script>
<script src="system.js"></script>

<script type="text/javascript">
    $("#cmbEmpType").change(function(){
        var employment_type = $("#cmbEmpType").find('option:selected').val(); 
        if(employment_type == "Regular" || employment_type == "Casual"){
          $('#cmbPosition').removeAttr('disabled');
          $("#cmbDepartment").removeAttr('disabled');
        }
        else{
          $("#cmbPosition").val("");
          $("#cmbDepartment").val("");
          $("#cmbPosition").attr('disabled','disabled'); 
          $("#cmbDepartment").attr('disabled','disabled'); 
        }
    });
</script>
<script type="text/javascript">
    $("#cmbPosition").change(function(){
        var position_item_id = $("#cmbPosition").find('option:selected').val(); 
        var action = 'get_position_details';

        $.ajax({
            type: "GET",
            url: "get.php",
            data: {"action" : action, "position_id" : position_item_id},
            success: function(data) {
                    var obj = $.parseJSON(data);
                    $('#cmbDepartment').val(obj['dept_id']);
            }
        });
    });
</script>
<script>
  $(function () {
    $("#employmentTable").DataTable({
      "responsive": false, 
      "lengthChange": false, 
      "autoWidth": false,
      "paging": true,
      "searching": true,
      "scrollX": true,
      "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#employmentTable_wrapper .col-md-6:eq(0)');
  });
</script>

<script>
  // If an employment_id is provided in the query string, open and highlight it
  $(document).ready(function(){
    var highlightEmploymentId = <?php echo isset($_GET['employment_id']) && is_numeric($_GET['employment_id']) ? intval($_GET['employment_id']) : 'null'; ?>;
    var employeeId = <?php echo $employee_id ? $employee_id : 'null'; ?>;
    if (highlightEmploymentId && employeeId) {
      // Wait a tick to ensure DataTable has rendered
      setTimeout(function(){
        // Open modal with details
        if (typeof GetEmploymentDetails === 'function') {
          GetEmploymentDetails(employeeId, highlightEmploymentId);
        }

        // Scroll to and highlight the row if present
        var row = $('#employment_row_' + highlightEmploymentId);
        if (row.length) {
          $('html, body').animate({scrollTop: row.offset().top - 120}, 600);
          row.addClass('table-info');
          setTimeout(function(){ row.removeClass('table-info'); }, 4000);
        }
      }, 300);
    }
  });
</script>

</body>
</html>
