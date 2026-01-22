//-----Ajax-----//
var xhr = new XMLHttpRequest();

//-----Validations-----//

$( document ).ready(function(){
    $('#btnSavePositionItem').click(function() {
        var form = document.getElementById('formPositionItem');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SavePositionItem();
        }
    });
});

//-----Functions-----//

function SavePositionItem(){
    var positionRefNum = $('#txtPosRefNum').val();
    var positionItemNum = $('#txtPosItemNum').val();
    var positionItemTitle = $('#txtItemtTitle').val();
    var salaryGrade = $('#txtSalaryGrade').val();
    var positionType = $('#cmbPosType').val();
    var positionRate = $('#txtPosRate').val();
    var dept_id = $('#cmbDepartment').val();
    var positionStatus = $('#cmbPositionStatus').val();

    var url = 'save_settings.php';
    var submit = 'SavePositionItem';
    var data = "submit="+submit;
        data += "&dept_id="+dept_id;
        data += "&positionRefNum="+positionRefNum;
        data += "&positionItemNum="+positionItemNum;
        data += "&positionItemTitle="+positionItemTitle;
        data += "&salaryGrade="+salaryGrade;
        data += "&positionType="+positionType;
        data += "&positionRate="+positionRate;
        data += "&positionStatus="+positionStatus;

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
                    text: 'Position Item Saved.',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'settings_positions.php';
                        }
                });
            }
            else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to save Position Item. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'settings_positions.php';
                        }
                });
            }
        } 
    }
}

function GetPositionDetails(position_id){
    var action = 'get_position_details';

    $.ajax({
        type: "GET",
        url: "get.php",
        data: {"action" : action, "position_id" : position_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtPositionID').val(obj['position_id']);
                $('#txtPosRefNum').val(obj['position_refnum']);
                $('#txtPosItemNum').val(obj['position_itemnum']);
                $('#txtItemtTitle').val(obj['position_title']);
                $('#txtSalaryGrade').val(obj['salary_grade']);
                $('#cmbPosType').val(obj['position_type']);
                $('#txtPosRate').val(obj['position_rate']);
                $('#cmbDepartment').val(obj['dept_id']);
                $('#cmbPositionStatus').val(obj['position_status']);
        }
    });
}

function DeletePosition(position_id){
    var action = 'delete_position';
    var url = 'save_settings.php';
    
    Swal.fire({
        title: 'Delete Position Data',
        text: "Other data may be affected. Are you sure to delete this position?",
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
                    data: {"submit" : action, "posid" : position_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Position Deleted.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'settings_positions.php';
                                    }
                            });
                        }
                        else{
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to Delete Position Data.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'settings_positions.php';
                                    }
                            });
                        }
                    }
                });
            }
            else{ result.dismiss === Swal.DismissReason.cancel; }
        });
}
