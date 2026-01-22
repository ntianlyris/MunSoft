<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
    include_once '../includes/view/showModals.php';
?>

<style>
body {
    padding: 20px;
    background-color: #f8f9fa;
}
.container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    padding: 20px;
    margin-top: 20px;
}
.header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #dee2e6;
}
.remittance-details {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.btn-container {
    display: flex;
    justify-content: flex-end;
}

/* Print-specific styles */
@media print {
    body * {
        visibility: hidden;
    }
    #printSection, #printSection * {
        visibility: visible;
    }
    #printSection {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .print-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
    }
    .signature-section {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
    }
    .signature-box {
        width: 35%;
        text-align: center;
    }
    .signature-box #sign_particulars {
        text-align: left;
    }
    .signature-line {
        border-top: 1px solid #000;
        margin-top: 60px;
        padding-top: 5px;
    }
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Remittance</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Remittance</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Remittance Page -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-success">
              <div class="card-header">
                <!-- Remittance Tabs -->
                <ul class="nav nav-tabs card-header-tabs" id="generateRemittanceTabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active show" id="tab-generate-remit" data-toggle="tab" href="#_remit" role="tab" aria-selected="true">
                      <i class="fas fa-receipt"></i> Generate
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="tab-history-remit" data-toggle="tab" href="#_remitHistory" role="tab" aria-selected="false">
                      <i class="fas fa-history"></i> History
                    </a>
                  </li>
                </ul>
              </div>

              <div class="card-body">
                <div class="tab-content mt-3" id="generateRemittanceTabsContent">
                  
                  <!-- ========== Generate Remittance Pane ========== -->
                  <div class="tab-pane fade show active" id="_remit" role="tabpanel" aria-labelledby="tab-generate-remit"> 
                    <div class="row">
                      <div class="col-md-12 mt-0" style="top: -15px;">
                        <h5><i class="fas fa-file-invoice-dollar"></i> Payroll Remittances</h5>
                      </div>
                      <div class="col-md-8">
                        <!-- Filter Form -->
                        <form id="remittanceForm" class="form-inline mb-3">
                          <label for="remitYear" class="mr-2">Year:</label>
                          <select id="remitYear" class="form-control form-control-sm mr-3">
                            <option value="" hidden>Select Year...</option>
                            <?php echo ViewPayPeriodYearsDropdown(); ?>
                          </select>

                          <label for="remitPeriod" class="mr-2">Remittance Period:</label>
                          <select id="remitPeriod" class="form-control form-control-sm mr-3">
                            <option value="" hidden>Select Remittance Period...</option>
                          </select>
                          
                          <button type="button" class="btn btn-success btn-sm" id="btnGenerateRemittance">
                            <i class="fas fa-calculator"></i> Generate Remittances
                          </button>
                        </form>
                      </div>

                      <div class="col-md-4">
                        <div class="remittance-controls mb-3 text-md-right">
                          <label for="remitStatus">Remittance Status:</label>
                          <select id="remitStatus" class="form-control form-control-sm d-inline-block w-auto">
                            <option value="Pending">Pending</option>
                            <option value="Remitted">Remitted</option>
                          </select>

                          <button id="btnSaveRemittance" class="btn btn-success btn-sm ml-2">
                            <i class="fas fa-save"></i> Save Remittance
                          </button>
                        </div>
                        <!-- OR & Ref No Fields -->
                        <div id="remitExtraFields" class="mt-2" style="display:none;">
                          <input type="text" id="remitOrNo" class="form-control form-control-sm d-inline-block w-auto" placeholder="OR Number">
                          <input type="text" id="remitRefNo" class="form-control form-control-sm d-inline-block w-auto" placeholder="Reference No">
                        </div>
                      </div>
                    </div>

                    <!-- Remittance Type Tabs -->
                    <ul class="nav nav-tabs" id="remittanceTabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" href="#tax" role="tab"><i class="fas fa-receipt"></i> Withholding Tax</a>
                      </li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#gsis" role="tab">GSIS (L/R)</a></li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#gsis_ecc" role="tab">GSIS (ECC)</a></li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#philhealth" role="tab">PhilHealth</a></li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pagibig" role="tab">Pag-IBIG</a></li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#sss" role="tab">SSS</a></li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#loans" role="tab">Loans</a></li>
                      <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#others" role="tab">Others</a></li>
                    </ul>

                    <div class="tab-content mt-3">
                      <!-- Withholding Tax -->
                      <div class="tab-pane fade show active" id="tax" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <th>Employee</th>
                              <th>Position</th>
                              <th>TIN</th>
                              <th>Basic Salary</th>
                              <th>Tax Amount</th>
                            </tr>
                          </thead>
                          <tbody id="taxTable"></tbody>
                        </table>
                      </div>

                      <!-- GSIS -->
                      <div class="tab-pane fade" id="gsis" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <th>Employee</th><th>Position</th><th>GSIS BP No.</th>
                              <th>Basic Salary</th><th>Employee Share</th><th>Employer Share</th><th>Total</th>
                            </tr>
                          </thead>
                          <tbody id="gsisTable"></tbody>
                        </table>
                      </div>

                      <!-- GSIS ECC -->
                      <div class="tab-pane fade" id="gsis_ecc" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead><tr><th>Employee</th><th>Position</th><th>GSIS BP No.</th><th>Amount</th></tr></thead>
                          <tbody id="gsisECCTable"></tbody>
                        </table>
                      </div>

                      <!-- PhilHealth -->
                      <div class="tab-pane fade" id="philhealth" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead><tr><th>Employee</th><th>Position</th><th>PhilHealth No.</th><th>Basic Salary</th><th>Employee Share</th><th>Employer Share</th><th>Total</th></tr></thead>
                          <tbody id="philhealthTable"></tbody>
                        </table>
                      </div>

                      <!-- Pag-IBIG -->
                      <div class="tab-pane fade" id="pagibig" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead><tr><th>Employee</th><th>Position</th><th>MID No.</th><th>Basic Salary</th><th>Employee Share</th><th>Employer Share</th><th>Total</th></tr></thead>
                          <tbody id="pagibigTable"></tbody>
                        </table>
                      </div>

                      <!-- SSS -->
                      <div class="tab-pane fade" id="sss" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead><tr><th>Employee</th><th>Position</th><th>SSS No.</th><th>Contribution Amount</th></tr></thead>
                          <tbody id="sssTable"></tbody>
                        </table>
                      </div>

                      <!-- Loans -->
                      <div class="tab-pane fade" id="loans" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead><tr><th>Loan</th><th>Total Loan Amount</th><th class="text-center">Action</th></tr></thead>
                          <tbody id="loansTable"></tbody>
                        </table>
                      </div>

                      <!-- Others -->
                      <div class="tab-pane fade" id="others" role="tabpanel">
                        <table class="table table-bordered table-hover">
                          <thead><tr><th>Particulars</th><th>Total Amount</th><th class="text-center">Action</th></tr></thead>
                          <tbody id="othersTable"></tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <!-- ========== History Tab Pane (styled like Generate) ========== -->
                  <div class="tab-pane fade" id="_remitHistory" role="tabpanel" aria-labelledby="tab-history-remit">
                    <div class="row">
                      <div class="col-md-12 mt-0" style="top: -15px;">
                        <h5><i class="fas fa-history"></i> Remittance History</h5>
                      </div>
                      <div class="col-md-12">
                        <form id="remittanceFilterForm" class="form-inline mb-3">
                          <label for="remitYearHistory" class="mr-2">Year:</label>
                          <select id="remitYearHistory" class="form-control form-control-sm mr-3">
                            <option value="" hidden>Select Year...</option>
                            <?php echo ViewPayPeriodYearsDropdown(); ?>
                          </select>

                          <label for="remitType" class="mr-2">Remittance Type:</label>
                          <select id="remitType" class="form-control form-control-sm mr-3">
                            <option value="" hidden>Select Type...</option>
                            <optgroup label="Government Remittances">
                              <option value="tax">Withholding Tax (BIR)</option>
                              <option value="gsis">GSIS (L/R)</option>
                              <option value="gsis_ecc">GSIS (ECC)</option>
                              <option value="philhealth">PhilHealth</option>
                              <option value="pagibig">Pag-IBIG</option>
                              <option value="sss">SSS</option>
                            </optgroup>
                            <optgroup label="Payroll Deductions">
                              <option value="loans">Loans</option>
                              <option value="others">Other Payables</option>
                            </optgroup>
                          </select>

                          <button type="button" class="btn btn-primary btn-sm" id="btnViewRemittances">
                            <i class="fas fa-search"></i> View Remittances
                          </button>
                        </form>

                        <div class="table-responsive">
                          <table class="table table-bordered table-hover" id="remittanceListTable">
                            <thead>
                              <tr>
                                <th>Period</th>
                                <th>Type</th>
                                <th>OR Number</th>
                                <th>Reference No</th>
                                <th>Total Employee Share</th>
                                <th>Total Gov't Share</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>

                </div> <!-- /tab-content -->
              </div><!-- /card-body -->
            </div><!-- /card -->
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Modal for Breakdown -->
<div class="modal fade" id="loanBreakdownModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Loan Breakdown</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="loanBreakdownTable">
          <thead>
            <tr>
              <th class="text-center">Employee</th>
              <th class="text-center">Position</th>
              <th class="text-center">Amortization</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Remittance history details -->
<div class="modal fade" id="remittanceDetailsModal" tabindex="-1" role="dialog" aria-labelledby="remittanceDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="remittanceDetailsModalLabel">Remittance Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="ml-4" id="remittancePeriodLabel"></h6>
          </div>
          <div class="col-md-6 text-right">
            <!-- Print Button -->
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary btn-sm mb-3" id="btnPrintRemittanceDetails" data-remit_id="">
                    <i class="fas fa-print"></i> Print Remittance Details
                </button>
            </div>
          </div>
        </div>
        
        <div id="remittanceDetailsTable">
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th>Employee Name</th>
                <th>Remittance</th>
                <th class="text-center">Employee Share</th>
                <th class="text-center">Employer Share</th>
              </tr>
            </thead>
            <tbody id="remittanceDetailsBody">
              <tr><td colspan="4" class="text-center text-muted">No data available</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Remittance Loans history details where loan types are group -->
<div class="modal fade" id="remittanceLoansDetailsModal" tabindex="-1" role="dialog" aria-labelledby="remittanceLoansDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="remittanceLoansDetailsModalLabel">Loans Remittance Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
        <div class="row">
          <div class="col-md-6">
            <h6 class="ml-4" id="remittanceLoansPeriodLabel"></h6>
          </div>
          <div class="col-md-6 text-right">
            <!-- Print Button -->
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-primary btn-sm mb-3" id="btnPrintLoansRemittanceDetails" data-loan_remit_id="">
                <i class="fas fa-print"></i> Print Remittance Loans Details
              </button>
            </div>
          </div>
        </div>

        <div id="remittanceLoansDetailsTable">
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th>Loan Type</th>
                <th>Total Loan Amount</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="remittanceLoansDetailsBody">
              <tr><td colspan="3" class="text-center text-muted">No data available</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Remittance Loan Breakdown details -->
<div class="modal fade" id="loanRemitBreakdownModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loanRemitBreakdownTitle"></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="ml-4" id="loanRemitBreakdownPeriodLabel"></h6>
                  </div>
                  <div class="col-md-6 text-right">
                    <!-- Print Button -->
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-primary btn-sm" id="btnPrintLoanRemitBreakdown" data-loans_breakdown="" data-breakdown_period="">
                            <i class="fas fa-print"></i> Print Loan Remittance Breakdown
                        </button>
                    </div>
                  </div>
                </div>

                <div id="loanRemitBreakdownTable">
                  <!-- Loan Remittance Breakdown Table -->
                  <table class="table table-bordered table-striped" id="loanRemitBreakdownTable">
                      <thead>
                          <tr>
                              <th>Employee Name</th>
                              <th>Position</th>
                              <th class="text-right">Amount</th>
                          </tr>
                      </thead>
                      <tbody></tbody>
                  </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Remiitance Others history details where other deduction types are group -->
<div class="modal fade" id="remittanceOthersDetailsModal" tabindex="-1" role="dialog" aria-labelledby="remittanceOthersDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="remittanceOthersDetailsModalLabel">
          <i class="fas fa-file-invoice-dollar"></i> Other Payables Remittance Details
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
        <div class="row mb-3">
          <div class="col-md-6">
            <h6 class="text-muted" id="remittanceOthersPeriodLabel">
              <i class="fas fa-calendar-alt"></i> Period: <span></span>
            </h6>
          </div>
          <div class="col-md-6 text-right">
            <!-- Export and Print Buttons -->
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-secondary btn-sm" id="btnExportOthersRemittanceDetails" title="Export to CSV">
                <i class="fas fa-download"></i> Export CSV
              </button>
              <button type="button" class="btn btn-primary btn-sm" id="btnPrintOthersRemittanceDetails" data-others_remit_id="" title="Print Details">
                <i class="fas fa-print"></i> Print
              </button>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover table-sm">
            <thead class="thead-light">
              <tr>
                <th width="50%"><i class="fas fa-file-invoice"></i> Deduction Type</th>
                <th width="30%" class="text-right"><i class="fas fa-dollar-sign"></i> Total Amount</th>
                <th width="20%" class="text-center"><i class="fas fa-cogs"></i> Action</th>
              </tr>
            </thead>
            <tbody id="remittanceOthersDetailsBody">
              <tr><td colspan="3" class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
          <i class="fas fa-times-circle"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Remittance Others Breakdown details -->
<div class="modal fade" id="othersRemitBreakdownModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="othersRemitBreakdownTitle">
                  <i class="fas fa-users"></i> Employee Breakdown
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="row mb-3">
                  <div class="col-md-6">
                    <h6 class="text-muted" id="othersRemitBreakdownPeriodLabel">
                      <i class="fas fa-calendar-alt"></i> Period: <span></span>
                    </h6>
                  </div>
                  <div class="col-md-6 text-right">
                    <!-- Print Button -->
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" id="btnPrintOthersRemitBreakdown" data-others_breakdown="" data-breakdown_period="" title="Print Breakdown">
                            <i class="fas fa-print"></i> Print Breakdown
                        </button>
                    </div>
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover table-sm">
                      <thead class="thead-light">
                          <tr>
                              <th width="40%"><i class="fas fa-user-tie"></i> Employee Name</th>
                              <th width="40%"><i class="fas fa-briefcase"></i> Position</th>
                              <th width="20%" class="text-right"><i class="fas fa-dollar-sign"></i> Amount</th>
                          </tr>
                      </thead>
                      <tbody id="othersRemitBreakdownTableBody"></tbody>
                  </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                  <i class="fas fa-times-circle"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden print section -->
<div id="printSection" style="display: none;"></div>

<!-- Loader Overlay -->
<div id="Loader" style="display: none;">
  <div class="spinner text-primary" role="status" style="width: 3rem; height: 3rem;">
    <span class="sr-only">Loading...</span>
  </div>
</div>

<!-- jQuery & Plugins -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../dist/js/adminlte.js"></script>

<!-- Custom Scripts -->
<script src="system.js"></script>
<script src="scripts/remittance.js"></script>

<script>
$(document).ready(function() {
  // Force tabs active on load
  $('#generateRemittanceTabs a[href="#_remit"]').tab('show');
  $('#remittanceTabs a[href="#tax"]').tab('show');
});
</script>

</body>
</html>