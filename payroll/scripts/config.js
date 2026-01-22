//-----Ajax-----//
var xhr = new XMLHttpRequest();

//-----Validations-----//

$( document ).ready(function(){
    $('#btnSaveEarningConfig').click(function() {
        var form = document.getElementById('formEarningConfig');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveEarningConfig();
        }
    });
});

$( document ).ready(function(){
    $('#btnSaveDeductionConfig').click(function() {
        var form = document.getElementById('formDeductionConfig');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveDeductionConfig();
        }
    });
});

//-----Functions-----//

function SaveEarningConfig(){
    var config_earning_id = $('#txtConfigEarningID').val();
    var earning_acct_code = $('#txtEarningAcctCode').val();
    var earning_code = $('#txtEarningCode').val();
    var earning_title = $('#txtEarningTitle').val();
    
    var url = 'save_settings.php';
    var submit = 'SaveEarningConfig';
    var data = "submit="+submit;
        data += "&config_earning_id="+config_earning_id;
        data += "&earning_acct_code="+earning_acct_code;
        data += "&earning_code="+earning_code;
        data += "&earning_title="+earning_title;

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
                    text: 'Earning Configuration Saved.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'config_earnings.php';
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to save Earning Configuration. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'config_earnings.php';
                        }
                });
            }
        } 
    }
}

function GetEarningConfigDetails(config_earning_id){
    var action = 'get_earning_config_details';

    $.ajax({
        type: "GET",
        url: "get.php",
        data: {"action" : action, "config_earning_id" : config_earning_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtConfigEarningID').val(obj['config_earning_id']);
                $('#txtEarningAcctCode').val(obj['earning_acct_code']);
                $('#txtEarningCode').val(obj['earning_code']);
                $('#txtEarningTitle').val(obj['earning_title']);
        }
    });
}

function DeleteEarningConfig(config_earning_id){
    var action = 'delete_earning_config';
    var url = 'save_settings.php';
    
    Swal.fire({
        title: 'Delete Earning Config Data',
        text: "Other data may be affected. Are you sure to delete this Earning Configuration?",
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
                    data: {"submit" : action, "config_earning_id" : config_earning_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Earning Configuration Data Deleted.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'config_earnings.php';
                                    }
                            });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to Delete Earning Configuration Data.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'config_earnings.php';
                                    }
                            });
                        }
                    }
                });
            }
            else{ result.dismiss === Swal.DismissReason.cancel; }
        });
}

function SaveDeductionConfig(){
    var submit = '';
    var config_deduction_id = $('#txtConfigDeductionID').val();
    var deduction_type = $('#cmbDeductionType').val();
    var deduction_acct_code = $('#txtDeductionAcctCode').val();
    var deduction_code = $('#txtDeductionCode').val();
    var deduction_title = $('#txtDeductionTitle').val();
    var is_employee_share = $('#isEmployeeShare').val();
    var deduction_category = $('#cmbDeductCategory').val();

    if(config_deduction_id !== ''){
        submit = 'EditDeductionConfig';
    }
    else{
        var submit = 'SaveDeductionConfig';
    }

    var url = 'save_settings.php';
    var data = "submit="+submit;
        data += "&config_deduction_id="+config_deduction_id;
        data += "&deduction_type="+deduction_type;
        data += "&deduction_acct_code="+deduction_acct_code;
        data += "&deduction_code="+deduction_code;
        data += "&deduction_title="+deduction_title;
        data += "&is_employee_share="+is_employee_share;
        data += "&deduction_category="+deduction_category;
    //alert(submit);
    xhr.open("POST", url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send(data);
    xhr.onreadystatechange=function(){ 
        if (xhr.readyState == 4){ 
            var obj = $.parseJSON(xhr.responseText);
            var result = obj.result;
            if(result === "success"){
                Swal.fire({
                    title: 'Success',
                    text: 'Deduction Configuration Saved.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'config_deductions.php';
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to save Deduction Configuration. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'config_deductions.php';
                        }
                });
            }
        } 
    }
}

function GetDeductionConfigDetails(config_deduction_id){
    var action = 'get_deduction_config_details';
    
    $.ajax({
        type: "GET",
        url: "get.php",
        data: {"action" : action, "config_deduction_id" : config_deduction_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtConfigDeductionID').val(obj['config_deduction_id']);
                $('#cmbDeductionType').val(obj['deduction_type_id']);
                $('#txtDeductionAcctCode').val(obj['deduct_acct_code']);
                $('#txtDeductionCode').val(obj['deduct_code']);
                $('#txtDeductionTitle').val(obj['deduct_title']);
                $('#isEmployeeShare').val(obj['is_employee_share']);
                $('#cmbDeductCategory').val(obj['deduct_category']);
        }
    });
}

function DeleteDeductionConfig(config_deduction_id){
    var action = 'delete_deduction_config';
    var url = 'save_settings.php';
    
    Swal.fire({
        title: 'Delete Deduction Configuration Data',
        text: "Other data may be affected. Are you sure to delete this Deduction Configuration?",
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
                    data: {"submit" : action, "config_deduction_id" : config_deduction_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Deduction Configuration Data Deleted.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'config_deductions.php';
                                    }
                            });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to Delete Deduction Configuration Data.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'config_deductions.php';
                                    }
                            });
                        }
                    }
                });
            }
            else{ result.dismiss === Swal.DismissReason.cancel; }
        });
}


