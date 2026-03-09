<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
?>

<!-- Loader Overlay -->
<div id="Loader" style="display: none;">
    <div class="spinner"></div>
</div>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Summary List of Payroll</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
                        <li class="breadcrumb-item active">Summary List of Payroll</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Filters -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Generate SLP Report
                    </h3>
                </div>

                <div class="card-body pb-3">
                    <form id="slpGenerateForm" class="form-horizontal">
                        <!-- Year & Period Row -->
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="slp_year" class="form-label fw-bold" style="font-size: 0.9rem;">Year <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="slp_year" name="slp_year" required>
                                        <option value="" selected hidden>Select Year...</option>
                                        <?php echo ViewPayPeriodYearsDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="pay_period" class="form-label fw-bold" style="font-size: 0.9rem;">Pay Period <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="pay_period" name="pay_period" required>
                                        <option value="" hidden selected>Select Pay Period...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Department & Employment Type -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="department" class="form-label fw-bold" style="font-size: 0.9rem;">Department <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="department" name="department" required>
                                        <option value="all" selected>All Departments</option>
                                        <?php echo ViewDepartmentsDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="employment_type" class="form-label fw-bold" style="font-size: 0.9rem;">Type <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="employment_type" name="employment_type" required>
                                        <option value="all" selected>All Types</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Casual">Casual</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="row mt-2">
                            <div class="col-12 col-sm-6 col-md-3">
                                <button type="button" id="generateSlpBtn" class="btn btn-primary btn-sm btn-block w-100" style="font-size: 0.9rem;">
                                    <i class="fas fa-cogs"></i> Generate Report
                                </button>
                            </div>

                            <div class="col-12 col-sm-4 col-md-3">
                                <button type="button" id="printSlpBtn" class="btn btn-warning btn-sm btn-block w-100" disabled style="font-size: 0.9rem;">
                                    <i class="fas fa-print"></i> Print PDF
                                </button>
                            </div>

                            <div class="col-12 col-sm-4 col-md-3">
                                <button type="button" id="exportExcelBtn" class="btn btn-success btn-sm btn-block w-100" disabled style="font-size: 0.9rem;">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="card card-outline card-success mt-3">
                <div class="card-header">
                    <h3 class="card-title">Generated Summary List of Payroll</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="slpTable" class="table table-bordered table-striped table-hover table-sm w-100">
                            <thead class="text-center table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th>Employee Name</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th style="text-align: right;">Gross Amount</th>
                                    <th style="text-align: right;">Total Deductions</th>
                                    <th style="text-align: right;">Net Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled dynamically after generation -->
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <th colspan="4" class="text-end" style="text-align: right;">Grand Total:</th>
                                    <th style="text-align: right;" id="grand_gross">0.00</th>
                                    <th style="text-align: right;" id="grand_deductions">0.00</th>
                                    <th style="text-align: right;" id="grand_net">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>


<?php $page_title = 'Summary List of Payroll'; include_once '../includes/layout/appfooter.php'; ?>
<!-- JS Libraries -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../dist/js/adminlte.js"></script>

<!-- RowGroup extension for DataTables if not bundled -->
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>

<!-- System -->
<script src="system.js"></script>
<script src="scripts/report_slp.js"></script>

</body>
</html>
