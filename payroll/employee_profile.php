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
                    <a class="nav-link" id="custom-tabs-three-gov-tab" data-toggle="pill" href="#custom-tabs-three-gov" role="tab" aria-controls="custom-tabs-three-gov" aria-selected="false">Gov't Numbers</a>
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
                  <div class="tab-pane fade" id="custom-tabs-three-gov" role="tabpanel" aria-labelledby="custom-tabs-three-gov-tab">
                    <div class="row">
                      <div class="col-sm-6">
                        <table class="table table-bordered">
                          <tr>
                            <th>SSS TIN No.</th>
                            <td><span id="lblBIRTIN"></span></td>
                          </tr>
                          <tr>
                            <th>GSIS BP No.</th>
                            <td><span id="lblGSISBP"></span></td>
                          </tr>
                          <tr>
                            <th>Pag-ibig MID</th>
                            <td><span id="lblPagibigMID"></span></td>
                          </tr>
                          <tr>
                            <th>PhilHealth No.</th>
                            <td><span id="lblPhilHealth"></span></td>
                          </tr>
                          <tr>
                            <th>SSS No.</th>
                            <td><span id="lblSSSNo"></span></td>
                          </tr>
                        </table>
                      </div>
                      <div class="col-sm-6 d-flex align-items-center">
                        <button class="btn btn-primary btn-flat" data-toggle="modal" data-target="#govNumbersModal">
                          <i class="fa fa-edit"></i> Add/Edit Numbers
                        </button>
                      </div>
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
                    <button type="button" class="btn btn-primary btn-flat btn-sm" id="btnShowAddEmploymentModal" onclick="GetEmployeeDetailsForEmployment(<?php echo $employee_id; ?>)" data-toggle="modal" data-target="#employment_modal">
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
                              <th class="text-center">Ref. No.</th>
                              <th class="text-center">Position</th>
                              <th class="text-center">Type</th>
                              <th class="text-center">Employment Start</th>
                              <th class="text-center">Employment End</th>
                              <th class="text-center">Department</th>
                              <th class="text-center">Office Assignment</th>
                              <th class="text-center">Designation</th>
                              <th class="text-center">Particulars</th>
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
      <!-- Modal for Gov't Numbers -->
      <div class="modal fade" id="govNumbersModal" tabindex="-1" role="dialog" aria-labelledby="govNumbersModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <form id="formGovNumbers">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="govNumbersModalLabel">Add/Edit Government Numbers</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="txtBIRTIN">BIR TIN</label>
                  <input type="text" class="form-control form-control-sm" id="txtBIRTIN" name="bir_tin">
                </div>
                <div class="form-group">
                  <label for="txtGSISBP">GSIS BP No.</label>
                  <input type="text" class="form-control form-control-sm" id="txtGSISBP" name="gsis_bp">
                </div>
                <div class="form-group">
                  <label for="txtPagibigMID">Pag-ibig MID</label>
                  <input type="text" class="form-control form-control-sm" id="txtPagibigMID" name="pagibig_mid">
                </div>
                <div class="form-group">
                  <label for="txtPhilHealth">PhilHealth No.</label>
                  <input type="text" class="form-control form-control-sm" id="txtPhilHealth" name="philhealth_no">
                </div>
                <div class="form-group">
                  <label for="txtSSSNo">SSS No.</label>
                  <input type="text" class="form-control form-control-sm" id="txtSSSNo" name="sss_no">
                </div>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
              </div>
            </div>
          </form>
        </div>
      </div>

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
    $('#btnShowAddEmploymentModal').on('click', function (){
      // Reset the form when the modal is shown
      $('#formEmployment')[0].reset();
      // Explicitly reset the hidden field (if needed)
      $('#txtEmploymentID').val(''); // Clears the hidden input value
      // Remove disabled attribute
      $('#cmbEmpType').val('').prop('disabled', false);
      $('#cmbPosition').val('').trigger('change').removeAttr('disabled').select2({dropdownParent: $('#employment_modal')});             // IMPORTANT!);  -> select 2 fix when not typing
      $('#cmbDepartment').val('').prop('disabled', false);
      $('#txtRate').removeAttr('disabled');
    });
</script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
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
          $("#cmbPosition").attr('disabled','disabled'); 
          $("#cmbDepartment").attr('disabled','disabled'); 
        }
        $("#cmbPosition").val("").trigger('change');
        $("#cmbDepartment").val("");
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
                    $('#cmbDepartment').val(obj['dept_id']).prop('disabled', true);
            }
        });
    });
</script>
<script>
function LoadGovNumbers(employee_id) {
    $.ajax({
        type: "GET",
        url: "get.php",
        data: { action: "get_gov_numbers", employee_id: employee_id },
        success: function(data) {
            var obj = $.parseJSON(data);
            $('#lblBIRTIN').html(obj.tin || '');
            $('#lblGSISBP').html(obj.gsis_bp || '');
            $('#lblPagibigMID').html(obj.pagibig_mid || '');
            $('#lblPhilHealth').html(obj.philhealth_no || '');
            $('#lblSSSNo').html(obj.sss_no || '');

            // Fill modal fields
            $('#txtBIRTIN').val(obj.tin || '');
            $('#txtGSISBP').val(obj.gsis_bp || '');
            $('#txtPagibigMID').val(obj.pagibig_mid || '');
            $('#txtPhilHealth').val(obj.philhealth_no || '');
            $('#txtSSSNo').val(obj.sss_no || '');
        }
    });
}

$(document).ready(function() {
    var query = getQuery();
    var employee_id = query.emp_id;

    // Load gov numbers on tab show
    $('a[data-toggle="pill"][href="#custom-tabs-three-gov"]').on('shown.bs.tab', function () {
        LoadGovNumbers(employee_id);
    });

    // Also load when modal is opened
    $('#govNumbersModal').on('show.bs.modal', function () {
        LoadGovNumbers(employee_id);
    });

    // Save gov numbers
    $('#formGovNumbers').submit(function(e) {
        e.preventDefault();
        var formData = {
            action: "save_gov_numbers",
            employee_id: employee_id,
            bir_tin: $('#txtBIRTIN').val(),
            gsis_bp: $('#txtGSISBP').val(),
            pagibig_mid: $('#txtPagibigMID').val(),
            philhealth_no: $('#txtPhilHealth').val(),
            sss_no: $('#txtSSSNo').val()
        };
        $.ajax({
            type: "POST",
            url: "save.php",
            data: formData,
            success: function(resp) {
                var obj = JSON.parse(resp);
                if(obj.result === "success") {
                    Swal.fire({
                        title: 'Success',
                        text: 'Government Numbers Successfully saved.',
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#govNumbersModal').modal('hide');
                                LoadGovNumbers(employee_id);
                            }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to save Government Numbers. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                    });
                }
            }
        });
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

