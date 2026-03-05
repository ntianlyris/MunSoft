
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
                `<tr><td colspan="9" class="text-center">Loading payroll data...</td></tr>`
            );
        },
        success: function (response) {
            let rows = "";
            if (Array.isArray(response) && response.length > 0) {
                $.each(response, function (index, emp) {
                    // Determine status badge color
                    let statusBadgeClass = 'badge-secondary';
                    if (emp.status === 'DRAFT') statusBadgeClass = 'badge-secondary';
                    else if (emp.status === 'REVIEW') statusBadgeClass = 'badge-warning';
                    else if (emp.status === 'APPROVED') statusBadgeClass = 'badge-info';
                    else if (emp.status === 'PAID') statusBadgeClass = 'badge-success';

                    // Build action buttons based on status
                    let actionButtons = `
                      <button class="btn btn-sm btn-info" onclick='viewPayrollBreakdown(this)' 
                        data-employee='${JSON.stringify({
                          payroll_entry_id: emp.payroll_entry_id || 0,
                          id_num: emp.id_num || "",
                          name: emp.full_name || "",
                          position: emp.position_title || "",
                          gross: emp.gross || 0,
                          deductions: emp.total_deductions || 0,
                          net: emp.net_pay || 0,
                          status: emp.status || "DRAFT",
                          govshares_list: emp.govshares_breakdown ? JSON.parse(emp.govshares_breakdown) : [],
                          earnings: emp.earnings_breakdown ? JSON.parse(emp.earnings_breakdown) : [],
                          deductions_list: emp.deductions_breakdown ? JSON.parse(emp.deductions_breakdown) : []
                        })}' title="View Payroll Details">
                        <i class="fas fa-search"></i> View
                      </button>
                    `;

                    // Add workflow status buttons
                    if (emp.status === 'DRAFT') {
                        actionButtons += ` 
                          <button class="btn btn-sm btn-primary ml-1" onclick="submitForReview([${emp.payroll_entry_id}])" title="Submit for Review">
                            <i class="fas fa-paper-plane"></i>
                          </button>
                        `;
                    } else if (emp.status === 'REVIEW') {
                        actionButtons += ` 
                          <button class="btn btn-sm btn-success ml-1" onclick="approvePayroll([${emp.payroll_entry_id}])" title="Approve">
                            <i class="fas fa-check"></i>
                          </button>
                          <button class="btn btn-sm btn-warning ml-1" onclick="returnToDraft([${emp.payroll_entry_id}])" title="Return to Draft">
                            <i class="fas fa-undo"></i>
                          </button>
                        `;
                    } else if (emp.status === 'APPROVED') {
                        actionButtons += ` 
                          <button class="btn btn-sm btn-success ml-1" onclick="markAsPaid([${emp.payroll_entry_id}])" title="Mark as Paid">
                            <i class="fas fa-money-bill-wave"></i>
                          </button>
                        `;
                    }

                    // Add history button for all statuses except DRAFT
                    if (emp.status !== 'DRAFT') {
                        actionButtons += ` 
                          <button class="btn btn-sm btn-secondary ml-1" onclick="viewTransitionHistory(${emp.payroll_entry_id})" title="View History">
                            <i class="fas fa-history"></i>
                          </button>
                        `;
                    }

                    rows += `
                      <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${emp.id_num ? emp.id_num : ""}</td>
                        <td>${emp.full_name ? emp.full_name : ""}</td>
                        <td>${emp.position_title ? emp.position_title : ""}</td>
                        <td class="text-right">${Number(emp.gross || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-right">${Number(emp.total_deductions || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-right font-weight-bold">
                          ${Number(emp.net_pay || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </td>
                        <td class="text-center">
                          <span class="badge ${statusBadgeClass}" style="font-size: 0.85rem;">${emp.status || 'DRAFT'}</span>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                          ${actionButtons}
                        </td>
                      </tr>
                    `;
                });
                $("#payrollTable tbody").html(rows);
                $("#printPayrollBtn").removeClass("d-none");
                $("#exportPayrollExcelBtn").removeClass("d-none");
                $("#deletePayrollRecordsBtn").removeClass("d-none");
                
                // Update status counts
                getPayrollStatusCounts();
            } else {
                $("#payrollTable tbody").html(
                    `<tr><td colspan="9" class="text-center">No payroll data found.</td></tr>`
                );
                $("#printPayrollBtn").addClass("d-none");
                $("#exportPayrollExcelBtn").addClass("d-none");
                $("#deletePayrollRecordsBtn").addClass("d-none");
            }
        },
        error: function () {
            $("#payrollTable tbody").html(
                `<tr><td colspan="9" class="text-center text-danger">Error fetching payroll data.</td></tr>`
            );
            $("#printPayrollBtn").addClass("d-none");
            $("#exportPayrollExcelBtn").addClass("d-none");
            $("#deletePayrollRecordsBtn").addClass("d-none");
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

$('#exportPayrollExcelBtn').on('click', function() {
    var period = $('#payrollPeriodYearDropdown').val();
    var department = $('#department').val();
    var employment_type = $('#employment_type').val();
    if (!period || !department || !employment_type) {
      alert('Please select both Pay Period and Department.');
      return;
    }
    var url = '../prints/export_payroll_excel.php?period=' + encodeURIComponent(period) + '&department=' + encodeURIComponent(department) + '&employment_type=' + encodeURIComponent(employment_type);
    window.open(url, '_blank');
});

/**
 * Change payroll status (workflow)
 */
function changePayrollStatus(payrollId, newStatus) {
    $.ajax({
        url: 'payroll_handler.php',
        type: 'POST',
        dataType: 'json',
        data: {
            payroll_entry_id: payrollId,
            status: newStatus,
            action: 'update_payroll_status'
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to update payroll status.",
                confirmButtonColor: '#dc3545',
            });
        },
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: response.message,
                    confirmButtonColor: '#28a745',
                }).then(function() {
                    RetrievePayroll();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.message,
                    confirmButtonColor: '#dc3545',
                });
            }
        }
    });
}

/**
 * View audit trail for payroll entry
 */
function viewAuditTrail(payrollId) {
    $.ajax({
        url: 'payroll_handler.php',
        type: 'POST',
        dataType: 'json',
        data: {
            payroll_entry_id: payrollId,
            limit: 50,
            action: 'get_payroll_audit_trail'
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to load audit trail.",
                confirmButtonColor: '#dc3545',
            });
        },
        success: function(auditTrail) {
            if (auditTrail && auditTrail.length > 0) {
                let auditHtml = '<div style="text-align: left; max-height: 400px; overflow-y: auto;">';
                auditHtml += '<table class="table table-sm">';
                auditHtml += '<thead><tr><th>Action</th><th>User</th><th>Date</th></tr></thead><tbody>';
                
                auditTrail.forEach(function(audit) {
                    const actionDate = new Date(audit.action_date).toLocaleString();
                    auditHtml += '<tr>';
                    auditHtml += '<td><strong>' + audit.action + '</strong></td>';
                    auditHtml += '<td>' + audit.changed_by + '</td>';
                    auditHtml += '<td>' + actionDate + '</td>';
                    auditHtml += '</tr>';
                });
                
                auditHtml += '</tbody></table></div>';
                
                Swal.fire({
                    title: 'Audit Trail',
                    html: auditHtml,
                    icon: 'info',
                    width: '80%',
                    confirmButtonColor: '#3085d6',
                });
            } else {
                Swal.fire({
                    title: 'Audit Trail',
                    text: 'No audit trail records found.',
                    icon: 'info',
                });
            }
        }
    });
}

/**
 * BULK DELETE: Delete DRAFT payroll records for the selected period and department
 * SECURITY: ONLY deletes DRAFT status entries - locked/approved/paid entries are protected
 * Requires explicit user confirmation before deletion
 */
function deleteAllPayrollRecords() {
    // Get current selection values
    const payroll_period_id = $('#payrollPeriodYearDropdown').val();
    const dept_id = $('#department').val();
    const emp_type_stamp = $('#employment_type').val();

    // Validate selections
    if (!payroll_period_id || !dept_id || !emp_type_stamp) {
        Swal.fire({
            icon: "warning",
            title: "Missing Information",
            text: "Please select Period, Department, and Employment Type before deleting.",
            confirmButtonColor: '#ffc107',
        });
        return;
    }

    // Get friendly display values for confirmation message
    const period_label = $('#payrollPeriodYearDropdown option:selected').text();
    const dept_label = $('#department option:selected').text();

    // SECURITY: Double confirmation - show warning first
    Swal.fire({
        title: 'Delete DRAFT Payroll Records?',
        html: `
            <p><strong style="color: red;">⚠️ WARNING: This action cannot be undone!</strong></p>
            <p>You are about to delete ALL <strong>DRAFT</strong> payroll records for:</p>
            <ul style="text-align: left;">
                <li><strong>Period:</strong> ${period_label}</li>
                <li><strong>Department:</strong> ${dept_label}</li>
                <li><strong>Type:</strong> ${emp_type_stamp}</li>
            </ul>
            <p style="background-color: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0;">
                <strong>✓ PROTECTED:</strong> Payroll records with status SUBMITTED, APPROVED, PAID, or LOCKED will <strong>NOT</strong> be deleted.
            </p>
            <p>Deleted records will include:</p>
            <ul style="text-align: left;">
                <li>All payroll entry records with DRAFT status</li>
                <li>Associated deductions for each deleted entry</li>
                <li>Associated government shares for each deleted entry</li>
            </ul>
            <p style="color: red;"><strong>🔒 This action is PERMANENT and will be logged for audit purposes.</strong></p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete DRAFT Records Only',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loader
            $('#Loader').fadeIn();

            // Send AJAX request for bulk deletion
            $.ajax({
                url: 'payroll_handler.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    payroll_period_id: payroll_period_id,
                    dept_id: dept_id,
                    emp_type_stamp: emp_type_stamp,
                    action: 'delete_payroll_records'
                },
                success: function(response) {
                    $('#Loader').fadeOut();

                    if (response.status === 'success') {
                        let summaryHtml = `<p>${response.message}</p>`;
                        summaryHtml += '<div style="text-align: left; margin-top: 15px;">';
                        summaryHtml += '<p><strong>📊 Deletion Summary:</strong></p>';
                        summaryHtml += '<ul>';
                        summaryHtml += `<li>✓ Payroll Entries Deleted: <strong>${response.deleted_entries}</strong></li>`;
                        summaryHtml += `<li>✓ Deductions Deleted: <strong>${response.deleted_deductions}</strong></li>`;
                        summaryHtml += `<li>✓ Government Shares Deleted: <strong>${response.deleted_govshares}</strong></li>`;
                        summaryHtml += `<li>💰 Total Gross Amount Deleted: <strong>₱${Number(response.total_gross_deleted).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></li>`;
                        
                        if (response.non_draft_protected > 0) {
                            summaryHtml += `<li style="background-color: #d4edda; padding: 5px; margin-top: 10px;">🛡️ <strong>Protected Records (NOT deleted):</strong> ${response.non_draft_protected}</li>`;
                        }
                        
                        summaryHtml += '</ul>';
                        summaryHtml += '</div>';
                        
                        Swal.fire({
                            icon: "success",
                            title: "Deletion Successful",
                            html: summaryHtml,
                            confirmButtonColor: '#28a745',
                        }).then(() => {
                            // Clear the table after successful deletion
                            $('#payrollTable tbody').html(`
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No payroll data available. Please retrieve again.</td>
                                </tr>
                            `);
                            // Hide the delete button
                            $('#deletePayrollRecordsBtn').addClass('d-none');
                            $('#printPayrollBtn').addClass('d-none');
                        });
                    } else if (response.status === 'warning') {
                        Swal.fire({
                            icon: "warning",
                            title: "No DRAFT Records Found",
                            html: `
                                <p>${response.message}</p>
                                ${response.non_draft_count > 0 ? `<p style="background-color: #d4edda; padding: 10px; margin-top: 10px;">🛡️ <strong>${response.non_draft_count} non-DRAFT record(s) are protected and cannot be deleted.</strong></p>` : ''}
                            `,
                            confirmButtonColor: '#ffc107',
                        });
                    } else if (response.status === 'info') {
                        Swal.fire({
                            icon: "info",
                            title: "No Records Found",
                            text: response.message,
                            confirmButtonColor: '#17a2b8',
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Deletion Failed",
                            text: response.message,
                            confirmButtonColor: '#dc3545',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#Loader').fadeOut();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred during deletion. Please try again.",
                        confirmButtonColor: '#dc3545',
                    });
                }
            });
        }
    });
}

/**
 * WORKFLOW: Submit multiple payroll entries for review (DRAFT → REVIEW)
 */
function submitForReview(payrollEntryIds) {
    if (!payrollEntryIds || payrollEntryIds.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No Selection",
            text: "Please select payroll records to submit for review.",
            confirmButtonColor: '#ffc107',
        });
        return;
    }

    Swal.fire({
        title: 'Submit for Review?',
        html: `
            <p>You are about to submit <strong>${payrollEntryIds.length}</strong> payroll record(s) for review.</p>
            <p>These records will move from <strong>DRAFT</strong> to <strong>REVIEW</strong> status.</p>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Submit for Review',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#Loader').fadeIn();

            $.ajax({
                url: 'payroll_handler.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    payroll_entry_ids: payrollEntryIds,
                    new_status: 'REVIEW',
                    action: 'update_payroll_status_bulk'
                },
                success: function(response) {
                    $('#Loader').fadeOut();
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: response.message,
                            confirmButtonColor: '#28a745',
                        }).then(() => {
                            RetrievePayroll();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message + (response.failed ? ` (${response.failed.length} failed)` : ''),
                            confirmButtonColor: '#dc3545',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#Loader').fadeOut();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to submit for review.",
                        confirmButtonColor: '#dc3545',
                    });
                }
            });
        }
    });
}

/**
 * WORKFLOW: Approve multiple payroll entries (REVIEW → APPROVED)
 */
function approvePayroll(payrollEntryIds) {
    if (!payrollEntryIds || payrollEntryIds.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No Selection",
            text: "Please select payroll records to approve.",
            confirmButtonColor: '#ffc107',
        });
        return;
    }

    Swal.fire({
        title: 'Approve Payroll?',
        html: `
            <p>You are about to approve <strong>${payrollEntryIds.length}</strong> payroll record(s).</p>
            <p>These records will move from <strong>REVIEW</strong> to <strong>APPROVED</strong> status.</p>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#Loader').fadeIn();

            $.ajax({
                url: 'payroll_handler.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    payroll_entry_ids: payrollEntryIds,
                    new_status: 'APPROVED',
                    action: 'update_payroll_status_bulk'
                },
                success: function(response) {
                    $('#Loader').fadeOut();
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: response.message,
                            confirmButtonColor: '#28a745',
                        }).then(() => {
                            RetrievePayroll();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            confirmButtonColor: '#dc3545',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#Loader').fadeOut();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to approve payroll.",
                        confirmButtonColor: '#dc3545',
                    });
                }
            });
        }
    });
}

/**
 * WORKFLOW: Return payroll entries to draft (REVIEW → DRAFT) with reason
 */
function returnToDraft(payrollEntryIds) {
    if (!payrollEntryIds || payrollEntryIds.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No Selection",
            text: "Please select payroll records to return.",
            confirmButtonColor: '#ffc107',
        });
        return;
    }

    Swal.fire({
        title: 'Return to Draft?',
        html: `
            <p>You are about to return <strong>${payrollEntryIds.length}</strong> payroll record(s) to DRAFT status.</p>
            <label for="returnReason"><strong>Reason for Return:</strong></label>
            <textarea id="returnReason" class="swal2-textarea" placeholder="Enter reason for returning to draft..." style="width: 100%; min-height: 80px;"></textarea>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Return to Draft',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const reason = Swal.getPopup().querySelector('#returnReason').value;
            if (!reason || reason.trim() === '') {
                Swal.showValidationMessage('Please provide a reason for returning to draft');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $('#Loader').fadeIn();

            $.ajax({
                url: 'payroll_handler.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    payroll_entry_ids: payrollEntryIds,
                    new_status: 'DRAFT',
                    reason: result.value,
                    action: 'update_payroll_status_bulk'
                },
                success: function(response) {
                    $('#Loader').fadeOut();
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: response.message,
                            confirmButtonColor: '#28a745',
                        }).then(() => {
                            RetrievePayroll();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            confirmButtonColor: '#dc3545',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#Loader').fadeOut();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to return payroll to draft.",
                        confirmButtonColor: '#dc3545',
                    });
                }
            });
        }
    });
}

/**
 * WORKFLOW: Mark payroll as paid (APPROVED → PAID)
 */
function markAsPaid(payrollEntryIds) {
    if (!payrollEntryIds || payrollEntryIds.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No Selection",
            text: "Please select payroll records to mark as paid.",
            confirmButtonColor: '#ffc107',
        });
        return;
    }

    Swal.fire({
        title: 'Mark as Paid?',
        html: `
            <p>You are about to mark <strong>${payrollEntryIds.length}</strong> payroll record(s) as PAID.</p>
            <p><strong style="color: red;">⚠️ WARNING:</strong> Once marked as PAID, these records CANNOT be edited or returned to previous status.</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Mark as Paid',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#Loader').fadeIn();

            $.ajax({
                url: 'payroll_handler.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    payroll_entry_ids: payrollEntryIds,
                    new_status: 'PAID',
                    action: 'update_payroll_status_bulk'
                },
                success: function(response) {
                    $('#Loader').fadeOut();
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: response.message,
                            confirmButtonColor: '#28a745',
                        }).then(() => {
                            RetrievePayroll();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            confirmButtonColor: '#dc3545',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#Loader').fadeOut();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to mark payroll as paid.",
                        confirmButtonColor: '#dc3545',
                    });
                }
            });
        }
    });
}

/**
 * Get payroll status distribution for dashboard/summary
 */
function getPayrollStatusCounts() {
    const payroll_period_id = $('#payrollPeriodYearDropdown').val();
    const dept_id = $('#department').val();
    const emp_type = $('#employment_type').val();

    if (!payroll_period_id || !dept_id || !emp_type) {
        return;
    }

    $.ajax({
        url: 'payroll_handler.php',
        type: 'POST',
        dataType: 'json',
        data: {
            payroll_period_id: payroll_period_id,
            dept_id: dept_id,
            emp_type: emp_type,
            action: 'get_payroll_status_counts'
        },
        success: function(response) {
            if (response.status === 'success') {
                // Update status badges/counters in UI if they exist
                const counts = response.counts;
                
                if ($('#statusDraftCount').length) {
                    $('#statusDraftCount').text(counts.DRAFT || 0);
                }
                if ($('#statusReviewCount').length) {
                    $('#statusReviewCount').text(counts.REVIEW || 0);
                }
                if ($('#statusApprovedCount').length) {
                    $('#statusApprovedCount').text(counts.APPROVED || 0);
                }
                if ($('#statusPaidCount').length) {
                    $('#statusPaidCount').text(counts.PAID || 0);
                }
            }
        }
    });
}

/**
 * Get workflow transition history for a payroll entry
 */
function viewTransitionHistory(payrollId) {
    $.ajax({
        url: 'payroll_handler.php',
        type: 'POST',
        dataType: 'json',
        data: {
            payroll_entry_id: payrollId,
            limit: 50,
            action: 'get_transition_history'
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to load transition history.",
                confirmButtonColor: '#dc3545',
            });
        },
        success: function(response) {
            if (response.status === 'success' && response.transitions && response.transitions.length > 0) {
                let historyHtml = '<div style="text-align: left; max-height: 400px; overflow-y: auto;">';
                historyHtml += '<table class="table table-sm">';
                historyHtml += '<thead><tr><th>From</th><th>To</th><th>By</th><th>Date</th><th>Reason</th></tr></thead><tbody>';
                
                response.transitions.forEach(function(transition) {
                    const changeDate = new Date(transition.changed_date).toLocaleString();
                    const reason = transition.reason ? transition.reason.substring(0, 30) + '...' : '-';
                    
                    historyHtml += '<tr>';
                    historyHtml += '<td><span class="badge bg-light text-dark">' + transition.from_status + '</span></td>';
                    historyHtml += '<td><span class="badge bg-info">' + transition.to_status + '</span></td>';
                    historyHtml += '<td>' + (transition.username || 'System') + '</td>';
                    historyHtml += '<td><small>' + changeDate + '</small></td>';
                    historyHtml += '<td><small>' + reason + '</small></td>';
                    historyHtml += '</tr>';
                });
                
                historyHtml += '</tbody></table></div>';
                
                Swal.fire({
                    title: 'Workflow Transition History',
                    html: historyHtml,
                    icon: 'info',
                    width: '90%',
                    confirmButtonColor: '#3085d6',
                });
            } else {
                Swal.fire({
                    title: 'Transition History',
                    text: 'No transition history found.',
                    icon: 'info',
                });
            }
        }
    });
}