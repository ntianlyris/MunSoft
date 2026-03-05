//-----Ajax-----//
var xhr = new XMLHttpRequest();

//-----Validations-----//

$( document ).ready(function(){
    $('#btnSaveEmployeeDeductions').click(function() {
        var form = document.getElementById('formEmployeeDeduction');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveEmployeeDeductions();
        }
    });
});

//-----Functions-----//

function SaveEmployeeDeductions(){
    // make sure employee_id is submitted when dropdown is disabled during edit
    var empId = $('#cmbEmployee').val();
    if (empId && $('#cmbEmployee').prop('disabled')) {
        $('#txtEmployeeID').val(empId);
    } else {
        $('#txtEmployeeID').val('');
    }

    var action = '';
    var employee_deduction_id = $('#txtEmployeeDeductionID').val();
    if (employee_deduction_id !== '') {
        action = 'edit_employee_deductions';
    }
    else{
        action = 'save_employee_deductions';
    }

    var confirmMessage = action === 'edit_employee_deductions'
      ? 'Are you sure you want to update employee deductions? This will affect payroll calculations.'
      : 'Are you sure you want to save these employee deductions? This will affect payroll calculations.';

    // Show confirmation dialog before saving
    Swal.fire({
      title: 'Confirm Save',
      text: confirmMessage,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#dc3545',
      confirmButtonText: 'Yes, Save',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        var formData = $('#formEmployeeDeduction').serialize(); // This automatically collects all form inputs with names
            formData +='&action='+action;

         $.ajax({
          url: 'deductions_handler.php', 
          type: 'POST',
          data: formData,
          success: function(response) {
            var obj = $.parseJSON(response);
            var result = obj.result;
                if(result == "success"){
                    Swal.fire({
                        title: 'Success',
                        text: 'Employee Deductions successfully saved.',
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                    });
                }
                else if(result == 'not_employed'){
                    Swal.fire({
                        title: 'Error',
                        text: 'Employee has no employment. Asign employment to employee first.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                    });
                }
                else if(result == 'block_edit'){
                    var blockMessage = obj.message || 'Cannot save/edit deductions. Payroll is locked and cannot be modified.';
                    var blockReason = obj.reason || 'unknown';
                    var errorTitle = 'Error - Cannot Edit';
                    
                    // Different title/handling based on blocking reason
                    if(blockReason === 'period'){
                        errorTitle = 'Error - 1st/2nd Half Mismatch';
                    } else if(blockReason === 'status'){
                        errorTitle = 'Error - Payroll Locked';
                    }
                    
                    Swal.fire({
                        title: errorTitle,
                        text: blockMessage,
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
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
                        text: 'Failed to save Employee Deductions.',
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
            alert('Submission failed!');
          }
        });
      }
    });
}

function GetEmployeeDeductionComponents(employee_deduction_id){
    var action = 'get_emp_deduction';
    var action1 = 'get_emp_deduction_comps';

    $.ajax({
        type: "GET",
        url: "deductions_handler.php",
        data: {"action" : action, "employee_deduction_id" : employee_deduction_id},
        success: function(data) {
                var obj = $.parseJSON(data);
                $('#txtEmployeeDeductionID').val(obj['employee_deduction_id']);
                $('#cmbEmployee').val(obj['employee_id']).trigger('change');
                // disable employee selector and store hidden copy
                $('#cmbEmployee').prop('disabled', true);
                $('#txtEmployeeID').val(obj['employee_id']);
                $('#txtDeductionParticulars').val(obj['deduction_particulars']);
                $('#txtEffectiveDate').val(obj['effective_date']);
        }
    });

    $.ajax({
        type: "GET",
        url: "deductions_handler.php",
        data: {"action" : action1, "employee_deduction_id" : employee_deduction_id},
        success: function(data) {
            let total_deductions = 0;
            $("#deductionCodesInputs").empty();  //---needed to ensure upon reloading the values of the account codes would not be duplicated.
            var obj = $.parseJSON(data);
            if (obj) {
                $.each(obj, function(key, value) {
                    var input = '<div class="row" style="padding-bottom:15px;" id="dynamic_field">';
                        input += '<div class="col-3"><label>'+ value['deduct_code'] +'</label></div>';
                        input += '<div class="col-8"><div class="input-group">';
                        input += '<input type="hidden" name="config_deduction_ids[]" value="'+ value['config_deduction_id'] +'">';
                        input += '<input type="text" style="text-align:right;" class="form-control form-control-sm emp-deductions-amt" value="'+ value['deduction_comp_amt'] +'" name="emp-deductions-amt[]" onkeyup="CalculateSum(); ValidateInputAmount();" placeholder="0.00" required>';
                        input += '<span class="input-group-btn"><a href="javascript:void(0);" class="btn btn-danger btn-sm btn-flat remove_button"><i class="fa fa-times"></i></a></span>';
                        input += '</div></div></div>';

                        $("#deductionCodesInputs").append(input);
                        total_deductions += parseFloat(value['deduction_comp_amt']) || 0; // fallback to 0 if NaN
                });
                $('#txtEmployeeDeductionTotal').val(total_deductions.toFixed(2));
            }
        }
    });
}

function DeleteEmployeeDeduction(emp_deduction_id){
    var action = 'delete_emp_deduction';
    var url = 'deductions_handler.php';
    
    Swal.fire({
        title: 'Delete Employee Deduction',
        text: "Other data may be affected. Are you sure to delete this employee deduction data?",
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
                    data: {"action" : action, "emp_deduction_id" : emp_deduction_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Employee Deduction Deleted.',
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
                                text: 'Failed to Delete Employee Deduction Data.',
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

//-- for details modal on all employee deductions -->
$(document).on('click', '.btn-view-deductions', function() {
    var employeeId = $(this).data('employee-id');
    var action = 'get_employee_deductions';

    $.ajax({
      url: 'get.php',
      method: 'POST',
      data: { "employee_id": employeeId, "action": action },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          var tbody = $('#deductions-history-body');
          tbody.empty();

          if (response.data.length > 0) {
            response.data.forEach(function(row) {
              tbody.append(`
                <tr>
                    <td>${row.deduction_particulars}</td>
                    <td>${row.effective_date}</td>
                    <td>${row.end_date ?? 'active'}</td>
                    <td align="right">${parseFloat(row.total_deduction).toLocaleString()}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button 
                                class="btn btn-secondary btn-sm view-deduction" 
                                data-emp_deduction_id="${row.employee_deduction_id}" 
                                data-toggle="modal" 
                                data-target="#employee_deductions_modal"
                                data-backdrop="false"
                            >
                                <span data-toggle="tooltip" title="View Details" data-placement="bottom"><i class="fa fa-edit"></i> Details</span>
                            </button>
                            <button class="btn btn-danger btn-sm delete-deduction" 
                                    data-emp_deduction_id="${row.employee_deduction_id}" 
                                    data-toggle="tooltip" 
                                    title="Delete Employee Deduction" 
                                    data-placement="bottom">
                                <i class="fa fa-times"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
              `);
            });
            $('#modalEmployeeName').text(response.full_name);
          } else {
            tbody.html('<tr><td colspan="4" class="text-center">No data found</td></tr>');
          }

          $('#deductionsModal').modal('show');
        }
      }
    });
});

$(document).on('click', '.view-deduction', function() {
  var emp_deduction_id = $(this).data('emp_deduction_id');
  GetEmployeeDeductionComponents(emp_deduction_id);
});

$(document).on('click', '.delete-deduction', function() {
  var emp_deduction_id = $(this).data('emp_deduction_id');
  DeleteEmployeeDeduction(emp_deduction_id);
});

$('#employee_deductions_modal').on('hidden.bs.modal', function () {
  if ($('.modal:visible').length) {
    $('body').addClass('modal-open');
  }
});
