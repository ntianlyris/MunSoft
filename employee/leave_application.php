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
            <h1>Leave of Absence</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Leave of Absence</li>
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

            <!-- Add Leave Button -->
            <div class="myButton mb-2">
                <button type="button" class="btn btn-success btn-flat" data-toggle="modal" data-target="#leave_modal">
                  <i class="fas fa-calendar-plus"></i> Apply for Leave
                </button>
            </div>

            <!-- Leave Records Table -->
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">My Leave Applications</h3>
              </div>
              <div class="card-body">
                <table id="leaveTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="text-center">No.</th>
                      <th class="text-center">Leave Type</th>
                      <th class="text-center">Date Filed</th>
                      <th class="text-center">Inclusive Dates</th>
                      <th class="text-center">No. of Days</th>
                      <th class="text-center">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php ViewAllEmployeeLeaveApplications(); ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Leave Application Modal -->
        <div class="modal fade" id="leave_modal" tabindex="-1" role="dialog" aria-labelledby="leaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="leaveModalLabel"><i class="fas fa-calendar-plus"></i> Apply for Leave</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            
            <form id="leaveForm" enctype="multipart/form-data">
                <div class="modal-body">
                <div class="row">
                    <!-- Leave Type -->
                    <div class="form-group col-md-6">
                    <label>Leave Type</label>
                    <select name="leave_type" class="form-control select2" style="width:100%;" required>
                        <option value="" selected hidden>-- Select Leave --</option>
                        <?php echo BuildLeaveTypesDropdown(); ?>
                    </select>
                    </div>

                    <!-- Inclusive Dates (Dynamic Inputs) -->
                    <div class="form-group col-md-6">
                    <label>Inclusive Dates</label>
                    <div id="date-container">
                        <div class="input-group mb-2 date-row">
                            <input type="date" name="inclusive_dates[]" class="form-control" required>
                        </div>
                    </div>
                    <button type="button" id="add-date" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus"></i> Add Date</button>
                    </div>
                </div>

                <div class="row">
                    <!-- Reason -->
                    <div class="form-group col-md-12">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="State your reason for leave..." required></textarea>
                    </div>
                </div>

                <div class="row">
                    <!-- Attachment -->
                    <div class="form-group col-md-12">
                    <label>Supporting Document (optional)</label>
                    <input type="file" name="attachment" class="form-control-file">
                    <small class="text-muted">Ex: medical certificate, travel order, etc.</small>
                    </div>
                </div>
                </div>
                
                <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>

            </div>
        </div>
        </div>
        <!-- End Modal -->

    </section>
</div>

<!-- jQuery -->
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
<script src="../dist/js/demo.js"></script>
<script src="system.js"></script>
<!--<script src="scripts/leaves.js"></script>-->
<script>
    $(document).ready(function() {
        // Add new date row
        $('#add-date').click(function() {
            let newRow = `
            <div class="input-group mb-2 date-row">
                <input type="date" name="inclusive_dates[]" class="form-control" required>
                <div class="input-group-append">
                <button class="btn btn-danger remove-date" type="button"><i class="fas fa-times"></i></button>
                </div>
            </div>
            `;
            $('#date-container').append(newRow);
        });

        // Remove date row
        $(document).on('click', '.remove-date', function() {
            $(this).closest('.date-row').remove();
        });
    });
</script>
<script>
$(document).ready(function(){

  // Handle leave form submit
  $("#leaveForm").on("submit", function(e){
    e.preventDefault();
    var action = '';
    var formData = new FormData(this); // supports file upload
        formData.append("action", "submit_leave_application");
    $.ajax({
      url: "leave_handler.php", // backend file
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function(){
        // you can show a loading spinner here
        $("#leaveForm button[type=submit]").prop("disabled", true).text("Submitting...");
      },
      success: function(response){
        // Expect JSON response
        try {
          var res = JSON.parse(response);
          if(res.status === "success"){
            $("#leave_modal").modal("hide");
            $("#leaveForm")[0].reset();
            Swal.fire({
              title: 'Success',
              text: res.message,
              icon: 'success',
              confirmButtonColor: '#28a745',
              confirmButtonText: 'Ok'
                }).then((result) => {
                      if (result.isConfirmed) {
                        window.location.reload();
                        //$("#leaveTable").DataTable().ajax.reload(null, false); // reload leave records
                    }
              });
          } else {
            Swal.fire({
                  icon: "error",
                  title: "Oopss...",
                  text: res.message,
                  confirmButtonColor: '#dc3545',
                });
          }
        } catch(err) {
          console.log(response);
          //alert("Unexpected response from server.");
        }
      },
      error: function(xhr, status, error){
        alert("AJAX Error: " + error);
      },
      complete: function(){
        $("#leaveForm button[type=submit]").prop("disabled", false).html('<i class="fas fa-paper-plane"></i> Submit');
      }
    });
  });

});
</script>
<script>
  $(function () {
    // Initialize DataTable
    $("#leaveTable").DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "paging": true,
      "searching": true,
      "scrollX": true,
      "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#leaveTable_wrapper .col-md-6:eq(0)');

    // Initialize Select2
    $('.select2').select2();
  });
</script>
</body>
</html>