<div class="modal fade" id="position_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- form start -->
            <form id="formPositionItem" class="form-horizontal">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Position Item Manager</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!--Position Details-->
                        <div class="col-sm-10 offset-1">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">Position Item Details</h5>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" id="txtPositionID" value="">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtPosRefNum">Position Ref. No.<span class="text-danger"> *</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtPosRefNum" name="posRefNum" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtPosItemNum">Plantilla Item No.</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtPosItemNum" name="itemNum" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtItemtTitle">Position Item Title<span class="text-danger"> *</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtItemtTitle" name="itemTitle" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtSalaryGrade">Salary Grade<span class="text-danger"> *</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-sm" id="txtSalaryGrade" name="salaryGrade" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="cmbPosType">Type<span class="text-danger"> *</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" id="cmbPosType" name="positionType" style="width: 100%;" required>
                                                <option value="" selected hidden>Select Position Type</option>
                                                <option value="0">Permanent</option>
                                                <option value="1">Temporary</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="cmbDepartment">Department<span class="text-danger"> *</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" id="cmbDepartment" name="deptID" style="width: 100%;" required>
                                                <option value="" selected hidden>Select Department...</option>
                                                <?php echo ViewDepartmentsDropdown(); ?>
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="cmbPositionStatus">Status<span class="text-danger"> *</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select class="form-control form-control-sm" id="cmbPositionStatus" name="positionStatus" style="width: 100%;" required>
                                                <option value="0" selected>Vacant</option>
                                                <option value="1">Active</option>
                                                <option value="2">Unfunded</option>
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
                    <button type="button" class="btn btn-primary" id="btnSavePositionItem"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

