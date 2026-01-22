
//---Toggle Include to Payroll---//
$(document).on('change', '.toggleIncludePayroll', function() {
    const empId = $(this).data('id');
    const include = $(this).is(':checked') ? 1 : 0;
    var action = 'toggle_include_payroll';

    $.ajax({
      url: 'payroll_handler.php',
      method: 'POST',
      data: { employee_id: empId, include_in_payroll: include, action: action },
      success: function(response) {
          if (response.status === 'success') {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: "Employee payroll inclusion updated successfully.",
                confirmButtonColor: '#28a745',
            });
              } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error updating inclusion. Please try again.",
                confirmButtonColor: '#dc3545',
            });
          }
      }
        });
});

//---Computer Payroll and Show it Dynamically---//
function computePayroll() {
    const period = $('#pay_period').val();
    const dept = $('#department').val();
    const empType = $('#employment_type').val();
    var action = 'compute_payroll';

    if (!period || !dept) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Please select both payroll period and department.",
        confirmButtonColor: '#dc3545',
      });
      return;
    }

    // Show loader
    $('#Loader').fadeIn();

    $.ajax({
      url: 'payroll_handler.php',
      type: 'POST',
      dataType: 'json',
      data: {
        period: period,
        department: dept,
        employment_type: empType,
        action: action
      },
      success: function(response) {
        $('#savePayrollBtn')
          .html('<i class="fas fa-save"></i> Save Payroll')  // use .html() to insert HTML content
          .addClass('btn-success')
          .removeClass('btn-secondary')
          .prop('disabled', false);

        let rows = '';
        if (response.length > 0) {
          response.forEach((emp, index) => {
              rows += `
                  <tr>
                  <td>${index + 1}</td>
                  <td>${emp.id_num}</td>
                  <td>${emp.name}</td>
                  <td>${emp.position}</td>
                  <td class="text-right">${Number(emp.gross).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                  <td class="text-right">${Number(emp.deductions).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                  <td class="text-right">${Number(emp.net).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                  <td class="text-center">
                      <button class="btn btn-sm btn-info" onclick='viewPayrollBreakdown(this)' data-employee='${JSON.stringify(emp)}'>
                      <i class="fas fa-search"></i> View Details
                      </button>
                  </td>
                  </tr>
              `;
          });
        }
        else{
          rows = `
              <tr>
                  <td colspan="8" class="text-center text-muted">No payroll data available.</td>
              </tr>
          `;
        }
        $('#payrollTable tbody').html(rows);
      },
      error: function(xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Failed to Compute Payroll. Please try again.",
          confirmButtonColor: '#dc3545',
        });
        console.error(error);
      },
      complete: function() {
        // Minimum 800ms delay to show spinner
        setTimeout(function () {
          $('#Loader').fadeOut();
        }, 800); // adjust the 800ms as needed

        $('#savePayrollBtn').removeClass('d-none'); // show the save button
      }
    });
}

// Retrieve Payroll Button Click
function RetrievePayroll() {
    let year = $("#payrollYearDropdown").val();
    let payrollPeriodId = $("#payrollPeriodYearDropdown").val();
    let departmentId = $("#department").val();
    let employmentType = $("#employment_type").val();

    // Validate inputs
    if (!year || !payrollPeriodId || !departmentId) {
        alert("⚠️ Please select Payroll Year, Pay Period, and Department.");
        return;
    }

    $.ajax({
        url: "payroll_handler.php",
        type: "POST",
        data: {
            payroll_period_id: payrollPeriodId,
            department_id: departmentId,
            employment_type: employmentType,
            action: "fetch_payroll_data"
        },
        dataType: "json",
        beforeSend: function () {
            $("#payrollTable tbody").html(
                `<tr><td colspan="8" class="text-center">Loading payroll data...</td></tr>`
            );
        },
        success: function (response) {
            let rows = "";
            if (Array.isArray(response) && response.length > 0) {
                $.each(response, function (index, emp) {
                    rows += `
                      <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${emp.id_num ? emp.id_num : ""}</td>
                        <td>${emp.full_name ? emp.full_name : ""}</td>
                        <td>${emp.position_title ? emp.position_title : ""}</td>
                        <td class="text-right">${Number(emp.gross || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-right">${Number(emp.total_deductions || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-right font-weight-bold">${Number(emp.net_pay || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-center">
                          <button class="btn btn-sm btn-info" onclick='viewPayrollBreakdown(this)' 
                            data-employee='${JSON.stringify({
                              id_num: emp.id_num || "",
                              name: emp.full_name || "",
                              position: emp.position_title || "",
                              gross: emp.gross || 0,
                              deductions: emp.total_deductions || 0,
                              net: emp.net_pay || 0,
                              govshares_list: emp.govshares_breakdown ? JSON.parse(emp.govshares_breakdown) : [],
                              earnings: emp.earnings_breakdown ? JSON.parse(emp.earnings_breakdown) : [],
                              deductions_list: emp.deductions_breakdown ? JSON.parse(emp.deductions_breakdown) : []
                            })}'>
                            <i class="fas fa-search"></i> View Details
                          </button>
                        </td>
                      </tr>
                    `;
                });
                $("#payrollTable tbody").html(rows);
                $("#printPayrollBtn").removeClass("d-none");
            } else {
                $("#payrollTable tbody").html(
                    `<tr><td colspan="8" class="text-center">No payroll data found.</td></tr>`
                );
                $("#printPayrollBtn").addClass("d-none");
            }
        },
        error: function () {
            $("#payrollTable tbody").html(
                `<tr><td colspan="8" class="text-center text-danger">Error fetching payroll data.</td></tr>`
            );
            $("#printPayrollBtn").addClass("d-none");
        }
    });
}

//---Show Payroll Breakdown---//
function viewPayrollBreakdown(button) {
    const emp = $(button).data('employee');

    $('#employeeName').text(emp.name);

    // Govshares table
    let govsharesRows = '';
    emp.govshares_list.forEach(item => {
        const amount = Number(item.amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        govsharesRows += `<tr>
            <td>${item.label}</td>
            <td class="text-right">${amount}</td>
        </tr>`;
    });
     $('#govsharesTable tbody').html(govsharesRows);

    // Earnings table
    let earningsRows = '';
    emp.earnings.forEach(item => {
        const amount = Number(item.amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        earningsRows += `<tr>
            <td>${item.label}</td>
            <td class="text-right">${amount}</td>
        </tr>`;
    });
    $('#earningsTable tbody').html(earningsRows);

    // Deductions table
    let deductionsRows = '';
    emp.deductions_list.forEach(item => {
        const amount = Number(item.amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        deductionsRows += `<tr>
        <td>${item.label}</td>
        <td class="text-right">${amount}</td>
        </tr>`;
    });
    $('#deductionsTable tbody').html(deductionsRows);

    // Totals
    $('#grossTotal').text(Number(emp.gross).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));    
    $('#deductionTotal').text(Number(emp.deductions).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('#netTotal').text(Number(emp.net).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    // Show modal
    $('#payrollBreakdownModal').modal('show');
}

$('#savePayrollBtn').on('click', function () {
    // implement your save function here
    savePayrollData(); // You can define this
});

function savePayrollData() {
  $('#savePayrollBtn').prop('disabled', true).text('Saving...');
  
  const period = $('#pay_period').val();
  const dept = $('#department').val();
  const empType = $('#employment_type').val();
  var action = 'save_payroll';

  // perform your save ajax here...
  $.ajax({
        url: 'payroll_handler.php',
        method: 'POST',
        data: {
            action: action,
            period: period,
            department: dept,
            employment_type: empType
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
              $('#savePayrollBtn')
              .html('<i class="fas fa-save"></i> Payroll Saved')  // use .html() to insert HTML content
              .addClass('btn-secondary')
              .removeClass('btn-success')
              .prop('disabled', true);

              Swal.fire({
                  icon: "success",
                  title: "Payroll Saved Successfully",
                  text: "Do you want to print the payroll now?",
                  showCancelButton: true,
                  confirmButtonText: "Yes, Print",
                  cancelButtonText: "No, Later",
                  confirmButtonColor: '#28a745',
                  cancelButtonColor: '#dc3545'
              }).then((result) => {
                  if (result.isConfirmed) {
                      window.open(`../prints/print_payroll.php?period=${period}&department=${dept}&employment_type=${empType}`, '_blank');
                  }
              });
            } 
            else if(response.status === 'existing'){
              Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                  confirmButtonColor: '#dc3545',
                });
              // On success
              $('#savePayrollBtn').text('Saved').addClass('btn-secondary').removeClass('btn-success');
            }
            else if(response.status === 'not_set'){
              Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                  confirmButtonColor: '#dc3545',
                });
              // On success
              $('#savePayrollBtn').addClass('d-none');
            }
            else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.message,
                  confirmButtonColor: '#dc3545',
                });
                // On success
                $('#savePayrollBtn').addClass('d-none');
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: 'An error occurred while saving payroll.',
                  confirmButtonColor: '#dc3545',
                });
            // On success
            $('#savePayrollBtn').addClass('d-none');
        }
  });
}

$(document).ready(function() {
    $("#payrollYearDropdown").on("change", function() {
        let selectedYear = $(this).val();
        var action = 'fetch_payroll_periods_of_year';

        if (selectedYear) {
            $.ajax({
                url: "payroll_handler.php",
                type: "POST",
                data: { year: selectedYear, action: action },
                dataType: "json",
                success: function(response) {
                    let $periodDropdown = $("#payrollPeriodYearDropdown");
                    $periodDropdown.empty(); // clear old options
                    $periodDropdown.append("<option value='' hidden>Select Pay Period...</option>");

                    if (response.length > 0) {
                        $.each(response, function(index, period) {
                            $periodDropdown.append(
                                $("<option>", {
                                    value: period.payroll_period_id,
                                    text: period.period_label
                                })
                            );
                        });
                    } else {
                        $periodDropdown.append("<option value='' hidden selected>No periods available</option>");
                    }
                },
                error: function() {
                    alert("Error fetching payroll periods. Please try again.");
                }
            });
        } else {
            $("#payrollPeriodYearDropdown").html("<option value=''>-- Select Payroll Period --</option>");
        }
    });
});

$('#printPayrollBtn').on('click', function() {
    var period = $('#payrollPeriodYearDropdown').val();
    var department = $('#department').val();
    var employment_type = $('#employment_type').val();
    if (!period || !department || !employment_type) {
      alert('Please select both Pay Period and Department.');
      return;
    }
    var url = '../prints/print_payroll.php?period=' + encodeURIComponent(period) + '&department=' + encodeURIComponent(department) + '&employment_type=' + encodeURIComponent(employment_type);
    window.open(url, '_blank');
});