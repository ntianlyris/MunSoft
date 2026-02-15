<div class="modal fade" id="employment_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- form start -->
            <form id="formEmployment" class="form-horizontal">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Employment Manager</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-10 offset-1">
                            <h5>Employment Details</h5>
                            <hr>

                            <div class="row">
                                <input type="hidden" id="txtEmployeeID_1">
                                <input type="hidden" id="txtEmploymentID">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtEmployeeIDNum_1">Employee ID No.<span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control form-control-sm" id="txtEmployeeIDNum_1" name="employee_id_num" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="txtFullName">Name<span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control form-control-sm" id="txtFullName" name="fullname" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="txtEmploymentRefNum">Employment Ref. No.<span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control form-control-sm" id="txtEmploymentRefNum" name="employmentRefNum" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cmbEmpType">Employment Type<span class="text-danger"> *</span></label>
                                        <select class="form-control form-control-sm" id="cmbEmpType" required>
                                            <option value="" selected hidden>Select Type...</option>
                                            <option value="Regular">Regular</option>
                                            <option value="Casual">Casual</option>
                                            <option value="JO">Job Order</option>
                                            <option value="COS">Contract of Service</option>
                                            <option value="MOA">MOA Personnel</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cmbPosition">Position Item<span class="text-danger"> *</span></label>
                                        <select class="form-control form-control-sm select2" id="cmbPosition" disabled>
                                            <option value="" selected hidden>Select Position...</option>
                                            <?php echo ViewPositionsDropdown(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cmbDepartment">Department<span class="text-danger"> *</span></label>
                                        <select class="form-control form-control-sm" id="cmbDepartment" disabled>
                                            <option value="" selected hidden>Select Department...</option>
                                            <?php echo ViewDepartmentsDropdown(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cmbDepartmentAssigned">Office Assignment<span class="text-danger"> *</span></label>
                                        <select class="form-control form-control-sm" id="cmbDepartmentAssigned" required>
                                            <option value="" selected hidden>Select Office Assignment...</option>
                                            <?php echo ViewDepartmentsDropdown(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="txtEmploymentDateStart">Start of Employment<span class="text-danger"> *</span></label>
                                        <input type="date" class="form-control form-control-sm" id="txtEmploymentDateStart" name="employmentDateStart" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="txtEmploymentDateEnd">End of Employment</label>
                                        <input type="date" class="form-control form-control-sm" id="txtEmploymentDateEnd" name="employmentDateEnd">
                                    </div>
                                    <div class="form-group">
                                        <label for="txtDesignation">Designation</label>
                                        <input type="text" class="form-control form-control-sm" id="txtDesignation" name="designation">
                                    </div>
                                    <div class="form-group">
                                        <label for="cmbWOrkNature">Nature of Work<span class="text-danger"> *</span></label>
                                        <select class="form-control form-control-sm" id="cmbWOrkNature" required>
                                            <option value="" selected hidden>Select Nature of Work...</option>
                                            <option value="Supervisory">Supervisory</option>
                                            <option value="Administrative">Administrative</option>
                                            <option value="Development">Development</option>
                                            <option value="Office Staff">Office Staff</option>
                                            <option value="Utility">Utility</option>
                                            <option value="Operations">Operations</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cmbWorkSpecifics">Work Specifics</label>
                                        <select class="form-control form-control-sm" id="cmbWorkSpecifics" required>
                                            <option value="" selected hidden>Select Work Specifics...</option>
                                            <option value="Clerical Services">Clerical Services</option>
                                            <option value="Financial Services">Financial Services</option>
                                            <option value="Health Services">Health Services</option>
                                            <option value="ICT Services">ICT Services</option>
                                            <option value="Janitorial Services">Janitorial Services</option>
                                            <option value="Security Services">Security Services</option>
                                            <option value="Teaching Services">Teaching Services</option>
                                            <option value="Technical Services">Technical Services</option>
                                            <option value="Manual Labour">Manual Labour</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="txtEmploymentParticulars">Particulars<span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control form-control-sm" id="txtEmploymentParticulars" name="empParticulars" placeholder="e.g. New, Promotion, etc." required>
                                    </div>
                                    <div class="form-group">
                                        <label for="txtRate">Basic Rate<span class="text-danger"> *</span></label>
                                        <input type="text" class="form-control form-control-sm" id="txtRate" name="empRate" placeholder="0.00" required>
                                    </div>
                                </div>
                                <!-- Optional: add second column if needed or leave empty -->
                                <div class="col-md-6"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <?php
                        $hideSaveBtn = (isset($GLOBALS['user_role_for_js']) && $GLOBALS['user_role_for_js'] === 'Employee') ? 'style="display:none;"' : '';
                    ?>
                    <button type="button" class="btn btn-primary" id="btnSaveEmployment" <?php echo $hideSaveBtn; ?>><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


