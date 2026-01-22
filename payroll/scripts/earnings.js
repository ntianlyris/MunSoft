
//-----Clickable table row-----//
jQuery(document).ready(function($) {
    $(".clickable-row").click(function(e) {
        e.preventDefault();
        $('#employee_earnings_modal').modal('show');
    });
});

//-----Ajax-----//
var xhr = new XMLHttpRequest();

//-----Validations-----//

$( document ).ready(function(){
    $('#btnSaveEmployeeEarnings').click(function() {
        var form = document.getElementById('formEmployeeEarning');
        if (!form.checkValidity()) {
            // Create the temporary button, click and remove it
            var tmpSubmit = document.createElement('button')
            form.appendChild(tmpSubmit)
            tmpSubmit.click()
            form.removeChild(tmpSubmit)
        } else {
            SaveEmployeeEarnings();
        }
    });
});

//-----Helper Functions-----//
function CalculateSum(){
    var arr = document.getElementsByName('emp-earnings-amt[]');
    var sum = 0;
    //alert(arr.length);
    for(var i=0; i < arr.length; i++){
      if (parseFloat(arr[i].value)) {
        sum += parseFloat(arr[i].value);
      }
    }
   // alert(sum);
    document.getElementById('txtEmployeeEarningTotal').value = sum.toFixed(2);
}

function ValidateInputAmount(){
    var txt_amt = document.getElementsByName('emp-earnings-amt[]');
    var amt;
    var numbers_decimal = /^-?\d*(\.\d{0,2})?$/;    //--numbers w/ 2 decimals

    for(var i=0; i < txt_amt.length; i++){
      amt = txt_amt[i].value;
      if (!amt.match(numbers_decimal)) {
        //alertify.notify('Amount must be numeric and have a maximum of 2 decimal places.','error', 3, function(){window.location.reload();});
        //alertify.alert('ERROR!','Amount must be numeric with a maximum of 2 decimal places.',function(){window.location.reload();});
        Swal.fire({
          title: 'Error',
          text: 'Amount must be numeric with a maximum of 2 decimal places.',
          icon: 'error',
          confirmButtonColor: '#dc3545',
          confirmButtonText: 'Ok'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href;
              }
        });
      }
    }
}

//-----Functions-----//

function SaveEmployeeEarnings(){
    var action = '';
    var employee_earning_id = $('#txtEmployeeEarningID').val();
    if (employee_earning_id !== '') {
        action = 'edit_employee_earnings';
    }
    else{
        action = 'save_employee_earnings';
    }

    var formData = $('#formEmployeeEarning').serialize(); // This automatically collects all form inputs with names
        formData +='&action='+action;

     $.ajax({
      url: 'earnings_handler.php', 
      type: 'POST',
      data: formData,
      success: function(response) {
        var obj = $.parseJSON(response);
        var result = obj.result;
            if(result == "success"){
                Swal.fire({
                    title: 'Success',
                    text: 'Employee Earnings successfully saved.',
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
            else{
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to save Employee Earnings.',
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

function GetEmployeeEarningComponents(emp_earning_id){
    var action = 'get_emp_earning';
    var action1 = 'get_emp_earning_comps';

    $.ajax({
        type: "GET",
        url: "earnings_handler.php",
        data: {"action" : action, "emp_earning_id" : emp_earning_id},
        success: function(data) {
                var obj = $.parseJSON(data);

                $('#txtEmployeeEarningID').val(obj['employee_earning_id']);
                $('#txtEmployeeEmploymentID').val(obj['employment_id']);
                $('#txtEmpPosition').val(obj['position_title']);
                $('#txtEmpType').val(obj['employment_type']);
                $('#cmbEmployee').select2('destroy'); // Destroys Select2 UI
                $('#cmbEmployee').val(obj['employee_id']).prop('disabled', true);  
                $('#txtEarningParticulars').val(obj['earning_particulars']);
                $('#txtEffectiveDate').val(obj['effective_date']);
                $('#txtBasicRate').val(obj['locked_rate']);
        }
    });

    $.ajax({
        type: "GET",
        url: "earnings_handler.php",
        data: {"action" : action1, "emp_earning_id" : emp_earning_id},
        success: function(data) {
            let gross = 0;
            $("#earningCodesInputs").empty();  //---needed to ensure upon reloading the values of the account codes would not be duplicated.
            var obj = $.parseJSON(data);
            if (obj) {
                var empType = $('#txtEmpType').val(); // Get current employment type

                $.each(obj, function(key, value) {
                    var isLocked = false;

                    if (
                        (empType === 'Regular' && value['earning_code'].toLowerCase().includes('regular')) ||
                        (empType === 'Casual' && value['earning_code'].toLowerCase().includes('casual'))
                    ) {
                        isLocked = true;
                    }

                    var input = '<div class="row" style="padding-bottom:15px;" id="dynamic_field">';
                    input += '<div class="col-3"><label>'+ value['earning_code'] +'</label></div>';
                    input += '<div class="col-9"><div class="input-group">';
                    input += '<input type="hidden" name="earning_code_ids[]" value="'+ value['config_earning_id'] +'">';

                    input += '<input type="text" style="text-align:right;" class="form-control form-control-sm emp-earnings-amt" ' +
                            'value="'+ value['earning_comp_amt'] +'" name="emp-earnings-amt[]" ' +
                            (isLocked ? 'readonly' : 'onkeyup="CalculateSum(); ValidateInputAmount();"') +
                            ' placeholder="0.00" required>';

                    if (isLocked) {
                        // Locked: no remove, show lock icon
                        input += '<span class="input-group-btn">' +
                                '<button class="btn btn-secondary btn-sm btn-flat" disabled>' +
                                '<i class="fa fa-lock"></i></button></span>';
                    } else {
                        // Editable: show remove button
                        input += '<span class="input-group-btn">' +
                                '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-flat remove_button">' +
                                '<i class="fa fa-times"></i></a></span>';
                    }

                    input += '</div></div></div>';

                    $("#earningCodesInputs").append(input);
                    gross += parseFloat(value['earning_comp_amt']) || 0;
                });
                $('#txtEmployeeEarningTotal').val(gross.toFixed(2));
            }
        }
    });
}

function DeleteEmployeeEarning(emp_earning_id){
    var action = 'delete_emp_earning';
    var url = 'earnings_handler.php';
    
    Swal.fire({
        title: 'Delete Employee Earning',
        text: "Other data may be affected. Are you sure to delete this employee earning data?",
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
                    data: {"action" : action, "emp_earning_id" : emp_earning_id},
                    success: function(data) {
                        var obj = $.parseJSON(data);
                        var result = obj.result;
                        if(result != "xxx"){
                            Swal.fire({
                                title: 'Success',
                                text: 'Employee Earning Deleted.',
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
                                text: 'Failed to Delete Employee Earning Data.',
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

//-- for details modal on all employee earnings -->
$(document).on('click', '.btn-view-earnings', function() {
    var employeeId = $(this).data('employee-id');
    var action = 'get_employee_earnings';

    $.ajax({
      url: 'get.php',
      method: 'POST',
      data: { "employee_id": employeeId, "action": action },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          var tbody = $('#earnings-history-body');
          tbody.empty();

          if (response.data.length > 0) {
            response.data.forEach(function(row) {
              tbody.append(`
                <tr>
                    <td>${row.position_title}</td>
                    <td>${row.earning_particulars}</td>
                    <td>${row.effective_date}</td>
                    <td>${row.end_date ?? 'active'}</td>
                    <td align="right">${parseFloat(row.locked_rate).toLocaleString()}</td>
                    <td align="right">${parseFloat(row.gross_amount).toLocaleString()}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button 
                                class="btn btn-secondary btn-sm view-earning" 
                                data-emp_earning_id="${row.employee_earning_id}" 
                                data-toggle="modal" 
                                data-target="#employee_earnings_modal"
                                data-backdrop="false"
                            >
                                <span data-toggle="tooltip" title="View Details" data-placement="bottom"><i class="fa fa-edit"></i> Details</span>
                            </button>
                            <button class="btn btn-danger btn-sm delete-earning" 
                                    data-emp_earning_id="${row.employee_earning_id}" 
                                    data-toggle="tooltip" 
                                    title="Delete Employee Earning" 
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

          $('#earningsModal').modal('show');
        }
      }
    });
});

$(document).on('click', '.view-earning', function() {
  var emp_earning_id = $(this).data('emp_earning_id');
  GetEmployeeEarningComponents(emp_earning_id);
});

$(document).on('click', '.delete-earning', function() {
  var emp_earning_id = $(this).data('emp_earning_id');
  DeleteEmployeeEarning(emp_earning_id);
});

$('#employee_earnings_modal').on('hidden.bs.modal', function () {
  if ($('.modal:visible').length) {
    $('body').addClass('modal-open');
  }
});

$( document ).ready(function(){
    var addButton = $('#btnAddEarningCode');
    var wrapper = $("#earningCodesInputs");

    $(addButton).on('click',function(){
      var earning_code_id = $('#cmbEarningCodes').find('option:selected').val();
      var earning_code = $('#cmbEarningCodes').find('option:selected').text();

      var input = '<div class="row" style="padding-bottom:15px;" id="dynamic_field">';
          input += '<div class="col-3"><label>'+ earning_code +'</label></div>';
          input += '<div class="col-9"><div class="input-group">';
          input += '<input type="hidden" name="earning_code_ids[]" value="'+ earning_code_id +'">';
          input += '<input type="text" style="text-align:right;" class="form-control form-control-sm emp-earnings-amt" name="emp-earnings-amt[]" onkeyup="CalculateSum(); ValidateInputAmount();" placeholder="0.00" required>';
          input += '<span class="input-group-btn"><a href="javascript:void(0);" class="btn btn-danger btn-sm btn-flat remove_button"><i class="fa fa-times"></i></a></span>';
          input += '</div></div></div>';

      if (earning_code_id != "") {
        $(wrapper).append(input);
      }
    });  

    $(wrapper).on('click', '.remove_button', function(e){
      e.preventDefault();
      $(this).closest("#dynamic_field").remove();
      CalculateSum();
    })



    //---handles getting and populating values of employment details for setting up earning linking it to the employments for service record reporting---//

    $('#cmbEmployee').on('change', function () {
        const employeeId = $(this).val();

        if (employeeId) {
            $.ajax({
                url: 'earnings_handler.php',
                method: 'POST',
                data: {
                    action: 'get_employee_current_employment',
                    employee_id: employeeId
                },
                dataType: 'json',
                success: function (response) {
                    // Clear previous fields before adding
                    $("#earningCodesInputs").empty();
                    if (response.status === 'ok') {
                        let basic_rate = parseFloat(response.data.rate).toFixed(2);
                        let emp_type = response.data.employment_type.toLowerCase();

                        $('#txtEmployeeEmploymentID').val(response.data.employment_id);
                        $('#txtEmpPosition').val(response.data.position_title);
                        $('#txtEmpType').val(response.data.employment_type);
                        $('#txtEarningParticulars').val(response.data.employment_particulars);
                        $('#txtEffectiveDate').val(response.data.employment_start);
                        $('#txtBasicRate').val(basic_rate);

                        // Determine appropriate earning label  --> determine earning code based on employment type
                        let earning_name = (emp_type === 'regular') ? 'Sal-Reg' : 'Sal-Cas';

                        // Auto-select from dropdown (if exists)
                        let found = false;
                        $('#cmbEarningCodes option').each(function() {
                            if ($(this).text().trim() === earning_name) {
                                $(this).prop('selected', true);
                                found = true;
                                return false;
                            }
                        });

                        if (found) {
                            // Trigger "Add Earning" logic
                            $('#btnAddEarningCode').click();

                            // Wait for element to render then inject basic rate
                            setTimeout(() => {
                                // Grab the last added earning input (assumes only one added at this point)
                                let lastInput = $('#earningCodesInputs .emp-earnings-amt').last();
                                lastInput.val(basic_rate).trigger('onkeyup');
                                lastInput.prop('readonly', true);
        
                                let removeBtn = lastInput.closest('#dynamic_field').find('.remove_button');
                                removeBtn
                                .removeClass('btn-danger remove_button')
                                .addClass('btn btn-secondary btn-sm btn-flat')
                                .prop('disabled', true)
                                .html('<i class="fa fa-lock"></i>');
                            }, 100);
                        }
                    } else {
                        Swal.fire("Not Found", response.message, "warning");

                        // Clear fields
                        $('#txtEmpPosition, #txtEmpType, #txtEarningParticulars, #txtEffectiveDate, #txtBasicRate, #txtEmployeeEarningTotal').val('');
                        $("#earningCodesInputs").empty();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    Swal.fire("Error", "Failed to load employee details.", "error");
                }
            });
        } else {
            $('#txtEmpPosition, #txtEmpType, #txtEarningParticulars, #txtEffectiveDate, #txtBasicRate, #txtEmployeeEarningTotal').val('');
        }
    });
});
