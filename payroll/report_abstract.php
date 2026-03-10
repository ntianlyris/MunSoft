<?php
include_once '../includes/layout/head.php';
include_once '../includes/layout/navbar.php';
include_once '../includes/layout/sidebar.php';
include_once '../includes/view/view.php';
include_once '../includes/view/showModals.php';
?>

<div id="Loader" style="display: none;">
    <div class="spinner"></div>
</div>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Abstract of Payroll</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
                        <li class="breadcrumb-item active">Abstract of Payroll</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Report Filters</h3>
                </div>
                <div class="card-body">
                    <form id="abstractFilterForm" class="form-row align-items-end">
                        <div class="col-md-2 mb-3">
                            <label for="abs_year" class="font-weight-bold">Year</label>
                            <select class="form-control form-control-sm" id="abs_year" name="abs_year" required>
                                <option value="" selected hidden>Select Year...</option>
                                <?php echo ViewPayPeriodYearsDropdown(); ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="pay_period" class="font-weight-bold">Pay Period</label>
                            <select class="form-control form-control-sm" id="pay_period" name="pay_period" required>
                                <option value="" hidden selected>Select Pay Period...</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="department" class="font-weight-bold">Department</label>
                            <select class="form-control form-control-sm" id="department" name="department">
                                <option value="all" selected>All Departments</option>
                                <?php echo ViewDepartmentsDropdown(); ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="employment_type" class="font-weight-bold">Type</label>
                            <select class="form-control form-control-sm" id="employment_type" name="employment_type">
                                <option value="all" selected>All Types</option>
                                <option value="Regular">Regular</option>
                                <option value="Casual">Casual</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="button" id="generateAbstractBtn" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-sync"></i> Generate
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-success mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Consolidated Abstract Results</h3>
                    <div class="card-tools">
                        <button type="button" id="printAbstractBtn" class="btn btn-warning btn-sm" disabled>
                            <i class="fas fa-print"></i> Print Abstract of Payrolls
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="abstractResultsContainer" class="table-responsive">
                        <!-- Dynamic table will be injected here -->
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-table fa-3x mb-3"></i>
                            <p>Select period and click generate to view the abstract report.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php $page_title = 'Abstract of Payroll';
include_once '../includes/layout/appfooter.php'; ?>
<!-- JS Libraries -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../dist/js/adminlte.js"></script>
<script src="system.js"></script>
<script src="scripts/report_abstract.js"></script>

</body>

</html>