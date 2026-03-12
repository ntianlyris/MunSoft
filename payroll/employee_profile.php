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
          <!-- Left Column: Profile Card -->
          <div class="col-12 col-lg-3">
            <div class="card card-primary card-outline shadow-sm">
              <div class="card-body box-profile">
                <!-- Responsive Circular Photo -->
                <div class="text-center mb-3">
                  <div class="profile-photo-wrapper">
                    <img class="profile-user-img img-fluid img-circle"
                         id="employeePhotoImg"
                         src="../includes/images/avatar.jpg"
                         alt="User profile picture"
                         title="Click to view full size">
                  </div>
                </div>

                <h3 class="profile-username text-center font-weight-bold mb-1"><span id="lblDisplayName"><i class="fas fa-spinner fa-spin"></i></span></h3>
                <p class="text-muted text-center mb-3"><span id="lblProfExpertise"><i class="fas fa-spinner fa-spin"></i></span></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Status</b> <span class="float-right"><span id="lblEmployeeStatus" class="badge badge-info"><i class="fas fa-spinner fa-spin"></i></span></span>
                  </li>
                  <li class="list-group-item">
                    <b>Username</b> <span class="float-right text-primary" id="lblEmployeeUser"><i class="fas fa-spinner fa-spin"></i></span>
                  </li>
                  <li class="list-group-item">
                    <b>Mobile</b> <span class="float-right text-muted" id="lblEmployeeMobile"><i class="fas fa-spinner fa-spin"></i></span>
                  </li>
                </ul>

                <button type="button" class="btn btn-primary btn-block btn-sm mb-2" data-toggle="modal" data-target="#photoUploadModal">
                  <i class="fas fa-camera mr-1"></i> Change Photo
                </button>
              </div>
            </div>

            <!-- ID Information -->
            <div class="card card-primary card-outline shadow-sm d-none d-lg-block">
              <div class="card-header">
                <h3 class="card-title text-sm"><i class="fas fa-id-card mr-1"></i> Identification</h3>
              </div>
              <div class="card-body py-3">
                <small class="text-muted d-block mb-1">Employee ID Number (EIDN)</small>
                <h5 class="font-weight-bold mb-0" id="lblEmployeeID"><i class="fas fa-spinner fa-spin"></i></h5>
              </div>
            </div>
          </div>

          <!-- Right Column: Detail Cards -->
          <div class="col-12 col-lg-9">
            <!-- Personal Information Card -->
            <div class="card card-white shadow-sm mb-4">
              <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-user text-primary mr-2"></i> Personal Information</h3>
                <div class="card-tools">
                    <button class="btn btn-primary btn-xs btn-flat" onclick="GetEmployeeDetails(<?php echo $employee_id; ?>)" data-toggle="modal" data-target="#employee_modal">
                        <i class="fa fa-edit mr-1"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-xs btn-flat" onclick="DeleteEmployee(<?php echo $employee_id; ?>)" data-toggle="tooltip" title="Delete" data-placement="bottom">
                        <i class="fa fa-times mr-1"></i> Delete
                    </button>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">Last Name</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblLastname"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">First Name</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblFirstname"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">Middle Name</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblMiddlename"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">Extension</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblExtension"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                </div>
                <hr class="my-2">
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">Birthdate</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblBirthdate"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">Age</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblAge"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">Gender</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblGender"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="text-muted small d-block">Civil Status</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblCivilStatus"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label class="text-muted small d-block">Residential Address</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblAddress"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Government Identifiers Card -->
            <div class="card card-white shadow-sm mb-4">
              <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-id-badge text-success mr-2"></i> Government Identifiers</h3>
                <div class="card-tools">
                    <button class="btn btn-success btn-xs btn-flat" data-toggle="modal" data-target="#govNumbersModal">
                      <i class="fa fa-edit mr-1"></i> Add/Edit Numbers
                    </button>
                </div>
              </div>
              <div class="card-body">
                <div class="row text-center text-md-left">
                  <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">TIN No.</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblBIRTIN"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">GSIS BP No.</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblGSISBP"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">Pag-IBIG MID No.</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblPagibigMID"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                </div>
                <hr class="my-2">
                <div class="row text-center text-md-left">
                  <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">PhilHealth No.</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblPhilHealth"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">SSS No.</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblSSSNo"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tenure Information Card -->
            <div class="card card-white shadow-sm mb-4">
              <div class="card-header bg-white py-3 border-bottom">
                <h3 class="card-title font-weight-bold"><i class="fas fa-briefcase text-info mr-2"></i> Tenure & Professional info</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="text-muted small d-block">Date of Original Appointment (Hire Date)</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblDateHired"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="text-muted small d-block">Employee ID Number (EIDN)</label>
                    <span class="h6 mb-0 font-weight-normal" id="lblEmployeeID2"><i class="fas fa-spinner fa-spin"></i></span>
                  </div>
                </div>
              </div>
            </div>
          </div>


        <!-- Custom CSS for Profile Design -->
        <style>
          .profile-photo-wrapper {
            position: relative;
            width: 100%;
            max-width: 180px;
            margin: 0 auto;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
          }
          .profile-photo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
          }
          .profile-photo-wrapper img:hover {
            transform: scale(1.05);
          }
          .card-white {
            background: #fff;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: .25rem;
          }
        </style>
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

      <!-- Employment Detail Modal (Read-Only) -->
      <div class="modal fade" id="employmentDetailModal" tabindex="-1" role="dialog" aria-labelledby="employmentDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="employmentDetailModalLabel"><i class="fas fa-briefcase"></i> Employment Details</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body p-0">
              <table class="table table-striped mb-0">
                <tbody id="employmentDetailContent">
                  <!-- Content populated by JS -->
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal for Employee Photo Upload -->
      <div class="modal fade" id="photoUploadModal" tabindex="-1" role="dialog" aria-labelledby="photoUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="photoUploadModalLabel">Upload Employee Photo</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="formPhotoUpload" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="text-center" id="photoPreviewContainer" style="margin-bottom: 20px;">
                  <img id="photoPreview" src="" alt="Preview" style="max-width: 250px; max-height: 250px; display: none;">
                  <p id="previewText" style="color: #999; font-size: 14px;">Image preview will appear here</p>
                </div>
                <div class="form-group">
                  <label for="fileEmployeePhoto">Select Photo:</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="fileEmployeePhoto" name="employee_photo" accept="image/jpeg,image/png,image/gif" required>
                    <label class="custom-file-label" for="fileEmployeePhoto">Choose file...</label>
                  </div>
                  <small class="form-text text-muted">
                    Supported formats: JPG, PNG, GIF (Max 5MB). Image will be automatically cropped and resized for optimal display.
                  </small>
                </div>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Photo</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Modal for Full-Size Photo View -->
      <div class="modal fade" id="photoViewModal" tabindex="-1" role="dialog" aria-labelledby="photoViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
          <div class="modal-content bg-dark">
            <div class="modal-header bg-dark border-0">
              <h5 class="modal-title text-white" id="photoViewModalLabel">Employee Photo</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body text-center bg-dark" style="padding: 30px;">
              <img id="photoViewImage" src="" alt="Employee Photo" style="max-width: 100%; max-height: 600px; border-radius: 8px;">
            </div>
            <div class="modal-footer bg-dark border-0">
              <a id="photoDownloadBtn" href="#" download class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Download</a>
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
            </div>
          </div>
        </div>
      </div>
      
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


<?php $page_title = 'Employee Profile'; include_once '../includes/layout/appfooter.php'; ?>
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
<!-- Employee Photo Upload Script -->
<script>
// Load employee photo on page load
function LoadEmployeePhoto(employee_id) {
    $.ajax({
        type: "POST",
        url: "../admin/upload_employee_photo.php",
        data: {
            action: "get_photo",
            employee_id: employee_id
        },
        dataType: "json",
        success: function(response) {
            if (response.status && response.photo_path) {
                $('#employeePhotoImg').attr('src', '../' + response.photo_path);
                // Store the photo path for full-size view
                $('#employeePhotoImg').attr('data-photo-path', '../' + response.photo_path);
            }
        }
    });
}

// Handle employee photo click to view full-size
$('#employeePhotoImg').on('click', function() {
    var photoPath = $(this).attr('src');
    if (photoPath && photoPath !== '../includes/images/avatar.jpg') {
        $('#photoViewImage').attr('src', photoPath);
        $('#photoDownloadBtn').attr('href', photoPath).attr('download', 'employee_photo.jpg');
        $('#photoViewModal').modal('show');
    }
});

// Handle photo file selection and preview
$('#fileEmployeePhoto').on('change', function(e) {
    var file = e.target.files[0];
    var fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
    
    // Update label
    $(this).siblings('.custom-file-label').html(file.name);
    
    // Check file size
    if (fileSize > 5) {
        Swal.fire({
            title: 'File Too Large',
            text: 'File size exceeds 5MB limit. Please select a smaller image.',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ok'
        });
        $(this).val('');
        $('#photoPreview').hide();
        $('#previewText').show();
        return;
    }
    
    // Preview image
    var reader = new FileReader();
    reader.onload = function(e) {
        $('#photoPreview').attr('src', e.target.result).show();
        $('#previewText').hide();
    };
    reader.readAsDataURL(file);
});

// Handle photo upload form submission
$('#formPhotoUpload').on('submit', function(e) {
    e.preventDefault();
    
    var fileInput = $('#fileEmployeePhoto')[0];
    if (!fileInput.files.length) {
        Swal.fire({
            title: 'No File Selected',
            text: 'Please select a file to upload.',
            icon: 'warning',
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'Ok'
        });
        return;
    }
    
    var query = getQuery();
    var employee_id = query.emp_id;
    
    var formData = new FormData();
    formData.append('action', 'upload_photo');
    formData.append('employee_id', employee_id);
    formData.append('employee_photo', fileInput.files[0]);
    
    $.ajax({
        type: "POST",
        url: "../admin/upload_employee_photo.php",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            if (response.status) {
                $('#photoUploadModal').modal('hide');
                Swal.fire({
                    title: 'Success',
                    text: 'Photo uploaded successfully!',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $('#formPhotoUpload')[0].reset();
                    $('.custom-file-label').html('Choose file...');
                    $('#photoPreview').hide();
                    $('#previewText').show();
                    LoadEmployeePhoto(employee_id);
                    
                    // Final cleanup to prevent "freeze" or "darkening"
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                });
            } else {
                Swal.fire({
                    title: 'Upload Failed',
                    text: 'Error: ' + response.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                });
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error',
                text: 'An error occurred during upload. Please try again.',
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ok'
            });
        }
    });
});
</script>
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
        
        // Load employee details
        $.ajax({
            type: "GET",
            url: "get.php",
            data: {"action" : action, "employee_id" : employee_id},
            success: function(data) {
                    var obj = $.parseJSON(data);
                    $('#lblDisplayName').html(obj['firstname'] + " " + obj['lastname']);
                    $('#lblProfExpertise').html(obj['prof_expertise'] || "Not Specified");
                    
                    // Status Badge
                    var status = obj['employee_status'];
                    var badgeClass = 'badge-secondary';
                    if (status == 'Active' || status == 'Employed' || status == '1') {
                        badgeClass = 'badge-success';
                    } else if (status == 'Inactive' || status == '0') {
                        badgeClass = 'badge-danger';
                    }
                    $('#lblEmployeeStatus').removeClass().addClass('badge ' + badgeClass).html(status);
                    
                    // Identification
                    $('#lblEmployeeID').html(obj['employee_id_num']);
                    $('#lblEmployeeID2').html(obj['employee_id_num']);
                    
                    // Personal Info
                    $('#lblLastname').html(obj['lastname']);
                    $('#lblFirstname').html(obj['firstname']);
                    $('#lblMiddlename').html(obj['middlename'] || "—");
                    $('#lblExtension').html(obj['extension'] || "—");
                    $('#lblBirthdate').html(moment(obj['birthdate']).format('MMMM D, YYYY'));
                    $('#lblAge').html(CalculateAge(obj['birthdate']));
                    $('#lblGender').html(obj['gender']);
                    $('#lblCivilStatus').html(obj['civil_status']);
                    $('#lblAddress').html(obj['address']);
                    
                    // Tenure
                    $('#lblDateHired').html(obj['hire_date'] ? moment(obj['hire_date']).format('MMMM D, YYYY') : "Not Recorded");
            }
        });

        $.ajax({
            type: "GET",
            url: "get.php",
            data: {"action" : action1, "employee_id" : employee_id},
            success: function(data) {
                    var obj = $.parseJSON(data);
                    $('#lblEmployeeUser').html(obj['username'] || "Not Linked");
                    $('#lblEmployeeMobile').html(obj['mobile'] || "Not Linked");
            }
        });
        
        // Load employee photo
        LoadEmployeePhoto(employee_id);
        
        // Load Government Numbers immediately
        LoadGovNumbers(employee_id);
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
  var _cachedEmployments = [];

  function formatCurrency(amount) {
    var val = parseFloat(amount);
    if (isNaN(val)) return "0.00";
    return val.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function formatEmploymentDate(dateStr, status) {
    if (!dateStr || dateStr === '0000-00-00' || dateStr === 'null') {
      return status == '1' ? 'PRESENT' : 'N/A';
    }
    var m = moment(dateStr);
    if (!m.isValid()) {
      return status == '1' ? 'PRESENT' : 'N/A';
    }
    return m.format('MMMM D, YYYY');
  }

  function showEmploymentDetailModal(id) {
    // If cache is empty, fetch from the dashboard/profile data
    if (_cachedEmployments.length === 0) {
      $.ajax({
        url: '../employee/get_dashboard_data.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'all' },
        success: function (response) {
          if (response.success && response.employments) {
            _cachedEmployments = response.employments;
            renderDetailModal(id);
          }
        }
      });
    } else {
      renderDetailModal(id);
    }
  }

  function renderDetailModal(id) {
    var e = _cachedEmployments.find(function (item) { return item.employment_id == id; });
    if (!e) return;

    var start = formatEmploymentDate(e.employment_start, '0');
    var end = formatEmploymentDate(e.employment_end, e.employment_status);
    var pos = e.designation || e.position_title || 'Position';
    var dept = e.dept_name || e.dept_assigned || 'Department';

    var html =
      '<tr><th width="40%">Ref Number</th><td>' + (e.employment_refnum || 'N/A') + '</td></tr>' +
      '<tr><th>Type</th><td>' + (e.employment_type || 'N/A') + '</td></tr>' +
      '<tr><th>Position</th><td>' + pos + '</td></tr>' +
      '<tr><th>Department</th><td>' + dept + '</td></tr>' +
      '<tr><th>Designation</th><td>' + (e.designation || 'N/A') + '</td></tr>' +
      '<tr><th>Period</th><td>' + start + ' - ' + end + '</td></tr>' +
      '<tr><th>Nature of Work</th><td>' + (e.work_nature || 'N/A') + '</td></tr>' +
      '<tr><th>Specifics</th><td>' + (e.work_specifics || 'N/A') + '</td></tr>' +
      '<tr><th>Particulars</th><td>' + (e.employment_particulars || 'N/A') + '</td></tr>' +
      '<tr><th>Rate</th><td>' + (e.rate ? '₱' + formatCurrency(e.rate) : 'N/A') + '</td></tr>';

    $('#employmentDetailContent').html(html);
    $('#employmentDetailModal').modal('show');
  }

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

