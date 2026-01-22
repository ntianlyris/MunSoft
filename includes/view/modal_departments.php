<div class="modal fade" id="department_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- form start -->
            <form id="formDepartment" class="form-horizontal">
                <div class="modal-header">
                    <h4 class="modal-title">Department Manager</h4>
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
                                    <h5 class="card-title">Department Details</h5>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" id="txtDeptID" value="">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtDeptCode">Code</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtDeptCode" name="dept_code" placeholder="Ex: 3-01-010" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtDeptTitle">Title</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtDeptTitle" name="dept_title" placeholder="Ex: MMO" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label for="txtDeptName">Name</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="txtDeptName" name="dept_name" placeholder="Ex: Mun. Mayor's Office" required>
                                        </div>
                                    </div>      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveDepartment"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

