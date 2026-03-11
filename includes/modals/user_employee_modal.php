<div class="modal fade" id="user_employee_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- form start -->
            <form class="form-horizontal" id="formUserEmployee">
                <div class="modal-header">
                    <h4 class="modal-title">Manage User for Employee</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div class="col-sm-10 offset-1">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                <h5 class="card-title">Add User to Employee</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-sm-4">Registered User:</label>
                                        <div class="col-sm-8">          
                                            <select id="cmbUser" class="form-control form-control-sm select2" placeholder="" required>
                                                <option value="" selected disabled hidden>Select User...</option>       
                                                <?php
                                                    include_once('../includes/class/Admin.php'); 
                                                    $MyUser = new Admin();
                                                    $users = $MyUser->getRegisteredUsers();
                                                    if($users){
                                                        foreach ($users as $key => $value) {
                                                            echo "<option value='".$value["userID"]."'>". $value['username'] ."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-4">Employee Data:</label>
                                        <div class="col-sm-8">
                                            <select id="cmbEmployeeData" class="form-control form-control-sm select2" placeholder="" required>
                                                <option value="" selected disabled hidden>Select Employee Data...</option>
                                                <?php
                                                    include_once('../includes/class/Employee.php'); 
                                                    $MyEmployee = new Employee();
                                                    $employees = $MyEmployee->GetEmployeesWithoutUser();

                                                    if($employees){
                                                        foreach ($employees as $value) {
                                                            echo "<option value='".$value["employee_id"]."'>". $value['firstname'] .' '.$value['lastname'] . ' ' .$value['extension'] ."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveUserEmployee"><i class="fas fa-save"></i> Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->