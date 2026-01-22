$ (function(){
    // Form submit
    $("#leaveTypeForm").on("submit", function(e){
        e.preventDefault();
        var action = 'save_leave_type';
        $.ajax({
            url: "leave_handler.php",
            type: "POST",
            data: $(this).serialize() + '&action=' + action,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message,
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                               $("#leaveTypeModal").modal('hide');
                                window.location.reload();
                            }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred.'
                });
            }
        });
    });
});

function GetLeaveTypeDetails(leave_type_id){
    var action = 'get_leave_type_details';

    $.ajax({
        type: "POST",
        url: "leave_handler.php",
        data: {"action" : action, "leave_type_id" : leave_type_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#leaveTypeId').val(obj['leave_type_id']);
                $('#leave_code').val(obj['leave_code']);
                $('#leave_name').val(obj['leave_name']);
                $('#yearly_allotment').val(obj['yearly_allotment']);
                $('#monthly_accrual').val(obj['monthly_accrual']);
                $('#is_accumulative').val(obj['is_accumulative']);
                $('#max_accumulation').val(obj['max_accumulation']);
                $('#gender_restriction').val(obj['gender_restriction']);
                $('#reset_policy').val(obj['reset_policy']);
                $('#requires_attachment').val(obj['requires_attachment']);
                $('#active').val(obj['active']); 
                $('#frequency_limit').val(obj['frequency_limit']);
                $('#description').val(obj['description']);
        }
    });
}

function DeleteLeaveType(leave_type_id){
    var action = 'delete_leave_type';
    var url = 'leave_handler.php';
    
    Swal.fire({
        title: 'Delete Leave Type Data',
        text: "Other data may be affected. Are you sure to delete this Leave Type?",
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
                    data: {"action" : action, "leave_type_id" : leave_type_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Leave Type Data Deleted.',
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
                                text: 'Failed to Delete Leave Type Data.',
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

// Initialize leave balances for employee
$("#initBalances").click(function() {
    let empId = $("#employeeSelect").val();
    let year = $("#yearSelect").val();
    var action = "initialize_balances";
    if (!empId) {
        Swal.fire({
            icon: 'warning',
            title: 'No Employee Selected',
            text: 'Please select an employee first.',
            confirmButtonColor: '#28a745'
        });
        return;
    }

    Swal.fire({
        title: 'Initialize Leave Balances',
        text: 'This will set up leave balances for the selected employee. Continue?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Continue'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "leave_handler.php",
                type: "POST",
                data: { employee_id: empId, year: year, action: action },
                success: function(res) {
                    try {
                        let data = JSON.parse(res);
                        if (data.status == "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Leave balances initialized successfully!',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                $("#loadBalances").click(); // reload table
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Unexpected Response',
                            text: res,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            });
        }
    });
});

$( document ).ready(function(){
    $(document).on('click', '.btnApproveLeave', function() {
        var leaveAppId = $(this).data('leaveapp-id');
        var action = 'approve_leave_application';
        Swal.fire({
            title: 'Approve Leave',
            text: "Are you sure you want to approve this leave application?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, Approve' 
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "leave_handler.php",
                    data: {"action" : action, "leave_application_id" : leaveAppId},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        if(obj.status === "success"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Leave application approved.',
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
                                text: obj.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                    }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred.'
                        });
                    }
                });
            }
        });    
    });

    $(document).on('click', '.btnDisapproveLeave', function() {
        var leaveAppId = $(this).data('leaveapp-id');
        var action = 'disapprove_leave_application';
        Swal.fire({
            title: 'Disapprove Leave',
            text: "Are you sure you want to disapprove this leave application?",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, Disapprove' 
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "leave_handler.php",
                    data: {"action" : action, "leave_application_id" : leaveAppId},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        if(obj.status === "success"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Leave application disapproved.',
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
                                text: obj.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'Ok'
                                }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                    }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred.'
                        });
                    }
                });
            }
        });
    });
});