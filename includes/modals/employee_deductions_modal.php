<div class="modal fade" id="deductionsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Deductions History</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <h5><span id="modalEmployeeName"></span></h5>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Particulars</th>
                    <th class="text-center">Effectivity Date</th>
                    <th class="text-center">End Date</th>
                    <th class="text-center">Total Deductions</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody id="deductions-history-body">
                  <!-- JS will inject rows here -->
                </tbody>
              </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="employee_deductions_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- form start -->
            <form id="formEmployeeDeduction" class="form-horizontal">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Manage Employee Deductions</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!--Client Details-->
                        <div class="col-sm-10 offset-1">
                            <input type="hidden" id="txtEmployeeDeductionID" name="employee_deduction_id">
                            <input type="hidden" id="txtEmployeeID" name="employee_id">
                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="cmbEmployee">Employee</label>
                                </div>
                                <div class="col-8">
                                    <select class="form-control form-control-sm select2" id="cmbEmployee" name="employee_id" style="width: 100%;" required>
                                        <option value="" selected hidden>Select Employee...</option>
                                        <?php echo ViewEmployeesDropdown(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="txtDeductionParticulars">Particulars</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control form-control-sm" id="txtDeductionParticulars" name="deduction_particulars" placeholder="e.g. New Deduction, New Loan/Renewal/Consolidation, Dues, etc." required>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="txtEffectiveDate">Effectivity Date</label>
                                </div>
                                <div class="col-8">
                                    <input type="date" class="form-control form-control-sm" id="txtEffectiveDate" name="effective_date" required>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="cmbDeductionCodes">Deduction Code</label>
                                </div>
                                <div class="col-8">
                                    <div class="input-group">
                                        <select class="form-control form-control-sm" id="cmbDeductionCodes" style="width: 90%;">
                                            <option value="" selected hidden>Select Deduction...</option>
                                            <?php echo ViewConfigDeductionsDropdown(); ?>
                                        </select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary btn-sm btn-flat" type="button" id="btnAddDeductionCode" onclick="" data-toggle="tooltip" title="Add Deduction" data-placement="bottom">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="deductionCodesInputs">
                            </div>
                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="txtEmployeeDeductionTotal">Total Deductions</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control form-control-sm" style="text-align:right;" id="txtEmployeeDeductionTotal" placeholder="0.00" read-only disabled>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEmployeeDeductions"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

