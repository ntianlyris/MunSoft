<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';

    $employee_id = "";
    if (isset($_GET['emp_id'])) {
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
            <h1>Employee Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-2">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="../includes/images/avatar.jpg"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><span id="lblDisplayName"></span></h3>

                <p class="text-muted text-center"><span id="lblProfExpertise">Software Engineer</p>
                <div class="text-center">
                  <button class="btn btn-primary btn-sm btn-flat"><i class="fas fa-upload"></i> Upload Photo</button>
                </div>
                <hr>
                <span>Username</span>
                <p><strong id="lblEmployeeUser"></strong></p>
                <hr>
                <span>Mobile</span>
                <p><strong id="lblEmployeeMobile"></strong></p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

           <!-- /.col -->
           <div class="col-12 col-md-10">
            <div class="card card-primary card-outline card-tabs">
              <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Employee Details</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-three-qualification-tab" data-toggle="pill" href="#custom-tabs-three-qualification" role="tab" aria-controls="custom-tabs-three-qualification" aria-selected="false">Qualification</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Profile</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill" href="#custom-tabs-three-settings" role="tab" aria-controls="custom-tabs-three-settings" aria-selected="false">Settings</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                    <div class="row">
                      <div class="col-sm-3">
                        <span>Employee ID No. (EIDN)</span>
                        <p><h5 id="lblEmployeeID"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Date Hired</span>
                        <p><h5 id="lblDateHired"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Status</span>
                        <p><h5 id="lblEmployeeStatus"></h5></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                        <span>Lastname</span>
                        <p><h5 id="lblLastname"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Firstname</span>
                        <p><h5 id="lblFirstname"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Middlename</span>
                        <p><h5 id="lblMiddlename"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Extension (Sr., Jr., etc.)</span>
                        <p><h5 id="lblExtension"></h5></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                        <span>Birthdate</span>
                        <p><h5 id="lblBirthdate"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Age</span>
                        <p><h5 id="lblAge"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Gender</span>
                        <p><h5 id="lblGender"></h5></p>
                      </div>
                      <div class="col-sm-3">
                        <span>Civil Status</span>
                        <p><h5 id="lblCivilStatus"></h5></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <span>Address</span>
                        <p><h5 id="lblAddress"></h5></p>
                      </div>
                    </div>
                    <hr>
                                <div class="text-right">
                                  <button class="btn btn-primary btn-flat" onclick="GetEmployeeDetails(<?php echo $employee_id; ?>)" data-toggle="modal" data-target="#employee_modal">
                                      <i class="fa fa-edit"></i> Edit
                                  </button>
                                  <button class="btn btn-danger btn-flat" onclick="DeleteEmployee(<?php echo $employee_id; ?>)" data-toggle="tooltip" title="Delete" data-placement="bottom">
                                      <i class="fa fa-times"></i> Delete
                                  </button>
                                </div>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-three-qualification" role="tabpanel" aria-labelledby="custom-tabs-three-qualification-tab">
                    
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                     Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-three-settings" role="tabpanel" aria-labelledby="custom-tabs-three-settings-tab">
                     Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                  </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
          <!-- /.col -->
          <div class="col-12">
            <div class="card card-primary card-outline">
              <div class="card-header" style="width: 100%;">
                <div class="row">
                  <div class="col-6">
                    Employment History
                  </div>
                  <div class="col-6 text-right">
                    <!-- Print Button -->
                    <button type="button" class="btn btn-success btn-flat btn-sm" onclick="window.open('../prints/print_service_record.php?employee_id=<?php echo $employee_id; ?>', '_blank')">
                      <i class="fas fa-print"></i> Print Service Record
                    </button>

                    <!-- Add Employment Button -->
                    <button type="button" class="btn btn-primary btn-flat btn-sm" onclick="GetEmployeeDetailsForEmployment(<?php echo $employee_id; ?>)" data-toggle="modal" data-target="#employment_modal">
                      <i class="fas fa-plus-circle"></i> Add Employment
                    </button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                  
                  <table id="tbl_employments" class="table table-bordered table-hover">
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
                              <th class="text-center">Work Nature</th>
                              <th class="text-center">Work Specifics</th>
                              <th class="text-center">Rate</th>
                              <th class="text-center">Status</th>
                              <th class="text-center">Action</th>
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
      <?php ShowEmployeeModal(); ?>
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
<!-- Moment -->
<script src="../plugins/moment/moment.min.js"></script>
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
<script src="scripts/employees.js"></script>
<!-- Clear the input fields on form when adding new entry -->
<script>
    // Ensure the modal has the correct ID
    $('#employment_modal').on('show.bs.modal', function () {
      // Reset the form when the modal is shown
      $('#formEmployment')[0].reset();
      // Explicitly reset the hidden field (if needed)
      $('#txtEmploymentID').val(''); // Clears the hidden input value
    });
</script>
<script type="text/javascript">
  $( document ).ready(function(){
    var query = getQuery();
        var employee_id = query.emp_id;
        var action = 'get_employee_details';
        var action1 = 'get_employee_user';
        $.ajax({
            type: "GET",
            url: "get.php",
            data: {"action" : action, "employee_id" : employee_id},
            success: function(data) {
                    var obj = $.parseJSON(data);
                    $('#lblEmployeeID').html(obj['employee_id_num']);
                    $('#lblDateHired').html(moment(obj['hire_date']).format('MM/DD/YYYY'));
                    $('#lblEmployeeStatus').html(obj['employee_status']);
                    $('#lblDisplayName').html(obj['firstname'] + " " + obj['lastname']);
                    $('#lblLastname').html(obj['lastname']);
                    $('#lblFirstname').html(obj['firstname']);
                    $('#lblMiddlename').html(obj['middlename']);
                    $('#lblExtension').html(obj['extension']);
                    $('#lblBirthdate').html(moment(obj['birthdate']).format('MM/DD/YYYY'));
                    $('#lblAge').html(CalculateAge(obj['birthdate']));
                    $('#lblGender').html(obj['gender']);
                    $('#lblCivilStatus').html(obj['civil_status']);
                    $('#lblAddress').html(obj['address']);
                    $('#lblProfExpertise').html(obj['prof_expertise']);
            }
        });

        $.ajax({
            type: "GET",
            url: "get.php",
            data: {"action" : action1, "employee_id" : employee_id},
            success: function(data) {
                    var obj = $.parseJSON(data);
                    $('#lblEmployeeUser').html(obj['username']);
                    $('#lblEmployeeMobile').html(obj['mobile']);
            }
        });
  });
</script>
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
    $("#tbl_employments").DataTable({
      //"order": [[ 2, "desc" ]],
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "paging": true,
      "searching": true,
      "scrollX": false,
      "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</html>

