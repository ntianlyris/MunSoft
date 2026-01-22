<div class="modal fade" id="employee_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- form start -->
            <form id="formEmployee" class="form-horizontal">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Employee Manager</h4>
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
                                    <h5 class="card-title">Employee Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md">
                                            <input type="hidden" id="txtEmployeeID">
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtEmployeeIDNum">Employee ID No.<span class="text-danger"> *</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="txtEmployeeIDNum" name="employee_id_num" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtFirstName">First Name<span class="text-danger"> *</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="txtFirstName" name="firstname" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtMiddleName">Middle Name</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="txtMiddleName" name="middlename" placeholder="">
                                                </div>
                                            </div> 
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtLastName">Last Name<span class="text-danger"> *</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="txtLastName" name="lastname" placeholder="" required>
                                                </div>
                                            </div>     
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtExtension">Ext.</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="txtExtension" name="extension" placeholder="Jr., Sr., etc.">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtBirthdate">Birthdate<span class="text-danger"> *</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control form-control-sm" id="txtBirthdate" name="birthdate" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="cmbGender">Gender<span class="text-danger"> *</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select class="form-control form-control-sm" id="cmbGender" style="width: 100%;" required>
                                                        <option value="" selected hidden>Select Gender...</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="cmbCivilStatus">Civil Status<span class="text-danger"> *</span></label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select class="form-control form-control-sm" id="cmbCivilStatus" style="width: 100%;" required>
                                                        <option value="" selected hidden>Select Civil Status...</option>
                                                        <option value="Single">Single</option>
                                                        <option value="Married">Married</option>
                                                        <option value="Widow">Widow</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtAddress">Address<span class="text-danger"> *</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="txtAddress" name="address" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtProfExpertise">Profession/Expertise<span class="text-danger"> *</span></label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm" id="txtProfExpertise" name="profExpertise" placeholder="ex: Lawyer, Engineer, IT Specialist, etc." required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="txtDateHired">Date Hired<span class="text-danger"> *</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control form-control-sm" id="txtDateHired" name="datehired" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    <label for="cmbEmpStatus">Employee Status<span class="text-danger"> *</span></label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select class="form-control form-control-sm" id="cmbEmpStatus" style="width: 100%;" required>
                                                        <option value="Active" selected>Active</option>
                                                        <option value="On-Leave">On-Leave</option>
                                                        <option value="Suspended">Suspended</option>
                                                        <option value="Resigned">Resigned</option>
                                                        <option value="Transferred">Transferred</option>
                                                        <option value="Retired">Retired</option>
                                                        <option value="Terminated">Terminated</option>
                                                        <option value="Laid Off">Laid Off</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEmployee"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->