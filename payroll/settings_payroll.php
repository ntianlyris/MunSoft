<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1>Payroll Settings</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Payroll Frequency Settings -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                <h3 class="card-title">Payroll Frequency</h3>
                </div>
                <div class="card-body">
                    <form action="save_settings.php" method="POST" class="form-inline">
                        <label for="pay_frequency" class="mr-2">Select Payroll Frequency:</label>
                        <select id="pay_frequency" class="form-control mr-3" name="pay_frequency">
                            <?php echo ViewPayFrequenciesDropdown(); ?>
                        </select>
                        <button type="submit" class="btn btn-primary" name="submit" value="set_pay_frequency"><i class="fas fa-arrow-right"></i> Set Pay Frequency</button>
                        <small class="text-muted" style="padding-left:10px;">⚠ Changing this affects pay period dropdown and computations.</small>
                    </form>
                </div>
            </div>
                <!-- Payroll Year Control -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Payroll Year Control</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="save_settings.php">
                        <input type="hidden" name="year_to_close" value="<?php echo date('Y') - 1; ?>">
                        <button type="submit" class="btn btn-success btn-lg" name="submit" value="close_payroll_year"><i class="fas fa-check"></i> Finalize December Payroll (Close Year) And Generate Pay Periods for the Current Year</button>
                        <small class="text-muted" style="padding-left:10px;">⚠ Only use this function if the final and last payroll for the year was finished.</small>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>


<?php $page_title = 'Payroll Settings'; include_once '../includes/layout/appfooter.php'; ?>
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
<script src="scripts/config.js"></script>

</body>
</html>