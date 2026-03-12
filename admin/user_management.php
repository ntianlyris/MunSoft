<?php
    include_once '../includes/layout/head.php';
    include_once '../includes/layout/navbar.php';
    include_once '../includes/layout/sidebar.php';
    include_once '../includes/view/view.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">User Management</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">User Management</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#systemUserModal"><i class="fa fa-plus-circle"></i> Add System User</button>
                    <button type="button" class="btn btn-info btn-flat" data-toggle="modal" data-target="#userRoleModal"><i class="fa fa-plus-circle"></i> Add New Role</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users-cog"></i> System Users</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th class="text-center">Username</th>
                                    <th class="text-center">Employee Name</th>
                                    <th class="text-center">Roles</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <?php
                                        include_once('../includes/class/Admin.php');
                                                  
                                        $MyAdmin = new Admin();
                                                  
                                        $users = $MyAdmin->getAdminUsers();
    
                                                if($users != null){
                                                    $count = 0;
                                                    foreach($users as $value) {
                                                        $count++;
                                                        $name = $value['firstname']." ".$value['lastname'];
    
                                                        $roles = $MyAdmin->initRoles($value['userID']);
           
                                                        echo '<tr> 
                                                                <td class="text-center">' . $count . '</td>                                                       
                                                                <td>' . $value['username'] . '</td>
                                                                <td><a href="employee_profile.php?emp_id='.$value['employee_id'].'">' . $name . '</a></td>
                                                                <td>';
    
                                                        foreach ($roles as $key => $role) {
                                                            echo '<span class="badge badge-info mr-1">'.$key.'</span>';
                                                        }
    
                                                        echo '</td>
                                                                <td class="text-center"><span class="badge badge-'.($value['status'] == 'active' ? 'success' : 'secondary').'">' . ucfirst($value['status']) . '</span></td>
                                                                <td class="text-center">
                                                                    <button 
                                                                        class="btn btn-outline-danger btn-xs" 
                                                                        onclick="DeleteSystemUser('.$value['adminID'].');" 
                                                                        data-toggle="tooltip" 
                                                                        title="Remove User">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td> 
                                                        </tr>';
                                                 
                                                    }   
                                                }
    
                                    ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->

            <div class="col-lg-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-shield"></i> System Roles</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <table id="roleTable" class="table table-hover table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th class="text-center" width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    include_once('../includes/class/Role.php');
                                    $all_roles = Role::getRoles();
                                    if($all_roles){
                                        foreach($all_roles as $role){
                                            $perms = Role::getRolePerms($role['roleID']);
                                            echo '<tr>
                                                    <td class="font-weight-bold">'.$role['roleName'].'</td>
                                                    <td>';
                                            if($perms){
                                                foreach($perms as $p){
                                                    echo '<small class="d-block text-muted"><i class="fas fa-check text-success mr-1"></i>'.$p.'</small>';
                                                }
                                            } else {
                                                echo '<small class="text-muted">No permissions</small>';
                                            }
                                            echo '</td>
                                                  <td class="text-center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-outline-info btn-xs" onclick="EditRole('.$role['roleID'].')" title="Edit Role"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-outline-danger btn-xs" onclick="DeleteRole('.$role['roleID'].')" title="Delete Role"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                  </td>
                                                  </tr>';
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->

        <div class="modal fade" id="systemUserModal">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="save_settings.php">
                    <div class="modal-header">
                    <h4 class="modal-title">Manage System User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-10 offset-1">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                <h5 class="card-title">Add System User</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-sm-4">Registered User:</label>
                                        <div class="col-sm-8">          
                                            <select id="cmbUser" name="userID" class="form-control form-control-sm select2" placeholder="" required>
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
                                            <select id="cmbEmployeeData" name="employeeID" class="form-control form-control-sm select2" placeholder="" required>
                                                <option value="" selected disabled hidden>Select Employee Data...</option>
                                                <?php
                                                    include_once('../includes/class/Employee.php'); 
                                                    $MyEmployee = new Employee();
                                                    $employees = $MyEmployee->GetEmployees();
    
                                                    if($employees){
                                                        foreach ($employees as $value) {
                                                            echo "<option value='".$value["employee_id"]."'>". $value['firstname'] .' '.$value['lastname'] . ' ' .$value['extension'] ."</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-sm-4" for="">Roles:</label>
                                        <div class="col-sm-8">     
                                            <select id="cmbUserRole" name="UserRoles[]" class="form-control form-control-sm select2" placeholder="" required>
                                                <option value="" selected disabled hidden>Select Role...</option>
                                                <?php
                                                    include_once('../includes/class/Role.php'); 
                                                    $roles = Role::getRoles();
                                                    if($roles != null){
                                                        foreach($roles as $row) {
                                                            echo "<option value='".$row['roleID']."'>". $row['roleName'] ."</option>";
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
                        <button type="submit" class="btn btn-primary" id="" name="submit" value="SaveSystemUser"><i class="fas fa-save"></i> Save changes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div class="modal fade" id="userRoleModal" tabindex="-1" role="dialog" aria-labelledby="userRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-info color-palette">
                    <h4 class="modal-title" id="userRoleModalLabel">Manage Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <form class="form-horizontal" id="formRole" method="POST" name="form_role" action="save_settings.php">
                        <input type="hidden" name="role_id" id="txtRoleID">
                        <div class="modal-body">
                                
                                <div class="form-group">
                                    <label class="font-weight-bold">Role Name:</label>
                                    <input type="text" class="form-control" id="txtRoleName" name="role_name" placeholder="Enter Role Name" required>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Permissions:</label>
                                    <div class="card card-outline card-secondary">
                                        <div class="card-body">
                                            <div class="row">
                                            <?php
        
                                                include_once('../includes/class/Role.php');
        
                                                $perms = Role::getPerms();
        
                                                if($perms != null){
                                                    foreach($perms as $row) {
                                                        echo '<div class="col-sm-6 mb-2">
                                                                <div class="custom-control custom-switch">
                                                                    <input class="custom-control-input perm-check" type="checkbox" id="perm_'.$row['perm_id'].'" name="role_perms[]" value="'.$row['perm_id'].'">
                                                                    <label for="perm_'.$row['perm_id'].'" class="custom-control-label font-weight-normal">'.$row['perm_desc'].'</label>
                                                                </div>
                                                              </div>';
                                                    }   
                                                }
        
                                            ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-flat" name="submit" value="AddUserRole"><i class="fa fa-save"></i> Save Role</button>    
                            <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>       
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>




<?php $page_title = 'User Management'; include_once '../includes/layout/appfooter.php'; ?>
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>

<!-- System Engine -->
<script src="system.js?v=<?php echo time(); ?>"></script>


<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2({
      theme: 'bootstrap4',
      width: '100%',
      dropdownParent: $('#systemUserModal') // ensures it works inside modal
    });

    $("#example1").DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "paging": true,
      "searching": true,
      "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#roleTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "paging": true,
        "searching": true,
        "info": false
    });
  });
</script>
<script type="text/javascript">
    $( document ).ready(function(){
        var query = getQuery();
        var add = query.add;
        var add_role = query.add_role;

        if (add) {
            switch (add) {
                case '0':
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to add System User!',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'user_management.php';
                            }
                    });
                    break;
                case '1':
                    Swal.fire({
                        title: 'Success',
                        text: 'New System User Added!',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'user_management.php';
                            }
                    });
                    break;
            }
        }

        if (add_role) {
            switch (add_role) {
                case '0':
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to add New Role!',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'user_management.php';
                            }
                    });
                    break;
                case '1':
                    Swal.fire({
                        title: 'Success',
                        text: 'New Role and Permissions Added!',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'user_management.php';
                            }
                    });
                    break;
            }
        }
    });
</script>
</body>
</html>