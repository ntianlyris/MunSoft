//-----For Sidebar link highlight-----//
$( document ).ready(function(){
    var url = window.location.pathname;
    var filename = url.substring(url.lastIndexOf('/')+1);
    var file = filename.split('.');
    var linkname = file[0];
    var clicked_li = $("#"+linkname);

    $(".nav-link").removeClass("active");
    clicked_li.addClass("active");
});

$( document ).ready(function(){
    $('#btnSaveDepartment').click(function() {
        var form = document.getElementById('formDepartment');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveDepartment();
        }
    });
});


//-----Ajax-----//
var xhr = new XMLHttpRequest();

//-----Department-----//
function SaveDepartment(){
    var dept_id = $('#txtDeptID').val();
    var dept_code = $('#txtDeptCode').val();
    var dept_title = $('#txtDeptTitle').val();
    var dept_name = $('#txtDeptName').val();

    var url = 'save_settings.php';
    var submit = 'SaveDepartment';
    var data = "submit="+submit;
        data += "&dept_id="+dept_id;
        data += "&dept_code="+dept_code;
        data += "&dept_title="+dept_title;
        data += "&dept_name="+dept_name;

    xhr.open("POST", url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send(data);
    xhr.onreadystatechange=function(){ 
        if (xhr.readyState == 4){ 
            var obj = $.parseJSON(xhr.responseText);
            var result = obj.result;
            if(result == "success"){
                Swal.fire({
                    title: 'Success',
                    text: 'Department Saved.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'settings_departments.php';
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to save Department. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'settings_departments.php';
                        }
                });
            }
        } 
    }
}

function GetDepartmentDetails(dept_id){
    var action = 'get_dept_details';

    $.ajax({
        type: "GET",
        url: "get.php",
        data: {"action" : action, "deptid" : dept_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtDeptID').val(obj['dept_id']);
                $('#txtDeptCode').val(obj['dept_code']);
                $('#txtDeptTitle').val(obj['dept_title']);
                $('#txtDeptName').val(obj['dept_name']);
        }
    });
}

function DeleteDepartment(dept_id){
    var action = 'delete_dept';
    var url = 'save_settings.php';
    
    Swal.fire({
        title: 'Delete Department Data',
        text: "Other data may be affected. Are you sure to delete this department?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes Delete'
      }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {"submit" : action, "deptid" : dept_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Department Deleted.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'settings_departments.php';
                                    }
                            });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to Delete Department Data.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'settings_departments.php';
                                    }
                            });
                        }
                    }
                });
            }
            else{ result.dismiss === Swal.DismissReason.cancel; }
        });
}

//-----System User Management-----//
function DeleteSystemUser(admin_id){
    var url = "save_settings.php";
	
    Swal.fire({
        title: 'Delete User',
        text: "Do you want to delete this user?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                    url: url,
                    data: {"submit" : "delete_user", "Data" : admin_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if (result == 'deleted') {
                            Swal.fire({
                                title: 'Success',
                                text: "User deleted successfully.",
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  window.location.href = 'user_management.php';
                                }
                              });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: "Failed to delete user!",
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  window.location.href = 'user_management.php';
                                }
                              });
                        }
                    }
            });
        }
        else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {}
      });  
}




