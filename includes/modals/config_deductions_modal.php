<div class="modal fade" id="config_deductions_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- form start -->
            <form id="formDeductionConfig" class="form-horizontal">
                <div class="modal-header">
                    <h4 class="modal-title">Deductions Manager</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!--Client Details-->
                        <div class="col-sm-10 offset-1">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">Deduction Details</h5>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" id="txtConfigDeductionID">
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <label for="cmbDeductionType">Deduction Type</label>
                                        </div>
                                        <div class="col-8">
                                            <select class="form-control form-control-sm" id="cmbDeductionType" name="deduction_type" style="width: 100%;" required>
                                                <option value="" selected hidden>Select Deduction Type...</option>
                                                <?php echo ViewDeductionTypesDropdown(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtDeductionAcctCode">Account Code</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtDeductionAcctCode" name="deduction_acct_code" placeholder="Ex: 20201010-1" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtDeductionCode">Deduction Code</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtDeductionCode" name="deduction_code" placeholder="Ex: TAX" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtDeductionTitle">Deduction Title</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtDeductionTitle" name="deduction_title" placeholder="Ex: Withholding Tax" required>
                                        </div>
                                    </div>    
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="isEmployeeShare">Is Employee Share?</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" id="isEmployeeShare" name="is_employee_share" style="width: 100%;" required>
                                                <option value="0" selected>No</option>
                                                <option value="1">YES</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="cmbDeductCategory">Category</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" id="cmbDeductCategory" name="deduction_category" style="width: 100%;" required>
                                                <option value="" selected hidden>Select Category...</option>
                                                <option value="STATUTORY">STATUTORY</option>
                                                <option value="LOAN">LOAN</option>
                                                <option value="UNION">UNION</option>
                                                <option value="OTHER">OTHER</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveDeductionConfig"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

