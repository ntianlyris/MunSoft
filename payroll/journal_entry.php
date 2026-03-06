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
                    <h1>Payroll Journal Entries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
                        <li class="breadcrumb-item active">Journal Entries</li>
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
                        <i class="fas fa-file-invoice"></i> Generate Journal Entry
                    </h3>
                </div>

                <div class="card-body pb-3">
                    <form id="journalGenerateForm" class="form-horizontal">
                        <!-- Year & Period Row -->
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                                <div class="form-group mb-3">
                                    <label for="journal_year" class="form-label fw-bold" style="font-size: 0.9rem;">Year <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="journal_year" name="journal_year" required>
                                        <option value="" selected hidden>Select Year...</option>
                                        <?php echo ViewPayPeriodYearsDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                                <div class="form-group mb-3">
                                    <label for="pay_period" class="form-label fw-bold" style="font-size: 0.9rem;">Pay Period <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="pay_period" name="pay_period" required>
                                        <option value="" hidden selected>Select Pay Period...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Consolidation Type -->
                            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold" style="font-size: 0.9rem;">Journal Scope <span class="text-danger">*</span></label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="consolidation_type" id="consolidation_single" value="single" checked>
                                        <label class="btn btn-outline-info btn-sm" for="consolidation_single" style="font-size: 0.85rem;">
                                            <i class="fas fa-building"></i> Single Dept
                                        </label>

                                        <input type="radio" class="btn-check" name="consolidation_type" id="consolidation_all" value="all">
                                        <label class="btn btn-outline-info btn-sm" for="consolidation_all" style="font-size: 0.85rem;">
                                            <i class="fas fa-sitemap"></i> All Depts
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Department & Employment Type -->
                            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                                <div class="form-group mb-3">
                                    <label for="department" class="form-label fw-bold" style="font-size: 0.9rem;">Department <span class="text-danger" id="dept_required">*</span></label>
                                    <select class="form-control form-control-sm" id="department" name="department" required>
                                        <option value="" hidden selected>-- Select --</option>
                                        <?php echo ViewDepartmentsDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                                <div class="form-group mb-3">
                                    <label for="employment_type" class="form-label fw-bold" style="font-size: 0.9rem;">Type <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="employment_type" name="employment_type" required>
                                        <option value="Regular" selected>Regular</option>
                                        <option value="Casual">Casual</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="row mt-2">
                            <div class="col-12 col-sm-6 col-md-3">
                                <button type="button" id="generateJournalBtn" class="btn btn-primary btn-sm btn-block w-100" style="font-size: 0.9rem;">
                                    <i class="fas fa-cogs"></i> Generate
                                </button>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <button type="button" id="printJournalBtn" class="btn btn-warning btn-sm btn-block w-100" disabled style="font-size: 0.9rem;">
                                    <i class="fas fa-print"></i> Print PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="card card-outline card-success mt-3">
                <div class="card-header">
                    <h3 class="card-title">Generated Journal Entry</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3" id="journalSummary">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">Responsibility Center</label>
                                <input type="text" id="summary_department" class="form-control form-control-sm" readonly placeholder="-- Not Generated Yet --">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">Pay Period</label>
                                <input type="text" id="summary_period" class="form-control form-control-sm" readonly placeholder="-- Not Generated Yet --">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">Year</label>
                                <input type="text" id="summary_year" class="form-control form-control-sm" readonly placeholder="-- Not Generated Yet --">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="journalTable" class="table table-bordered table-striped table-hover table-sm">
                            <thead class="text-center table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 45%;">Accounts & Explanation</th>
                                    <th style="width: 15%;">Account Code</th>
                                    <th style="width: 17%; text-align: right;">Debit Amt</th>
                                    <th style="width: 18%; text-align: right;">Credit Amt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled dynamically after generation -->
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th style="text-align: right;"></th>
                                    <th style="text-align: right;"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- JS Libraries -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/select2/js/select2.full.min.js"></script>
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

<!-- System -->
<script src="system.js"></script>
<script src="scripts/journal_entry.js"></script>


</body>
</html>