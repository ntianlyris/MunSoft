<div class="modal fade" id="config_earnings_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- form start -->
            <form id="formEarningConfig" class="form-horizontal">
                <div class="modal-header">
                    <h4 class="modal-title">Earnings Manager</h4>
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
                                    <h5 class="card-title">Earning Details</h5>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" id="txtConfigEarningID">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtEarningAcctCode">Account Code</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtEarningAcctCode" name="earning_acct_code" placeholder="Ex: 50101010" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtEarningCode">Earning Code</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtEarningCode" name="earning_code" placeholder="Ex: Salaries-Reg" required>
                                            <small class="text-muted">⚠ Earning Codes to enter must be: Sal-Reg,Sal-Cas,PERA,RA,TA,SUB,HZD,LNDRY</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtEarningTitle">Earning Title</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtEarningTitle" name="earning_title" placeholder="Ex: Salaries-Regular(Basic Rate)" required>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEarningConfig"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

