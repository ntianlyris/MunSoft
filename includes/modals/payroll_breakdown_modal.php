<div class="modal fade" id="payrollBreakdownModal" tabindex="-1" role="dialog" aria-labelledby="payrollModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="payrollModalLabel">Payroll Breakdown</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h5 id="employeeName"></h5>
        <hr>
        <div class="row">
          <div class="col-md-4">
            <h6>Government Shares</h6>
            <table class="table table-sm table-bordered" id="govsharesTable">
              <thead><tr><th>Description</th><th class="text-right">Amount (₱)</th></tr></thead>
              <tbody></tbody>
            </table>
          </div>
          <div class="col-md-4">
            <h6>Earnings</h6>
            <table class="table table-sm table-bordered" id="earningsTable">
              <thead><tr><th>Description</th><th class="text-right">Amount (₱)</th></tr></thead>
              <tbody></tbody>
            </table>
          </div>
          <div class="col-md-4">
            <h6>Deductions</h6>
            <table class="table table-sm table-bordered" id="deductionsTable">
              <thead><tr><th>Description</th><th class="text-right">Amount (₱)</th></tr></thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <hr>
        <div class="text-right">
          <p><strong>Gross Pay:</strong> <span id="grossTotal"></span></p>
          <p><strong>Total Deductions:</strong> <span id="deductionTotal"></span></p>
          <p><strong>Net Pay:</strong> <span id="netTotal"></span></p>
        </div>
      </div>
    </div>
  </div>
</div>