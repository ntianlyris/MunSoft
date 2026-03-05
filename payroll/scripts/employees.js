//-----Ajax-----//
var xhr = new XMLHttpRequest();

function CalculateAge(dob){
    var birthdate = new Date(dob);
    var month_diff = Date.now() - birthdate.getTime();
    var age_date_object = new Date(month_diff);
    var year = age_date_object.getUTCFullYear();
    return Math.abs(year - 1970);
}

jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});

$( document ).ready(function(){
    $('#btnSaveEmployee').click(function() {
        var form = document.getElementById('formEmployee');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveEmployee();
        }
    });
});

$( document ).ready(function(){
    $('#btnSaveUserEmployee').click(function() {
        var form = document.getElementById('formUserEmployee');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveUserEmployee();
        }
    });
});

$( document ).ready(function(){
    $('#btnSaveEmployment').click(function() {
        var form = document.getElementById('formEmployment');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveEmployment();
        }
    });
});

function SaveEmployee(){
    var employee_id = $('#txtEmployeeID').val();
    var employee_id_num = $('#txtEmployeeIDNum').val();
    var firstname = $('#txtFirstName').val();
    var middlename = $('#txtMiddleName').val();
    var lastname = $('#txtLastName').val();
    var extension = $('#txtExtension').val();
    var birthdate = $('#txtBirthdate').val();
    var gender = $('#cmbGender').val();
    var civil_status = $('#cmbCivilStatus').val();
    var address = $('#txtAddress').val();    
    var prof_expertise = $('#txtProfExpertise').val();  
    var date_hired = $('#txtDateHired').val();  
    var employee_status = $('#cmbEmpStatus').val();  

    var url = 'save.php';
    var action = 'SaveEmployee';
    var data = "action="+action;
        data += "&employee_id="+employee_id;
        data += "&employee_id_num="+employee_id_num;
        data += "&firstname="+firstname;
        data += "&middlename="+middlename;
        data += "&lastname="+lastname;
        data += "&extension="+extension;
        data += "&birthdate="+birthdate;
        data += "&gender="+gender;
        data += "&civil_status="+civil_status;
        data += "&address="+address;
        data += "&prof_expertise="+prof_expertise;
        data += "&date_hired="+date_hired;
        data += "&employee_status="+employee_status;

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
                    text: 'Employee Data Saved.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to save Employee Data. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
            }
        } 
    }
}

function GetEmployeeDetails(employee_id){
    var action = 'get_employee_details';

    $.ajax({
        type: "GET",
        url: "get.php",
        data: {"action" : action, "employee_id" : employee_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtEmployeeID').val(obj['employee_id']);
                $('#txtEmployeeIDNum').val(obj['employee_id_num']);
                $('#txtFirstName').val(obj['firstname']);
                $('#txtMiddleName').val(obj['middlename']);
                $('#txtLastName').val(obj['lastname']);
                $('#txtExtension').val(obj['extension']);
                $('#txtBirthdate').val(obj['birthdate']);
                $('#cmbGender').val(obj['gender']);
                $('#cmbCivilStatus').val(obj['civil_status']);
                $('#txtAddress').val(obj['address']);
                $('#txtProfExpertise').val(obj['prof_expertise']); 
                $('#txtDateHired').val(obj['hire_date']);
        }
    });
}

function GetEmployeeDetailsForEmployment(employee_id){
    var action = 'get_employee_details';

    $.ajax({
        type: "GET",
        url: "get.php",
        data: {"action" : action, "employee_id" : employee_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtEmployeeID_1').val(obj['employee_id']);
                $('#txtEmployeeIDNum_1').val(obj['employee_id_num']);
                $('#txtFullName').val(obj['lastname'] + ", " + obj['firstname'] + " " + obj['extension'] + " " + obj['middlename'].charAt(0));
        }
    });
}

function DeleteEmployee(employee_id){
    var action = 'delete_employee';
    var url = 'save.php';
    
    Swal.fire({
        title: 'Delete Employee Data',
        text: "Other data may be affected. Are you sure to delete this employee?",
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
                    data: {"action" : action, "employee_id" : employee_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Employee Data Deleted.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'employees.php';
                                    }
                            });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to Delete Employee Data.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'employees.php';
                                    }
                            });
                        }
                    }
                });
            }
            else{ result.dismiss === Swal.DismissReason.cancel; }
        });
}

function SaveUserEmployee(){
    var user_id = $('#cmbUser').val();
    var employee_id = $('#cmbEmployeeData').val();

    var url = 'save.php';
    var action = 'SaveUserEmployee';

    var data = "action="+action;
        data += "&user_id="+user_id;
        data += "&employee_id="+employee_id;
        
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
                    text: 'Employee Data Linked to User.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
        }
        else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to link Employee Data to User. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
            }
        } 
    }
}

function UnlinkUser(employee_id, user_id){
    var url = 'save.php';
    var action = 'UnlinkUserEmployee';

        Swal.fire({
            title: 'Unlink User from Employee',
            text: "Other data may be affected. Are you sure to unlink this user?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes Unlink'
          }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {"action" : action, "employee_id" : employee_id, "user_id" : user_id},
                        success: function(data) {
                            var obj = $.parseJSON(data);
                            var result = obj.result;
                            if(result != "xxx"){
                                Swal.fire({
                                    title: 'Success',
                                    text: 'User Unlinked successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'Ok'
                                    }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.reload();
                                        }
                                });
                            }
                            else{
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Failed to Unlink User.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: 'Ok'
                                    }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.reload();
                                        }
                                });
                            }
                        }
                    });
                }
                else{ result.dismiss === Swal.DismissReason.cancel; }
            });   
}

function SaveEmployment(){
    var action = '';
    var employee_id = $('#txtEmployeeID_1').val();
    var employment_id= $('#txtEmploymentID').val();
    var employment_refnum = $('#txtEmploymentRefNum').val();
    var employment_type = $('#cmbEmpType').val();
    var employment_start = $('#txtEmploymentDateStart').val();
    var employment_end = $('#txtEmploymentDateEnd').val();   
    var position = $('#cmbPosition').val();    
    var department_assigned = $('#cmbDepartmentAssigned').val();
    var designation = $('#txtDesignation').val();
    var work_nature = $('#cmbWOrkNature').val();
    var work_specifics = $('#cmbWorkSpecifics').val();
    var employment_rate = $('#txtRate').val();
    var employment_particulars = $('#txtEmploymentParticulars').val();

    var url = 'save.php';

    if (employment_id === '') {
        action = 'SaveEmployment';
    }
    else{
        action = 'EditEmployment';
    }

    var data = "action="+action;
        data += "&employee_id="+employee_id;
        data += "&employment_id="+employment_id;
        data += "&employment_refnum="+employment_refnum;
        data += "&employment_type="+employment_type;
        data += "&employment_start="+employment_start;
        data += "&employment_end="+employment_end;
        data += "&position="+position;
        data += "&department_assigned="+department_assigned;
        data += "&designation="+designation;
        data += "&work_nature="+work_nature;
        data += "&work_specifics="+work_specifics;
        data += "&employment_rate="+employment_rate;
        data += "&employment_particulars="+employment_particulars;

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
                    text: 'Employment Data Saved.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
            }
            else if(result == "has_active_emp"){
                Swal.fire({
                    title: 'Error',
                    text: 'Employee has current active employment. Please end it first to be inactive.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
            }
            else if(result == "position_filled"){
                Swal.fire({
                    title: 'Error',
                    text: 'Position is not vacant. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href;
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to save Employment Data. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                });
            }
        } 
    }
}

function GetEmploymentDetails(employee_id,employment_id){
    var action = 'get_employment_details';
    
    GetEmployeeDetailsForEmployment(employee_id);
    // Clear all options except the first (placeholder)

    $.ajax({
        type: "GET",
        url: "get.php",
        data: {"action" : action, "employment_id" : employment_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtEmploymentID').val(obj['employment_id']);
                $('#cmbEmpType').val(obj['employment_type']).prop('disabled', true);        //.trigger('change')
                $('#cmbPosition').val(obj['position_id']).trigger('change').prop('disabled', true);
                $('#cmbDepartment').val(obj['dept_id']).prop('disabled', true);
                $('#txtEmploymentRefNum').val(obj['employment_refnum']);
                $('#txtEmploymentDateStart').val(obj['employment_start']);
                $('#txtEmploymentDateEnd').val(obj['employment_end']);
                $('#cmbDepartmentAssigned').val(obj['dept_assigned']);
                $('#txtDesignation').val(obj['designation']);
                $('#cmbWOrkNature').val(obj['work_nature']);
                $('#cmbWorkSpecifics').val(obj['work_specifics']);
                $('#txtEmploymentParticulars').val(obj['employment_particulars']);
                $('#txtRate').val(obj['rate']).prop('disabled', true);
            // Show employment modal after fields populated
            if (typeof $('#employment_modal').modal === 'function') {
                $('#employment_modal').modal('show');
            }
        }
    });
}

function DeleteEmployment(employment_id,position_id){
    var action = 'delete_employment';
    var url = 'save.php';
    
    Swal.fire({
        title: 'Delete Employment Data',
        text: "Other data may be affected. Are you sure to delete this employment?",
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
                    data: {"action" : action, "employment_id" : employment_id, "position_id" : position_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Employment Data Deleted.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                    }
                            });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to Delete Employment Data.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                    }
                            });
                        }
                    }
                });
            }
            else{ result.dismiss === Swal.DismissReason.cancel; }
        });
}

