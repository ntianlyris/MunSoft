<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
?>

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
                    <h3 class="card-title">Generate Journal Entry</h3>
                </div>

                <div class="card-body">
                    <form id="journalGenerateForm" class="row">

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="journal_year">Year</label>
                                <select class="form-control form-control-sm" id="journal_year" name="journal_year" required>
                                    <option value="" selected hidden>Select Year...</option>
                                    <?php echo ViewPayPeriodYearsDropdown(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="pay_period">Pay Period</label>
                                <select class="form-control form-control-sm" id="pay_period" name="pay_period" required>
                                    <option value="" hidden selected>Select Pay Period...</option>
                                    
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <select class="form-control form-control-sm" id="department" name="department" style="width: 100%;" required>
                                    <option value="" hidden selected>-- Select Department --</option>
                                    <?php echo ViewDepartmentsDropdown(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="employment_type">Type</label>
                                <select class="form-control form-control-sm" id="employment_type" name="employment_type" style="width: 100%;" required>
                                    <option value="Regular" selected>Regular</option>
                                    <option value="Casual">Casual</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex align-items-start mt-4" style="padding-top: 7px;">
                            <button type="button" id="generateJournalBtn" class="btn btn-primary btn-sm btn-flat btn-block">
                                <i class="fas fa-cogs"></i> Generate Journal Entry
                            </button>
                        </div>

                        <div class="col-md-2 d-flex align-items-start mt-4" style="padding-top: 7px;">
                            <button type="button" id="printJournalBtn" class="btn btn-warning btn-sm btn-flat btn-block" disabled>
                                <i class="fas fa-print"></i> Print Journal
                            </button>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Responsibility Center</label>
                                <input type="text" id="summary_department" class="form-control form-control-sm" readonly placeholder="-- Select Department --">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pay Period</label>
                                <input type="text" id="summary_period" class="form-control form-control-sm" readonly placeholder="-- Select Pay Period --">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Year</label>
                                <input type="text" id="summary_year" class="form-control form-control-sm" readonly placeholder="-- Select Year --">
                            </div>
                        </div>
                    </div>

                    <table id="journalTable" class="table table-bordered table-striped table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th>Accounts & Explanation</th>
                                <th>Account Code</th>
                                <th>Debit Amt</th>
                                <th>Credit Amt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Filled dynamically after generation -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
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