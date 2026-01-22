<div class="modal fade" id="earningsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Earnings History</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <h5><span id="modalEmployeeName"></span></h5>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Position</th>
                    <th class="text-center">Particulars</th>
                    <th class="text-center">Effectivity Date</th>
                    <th class="text-center">End Date</th>
                    <th class="text-center">Basic Rate</th>
                    <th class="text-center">Gross Amount</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody id="earnings-history-body">
                  <!-- JS will inject rows here -->
                </tbody>
              </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="employee_earnings_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- form start -->
            <form id="formEmployeeEarning" class="form-horizontal">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Manage Employee Earnings</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!--Client Details-->
                        <div class="col-sm-10 offset-1">
                            <div class="row">
                                <input type="hidden" id="txtEmployeeEarningID" name="employee_earning_id">
                                <input type="hidden" id="txtEmployeeEmploymentID" name="employment_id">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="cmbEmployee">Employee</label>
                                        </div>
                                        <div class="col-7">
                                            <select class="form-control form-control-sm select2" id="cmbEmployee" name="employee_id" style="width: 100%;" required>
                                                <option value="" selected hidden>Select Employee...</option>
                                                <?php echo ViewEmployeesDropdown(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="txtEmpPosition">Position</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="text" class="form-control form-control-sm" id="txtEmpPosition" name="emp_position" readonly>
                                        </div>
                                    </div> 
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="txtEmpType">Type</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="text" class="form-control form-control-sm" id="txtEmpType" name="emp_type" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="txtEarningParticulars">Particulars</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="text" class="form-control form-control-sm" id="txtEarningParticulars" name="earning_particulars" placeholder="e.g. Entry-level, Promotion, Step Increment, Salary Increase, etc." readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="txtEffectiveDate">Effectivity</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="date" class="form-control form-control-sm" id="txtEffectiveDate" name="effective_date" readonly>
                                        </div>
                                    </div> 
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="txtBasicRate">Basic Rate</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="text" class="form-control form-control-sm" id="txtBasicRate" name="basic_rate" placeholder="0.00" readonly>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="cmbEarningCodes">Earning Code</label>
                                        </div>
                                        <div class="col-9">
                                            <div class="input-group">
                                                <select class="form-control form-control-sm" id="cmbEarningCodes" style="width: 90%;">
                                                    <option value="" selected hidden>Select Earning...</option>
                                                    <?php echo ViewConfigEarningsDropdown(); ?>
                                                </select>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-primary btn-sm btn-flat" type="button" id="btnAddEarningCode" onclick="" data-toggle="tooltip" title="Add Earning" data-placement="bottom">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="earningCodesInputs">    <!-- JS will inject earning codes here -->
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-3">
                                            <label for="txtEmployeeEarningTotal">Total Earnings</label>
                                        </div>
                                        <div class="col-9">
                                            <input type="text" class="form-control form-control-sm" style="text-align:right;" id="txtEmployeeEarningTotal" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                              
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEmployeeEarnings"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->



