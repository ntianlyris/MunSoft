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
                                    <label for="department" class="form-label fw-bold" style="font-size: 0.9rem;">Department</label>
                                    <select class="form-control form-control-sm" id="department" name="department">
                                        <option value="" selected>All Departments</option>
                                        <?php echo ViewDepartmentsDropdown(); ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Generate Button -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group mb-3">
                                    <label>&nbsp;</label>
                                    <button type="button" id="generateReportBtn" class="btn btn-primary btn-sm btn-block w-100" style="font-size: 0.9rem; margin-top: 0.5rem;">
                                        <i class="fas fa-cogs"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Selection Buttons -->
            <div class="card card-outline card-primary mt-3" id="quickSelectCard" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightning-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body pb-2">
                    <div class="row">
                        <!-- Quick View Buttons -->
                        <div class="col-md-8">
                            <label class="fw-bold" style="font-size: 0.9rem;">Quick View:</label>
                            <div class="btn-group btn-group-sm mb-3" role="group">
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="all" title="View all deductions">
                                    <i class="fas fa-list"></i> All
                                </button>
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="tax" title="View tax deductions only">
                                    <i class="fas fa-receipt"></i> Tax
                                </button>
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="gsis" title="View GSIS deductions only">
                                    <i class="fas fa-briefcase"></i> GSIS
                                </button>
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="philhealth" title="View PhilHealth deductions only">
                                    <i class="fas fa-heart"></i> PhilHealth
                                </button>
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="pagibig" title="View Pag-IBIG deductions only">
                                    <i class="fas fa-home"></i> Pag-IBIG
                                </button>
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="sss" title="View SSS deductions only">
                                    <i class="fas fa-shield-alt"></i> SSS
                                </button>
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="loans" title="View loans only">
                                    <i class="fas fa-money-bill"></i> Loans
                                </button>
                                <button type="button" class="btn btn-outline-info quickViewBtn" data-type="others" title="View other payables only">
                                    <i class="fas fa-ellipsis-h"></i> Others
                                </button>
                            </div>
                        </div>

                        <!-- Quick Export Buttons -->
                        <div class="col-md-4">
                            <label class="fw-bold" style="font-size: 0.9rem;">Export:</label>
                            <div class="btn-group btn-group-sm mb-3" role="group">
                                <button type="button" class="btn btn-success quickExportBtn" data-export-type="excel" title="Export to Excel">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button type="button" class="btn btn-danger quickExportBtn" data-export-type="pdf" title="Export to PDF">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button type="button" class="btn btn-primary quickPrintBtn" title="Print all deductions">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Card with Tabs -->
            <div class="card card-outline card-success mt-3" id="resultsCard" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i> Deduction Register Report
                    </h3>
                </div>

                <div class="card-body">
                    <!-- Deduction Type Tabs -->
                    <ul class="nav nav-tabs" id="deductionTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" data-toggle="tab" href="#tab_tax" role="tab">
                                <i class="fas fa-receipt"></i> Withholding Tax
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_gsis" role="tab">
                                <i class="fas fa-briefcase"></i> GSIS (L/R)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_gsis_ecc" role="tab">
                                <i class="fas fa-briefcase"></i> GSIS (ECC)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_philhealth" role="tab">
                                <i class="fas fa-heart"></i> PhilHealth
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_pagibig" role="tab">
                                <i class="fas fa-home"></i> Pag-IBIG
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_sss" role="tab">
                                <i class="fas fa-shield-alt"></i> SSS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_loans" role="tab">
                                <i class="fas fa-money-bill"></i> Loans
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_others" role="tab">
                                <i class="fas fa-ellipsis-h"></i> Other Payables
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- Withholding Tax Tab -->
                        <div class="tab-pane fade show active" id="tab_tax" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="tax-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="tax" data-export="excel" title="Export to Excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="tax" data-export="pdf" title="Export to PDF">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="tax" title="Print">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Employee Name</th>
                                            <th>Position</th>
                                            <th>TIN</th>
                                            <th style="text-align: right;">Basic Salary</th>
                                            <th style="text-align: right;">Tax Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="taxTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="5" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="tax-total">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- GSIS Tab -->
                        <div class="tab-pane fade" id="tab_gsis" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="gsis-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="gsis" data-export="excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="gsis" data-export="pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="gsis">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Employee Name</th>
                                            <th>Position</th>
                                            <th>GSIS BP No.</th>
                                            <th style="text-align: right;">Basic Salary</th>
                                            <th style="text-align: right;">Employee Share</th>
                                            <th style="text-align: right;">Employer Share</th>
                                            <th style="text-align: right;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="gsisTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="7" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="gsis-total">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- GSIS ECC Tab -->
                        <div class="tab-pane fade" id="tab_gsis_ecc" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="gsis_ecc-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="gsis_ecc" data-export="excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="gsis_ecc" data-export="pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="gsis_ecc">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Employee Name</th>
                                            <th>Position</th>
                                            <th>GSIS BP No.</th>
                                            <th style="text-align: right;">Employer Share</th>
                                        </tr>
                                    </thead>
                                    <tbody id="gsisEccTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="4" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="gsis_ecc-total">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- PhilHealth Tab -->
                        <div class="tab-pane fade" id="tab_philhealth" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="philhealth-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="philhealth" data-export="excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="philhealth" data-export="pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="philhealth">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Employee Name</th>
                                            <th>Position</th>
                                            <th>PhilHealth No.</th>
                                            <th style="text-align: right;">Basic Salary</th>
                                            <th style="text-align: right;">Employee Share</th>
                                            <th style="text-align: right;">Employer Share</th>
                                            <th style="text-align: right;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="philhealthTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="7" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="philhealth-total">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Pag-IBIG Tab -->
                        <div class="tab-pane fade" id="tab_pagibig" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="pagibig-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="pagibig" data-export="excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="pagibig" data-export="pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="pagibig">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Employee Name</th>
                                            <th>Position</th>
                                            <th>MID No.</th>
                                            <th style="text-align: right;">Basic Salary</th>
                                            <th style="text-align: right;">Employee Share</th>
                                            <th style="text-align: right;">Employer Share</th>
                                            <th style="text-align: right;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pagibigTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="7" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="pagibig-total">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- SSS Tab -->
                        <div class="tab-pane fade" id="tab_sss" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="sss-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="sss" data-export="excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="sss" data-export="pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="sss">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Employee Name</th>
                                            <th>Position</th>
                                            <th>SSS No.</th>
                                            <th style="text-align: right;">Contribution Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sssTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="4" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="sss-total">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Loans Tab -->
                        <div class="tab-pane fade" id="tab_loans" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="loans-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="loans" data-export="excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="loans" data-export="pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="loans">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Loan Type</th>
                                            <th style="text-align: right;">Total Loan Amount</th>
                                            <th style="width: 15%; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loansTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="2" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="loans-total">0.00</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Others Tab -->
                        <div class="tab-pane fade" id="tab_others" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <span class="badge badge-info ml-2" id="others-count">0 records</span>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-success btn-sm typeExportBtn" data-type="others" data-export="excel">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm typeExportBtn" data-type="others" data-export="pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm typePrintBtn" data-type="others">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="text-center table-light">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Deduction Type</th>
                                            <th style="text-align: right;">Total Amount</th>
                                            <th style="width: 15%; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="othersTable"></tbody>
                                    <tfoot>
                                        <tr class="table-active fw-bold">
                                            <th colspan="2" class="text-end" style="text-align: right;">Total:</th>
                                            <th style="text-align: right;" class="others-total">0.00</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Loan Breakdown Modal -->
<div class="modal fade" id="loanBreakdownModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="loanBreakdownTitle">Loan Breakdown</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Period: <span id="loanBreakdownPeriod"></span></h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-success btn-sm" id="exportLoanBreakdownExcel">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="exportLoanBreakdownPDF">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="printLoanBreakdown">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="loanBreakdownTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Employee Name</th>
                                <th>Position</th>
                                <th style="text-align: right;">Amortization</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="table-active fw-bold">
                                <th colspan="3" style="text-align: right;">Total:</th>
                                <th style="text-align: right;" id="loanBreakdownTotal">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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
