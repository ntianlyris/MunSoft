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
        <div class="row">
            <div class="col-12">
                <!-- Button trigger modal -->
                <div class="btn-group myButton">
                    <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#systemUserModal"><i class="fa fa-plus-circle"></i> Add System User</button>
                </div>  
                <!-- Button trigger modal 
                <div class="btn-group myButton">
                    <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#userRoleModal"><i class="fa fa-plus-circle"></i> Add New Role</button>
                </div>-->
                <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">System Users</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Username</th>
                                <th class="text-center">Employee Name</th>
                                <th class="text-center">Roles</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
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
                                                            <td>' . $count . '</td>                                                       
                                                            <td>' . $value['username'] . '</td>
                                                            <td><a href="employee_profile.php?emp_id='.$value['employee_id'].'">' . $name . '</a></td>
                                                            <td>';

                                                    foreach ($roles as $key => $role) {
                                                        echo $key.'<br/>';
                                                    }

                                                    echo '</td>
                                                            <td>' . $value['status'] . '</td>
                                                            <td class="text-center">
                                                                <div class="btn-group">
                                                                    <button 
                                                                        class="btn btn-danger btn-flat btn-sm" 
                                                                        onclick="DeleteSystemUser('.$value['userID'].');" 
                                                                        data-toggle="tooltip" 
                                                                        title="Remove User" 
                                                                        data-placement="bottom">
                                                                        <i class="fas fa-times-circle"></i> Remove
                                                                    </button>
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
                <!-- /.card -->
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
                                            <select id="cmbUser" name="userID" class="form-control form-control-sm" placeholder="" required>
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
                                            <select id="cmbEmployeeData" name="employeeID" class="form-control form-control-sm" placeholder="" required>
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
                                            <select id="cmbUserRole" name="UserRoles[]" class="form-control form-control-sm" placeholder="" required>
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
<script src="system.js"></script>


<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "paging": true,
      "searching": true,
      "buttons": ["excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
<script type="text/javascript">
    $( document ).ready(function(){
        var query = getQuery();
        var add = query.add;
        switch (add) {
            case '0':
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to add User!',
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
        
            default:
                break;
        }
    });
</script>
</body>
</html>