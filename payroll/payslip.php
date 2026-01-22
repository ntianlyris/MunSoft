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
            <h1>Payslip</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Payslip</li>
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
                    <div class="card card-outline card-purple">
                        <div class="card-header">
                            <h3 class="card-title">Employee Payslip</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            
                                <!-- Employee Selector -->
                                <div class="mb-3">
                                    <form id="formPaySlipGenerator">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Select Employee:</label>
                                                <select id="payslipEmployee" name="payslipEmployee" class="form-control select2" style="width: 100%;" required>
                                                    <option hidden selected value="">-- Choose Employee --</option>
                                                    <?php echo ViewEmployeesDropdown(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Year:</label>
                                                <select id="payslipYear" name="payslipYear" class="form-control" style="width: 100%;" required>
                                                    <option hidden selected value="">-- Choose Year --</option>
                                                    <?php echo ViewPayPeriodYearsDropdown(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Period:</label>
                                                <select id="payslipPeriod" name="payslipPeriod" class="form-control" style="width: 100%;" required>
                                                    <option hidden selected value="">-- Choose Period --</option>
                                                    
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label></label>
                                                <!-- Print Button -->
                                                <div class="d-flex">
                                                    <button type="submit" class="btn btn-success btn-flat btn-responsive mt-2" id="btnGeneratePayslip" data-remit_id="">
                                                        <i class="fas fa-print"></i> Generate Payslip
                                                    </button>
                                                </div>
                                            </div>
                                    </form>
                                            <div class="col-md-3 text-right">
                                                <label></label>
                                                <!-- Print Button -->
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-warning btn-flat btn-responsive mt-2" id="btnPrintPayslip" data-remit_id="">
                                                        <i class="fas fa-print"></i> Print Payslip
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                </div>

                                <div class="payslip-container border rounded p-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <!-- LEFT: Employee Information -->
                                            <div class="employee-info border-right pr-3" style="width: 100%;">
                                                <h5 class="text-center mb-3 text-primary">Employee Information</h5>

                                                <p><strong>Employee No:</strong> <span id="employee_num"></span></p>
                                                <p><strong>Name:</strong> <span id="employee_name"></span></p>
                                                <p><strong>Position:</strong> <span id="position"></span></p>
                                                <p><strong>Department:</strong> <span id="department"></span></p>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <!-- RIGHT: Payslip Details -->
                                            <div class="payslip-details" style="width: 100%;">

                                                <h5 class="text-center text-success mb-3">PAYSLIP</h5>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">Period Coverage: <label id="payCoverage"></label></div>
                                                    <div class="col-md-6 mb-3">Date Issued: <label id="issuedDate"></label></div>
                                                </div>

                                                <div class="row">
                                                    <!-- Earnings -->
                                                    <div class="col-6">
                                                        <h6 class="text-secondary">EARNINGS</h6>
                                                        <table class="table table-sm table-bordered">
                                                            <tbody id="earningsList">
                                                                <tr>
                                                                    <td>BASIC</td>
                                                                    <td class="text-right" id="basic">0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>OTHER INCOME</td>
                                                                    <td class="text-right" id="other_earnings">0.00</td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="font-weight-bold">
                                                                    <td>GROSS PAY</td>
                                                                    <td class="text-right" id="grossPay">0.00</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>

                                                    <!-- Deductions -->
                                                    <div class="col-6">
                                                        <h6 class="text-secondary">DEDUCTIONS</h6>
                                                        <table class="table table-sm table-bordered">
                                                            <tbody id="deductions_container">
                                                                <!-- GSIS, HDMF, PhilHealth, Loans, Others -->
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="font-weight-bold">
                                                                    <td>TOTAL DEDUCTIONS</td>
                                                                    <td class="text-right" id="totalDeductions">0.00</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- NET PAY -->
                                                <div class="text-center mt-3 p-2 bg-light border rounded">
                                                    <h4>NET PAY: <span id="netPay" class="text-primary">₱0.00</span></h4>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    

                                    
                                </div>
                            
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->

        <!-- Modals -->
        <?php //ShowEmployeeModal(); ?>

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
<!-- System Engine -->
<script src="system.js"></script>
<!-- Employees Engine -->
<script src="scripts/payslip.js"></script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
  });
</script>
</body>
</html>