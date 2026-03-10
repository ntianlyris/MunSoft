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
            <h1>Employee Deductions</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Deductions</li>
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
            <div class="myButton">
                <button type="button" class="btn btn-primary btn-flat"  data-toggle="modal" data-target="#employee_deductions_modal"><i class="fas fa-plus-circle"></i> Add Employee Deductions</button>
            </div>
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Employee Deductions</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">EIDN</th>
                            <th class="text-center">Employee</th>
                            <th class="text-center">Effectivity Date</th>
                            <th class="text-center">Total Deductions</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php ViewAllEmployeesDeductions(); ?>
                    </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->

      <!-- Modals -->
      <?php ShowEmployeeDeductionsModal(); ?>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


<?php $page_title = 'Employee Deductions'; include_once '../includes/layout/appfooter.php'; ?>
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
<script src="scripts/deductions.js"></script>
<!-- Clear the input fields on form when adding new entry -->
<script>
  $('#employee_deductions_modal').on('show.bs.modal', function () {
    // 1. Reset all static form fields
    $('#formEmployeeDeduction')[0].reset();

    // 2. Clear all hidden input fields (just in case)
    $('#formEmployeeDeduction').find('input[type="hidden"]').val('');

    // 3. Remove all dynamically injected earning code inputs
    $('#deductionCodesInputs').empty();

    // 4. Reset dropdowns if you're using any (e.g. select2)
    $('#formEmployeeDeduction select').val('').trigger('change');

    // 5. Remove validation states and messages (optional)
    $('#formEmployeeDeduction .is-invalid').removeClass('is-invalid');
    $('#formEmployeeDeduction .invalid-feedback').remove();
    // re-enable employee selector and reinitialize select2 for modal context
    $('#cmbEmployee').prop('disabled', false);
    $('#cmbEmployee').select2({
        dropdownParent: $('#employee_deductions_modal'),
        width: '100%'
    });  });
</script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
  });
</script>

<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": false, 
      "lengthChange": false, 
      "autoWidth": false,
      "paging": false,
      "searching": true,
      "scrollX": true,
      "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>

<script type="text/javascript">
  $( document ).ready(function(){
    var addButton = $('#btnAddDeductionCode');
    var wrapper = $("#deductionCodesInputs");

    $(addButton).on('click',function(){
      var config_deduction_id = $('#cmbDeductionCodes').find('option:selected').val();
      var deduction_code = $('#cmbDeductionCodes').find('option:selected').text();

      if (config_deduction_id != "") {
        var isDuplicate = false;
        $('input[name="config_deduction_ids[]"]').each(function() {
            if ($(this).val() == config_deduction_id) {
                isDuplicate = true;
                return false;
            }
        });

        if (isDuplicate) {
            Swal.fire({
                title: 'Duplicate Deduction',
                text: 'This deduction code has already been added to the list.',
                icon: 'warning',
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Ok'
            });
            return;
        }

        var input = '<div class="row" style="padding-bottom:15px;" id="dynamic_field">';
            input += '<div class="col-3"><label>'+ deduction_code +'</label></div>';
            input += '<div class="col-8"><div class="input-group">';
            input += '<input type="hidden" name="config_deduction_ids[]" value="'+ config_deduction_id +'">';
            input += '<input type="text" style="text-align:right;" class="form-control form-control-sm emp-deductions-amt" name="emp-deductions-amt[]" onkeyup="CalculateSum(); ValidateInputAmount();" placeholder="0.00" required>';
            input += '<span class="input-group-btn"><a href="javascript:void(0);" class="btn btn-danger btn-sm btn-flat remove_button"><i class="fa fa-times"></i></a></span>';
            input += '</div></div></div>';

        $(wrapper).append(input);
      }
    });  

    $(wrapper).on('click', '.remove_button', function(e){
      e.preventDefault();
      $(this).closest("#dynamic_field").remove();
      //CalculateSum();
    })
  });

  function CalculateSum(){
    var arr = document.getElementsByName('emp-deductions-amt[]');
    var sum = 0;
    //alert(arr.length);
    for(var i=0; i < arr.length; i++){
      if (parseFloat(arr[i].value)) {
        sum += parseFloat(arr[i].value);
      }
    }
   // alert(sum);
    document.getElementById('txtEmployeeDeductionTotal').value = sum.toFixed(2);
  }

  function ValidateInputAmount(){
    var txt_amt = document.getElementsByName('emp-deductions-amt[]');
    var amt;
    var numbers_decimal = /^-?\d*(\.\d{0,2})?$/;    //--numbers w/ 2 decimals

    for(var i=0; i < txt_amt.length; i++){
      amt = txt_amt[i].value;
      if (!amt.match(numbers_decimal)) {
        //alertify.notify('Amount must be numeric and have a maximum of 2 decimal places.','error', 3, function(){window.location.reload();});
        //alertify.alert('ERROR!','Amount must be numeric with a maximum of 2 decimal places.',function(){window.location.reload();});
        Swal.fire({
          title: 'Error',
          text: 'Amount must be numeric with a maximum of 2 decimal places.',
          icon: 'error',
          confirmButtonColor: '#dc3545',
          confirmButtonText: 'Ok'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href;
              }
        });
      }
    }
  }
</script>
</body>
</html>

