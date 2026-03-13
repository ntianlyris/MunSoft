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
                    <h1>Deduction Registers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
                        <li class="breadcrumb-item active">Deduction Registers</li>
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
                        <i class="fas fa-filter"></i> Generate Deduction Register
                    </h3>
                </div>

                <div class="card-body pb-3">
                    <form id="deductionRegisterForm" class="form-horizontal">
                        <div class="row">
                            <!-- Year Filter -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="report_year" class="form-label fw-bold" style="font-size: 0.9rem;">Year <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="report_year" name="report_year" required>
                                        <option value="" selected hidden>Select Year...</option>
                                        <?php echo ViewPayPeriodYearsDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Pay Period Filter -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="pay_period" class="form-label fw-bold" style="font-size: 0.9rem;">Pay Period <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="pay_period" name="pay_period" required>
                                        <option value="" hidden selected>Select Pay Period...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Department Filter -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="department" class="form-label fw-bold" style="font-size: 0.9rem;">Department <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="department" name="department" required>
                                        <option value="all" selected>All Departments</option>
                                        <?php echo ViewDepartmentsDropdown(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Deduction Type Filter -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="deduction_type" class="form-label fw-bold" style="font-size: 0.9rem;">Deduction Type <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="deduction_type" name="deduction_type" required>
                                        <option value="" selected hidden>Select Deduction Type...</option>
                                        <?php echo ViewDeductionTypesDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Deduction Category Filter (Conditional) -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label for="deduction_category" class="form-label fw-bold" style="font-size: 0.9rem;">Category</label>
                                    <select class="form-control form-control-sm" id="deduction_category" name="deduction_category" disabled>
                                        <option value="" selected>All Categories</option>
                                        <option value="LOAN">LOAN</option>
                                        <option value="OTHER">OTHERS</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Specific Deduction Filter (Dynamic) -->
                            <div class="col-12 col-sm-6 col-md-6" id="specific_deduction_col" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="specific_deduction" class="form-label fw-bold" style="font-size: 0.9rem;">Specific Deduction <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm select2" id="specific_deduction" name="specific_deduction" style="width: 100%;">
                                        <option value="" selected>Select Specific Deduction...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button Row -->
                        <div class="row mt-2">
                            <div class="col-12 col-sm-6 col-md-3">
                                <button type="button" id="generateReportBtn" class="btn btn-primary btn-sm btn-block w-100" style="font-size: 0.9rem;">
                                    <i class="fas fa-cogs"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table (Placeholder for UI consistency) -->
            <div class="card card-outline card-success mt-3" id="resultsCard" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">Generated Internal Deduction Register</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="deductionTable" class="table table-bordered table-striped table-hover table-sm w-100">
                            <thead class="text-center table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th>Employee Name</th>
                                    <th>Position</th>
                                    <th>Deduction Name</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled dynamically after generation -->
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <th colspan="4" class="text-end" style="text-align: right;">Grand Total:</th>
                                    <th style="text-align: right;" id="grand_total">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php $page_title = 'Deduction Registers'; include_once '../includes/layout/appfooter.php'; ?>
<!-- JS Libraries -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../dist/js/adminlte.js"></script>

<!-- RowGroup extension for DataTables -->
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>

<!-- System scripts -->
<script src="system.js"></script>
<script src="scripts/report_deduction_registers.js"></script>

</body>
</html>
