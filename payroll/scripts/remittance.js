$(document).ready(function() {
    $("#remitYear").on("change", function() {
        let selectedYear = $(this).val();
        var action = 'fetch_remit_month_periods_of_year';

        if (selectedYear) {
            $.ajax({
                url: "remittance_handler.php",
                type: "POST",
                data: { year: selectedYear, action: action },
                dataType: "json",
                success: function(response) {
                    let $periodDropdown = $("#remitPeriod");
                    $periodDropdown.empty(); // clear old options
                    $periodDropdown.append("<option value='' hidden>Select Remittance Period...</option>");

                    if (response.length > 0) {
                        $.each(response, function(index, period) {
                            $periodDropdown.append(
                                $("<option>", {
                                    value: period.payroll_period,
                                    text: period.period_label
                                })
                            );
                        });
                    } else {
                        $periodDropdown.append("<option value='' hidden selected>No periods available</option>");
                    }
                },
                error: function() {
                    alert("Error fetching remittance periods. Please try again.");
                }
            });
        } else {
            $("#remitPeriod").html("<option value=''hidden>Select Remittance Period...</option>");
        }
    });
});

function formatMoney(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

$(document).on('click', '#btnGenerateRemittance', function () {
    let year = $('#remitYear').val();
    let period = $('#remitPeriod').val();   // expects format "YYYY-MM-DD_YYYY-MM-DD" (2025-01-01_2025-01-31)
    //let dept = $('#remitDept').val();

    if (!year || !period) {
        alert("Please select Year and Pay Period.");
        return;
    }

    // Show loader
    $('#Loader').fadeIn();

    $.ajax({
        url: "remittance_handler.php",
        type: "POST",
        data: {
            action: "generate_remittance",
            year: year,
            period: period
        },
        dataType: "json",
        success: function (response) {
            // Clear tables
            $('#philhealthTable').html('');
            $('#gsisTable').html('');
            $('#sssTable').html('');
            $('#pagibigTable').html('');
            $('#taxTable').html('');
            $('#loansTable').html('');

            // Populate each tab’s table
            // Tax Table
            response.tax.forEach(row => {
                $('#taxTable').append(`
                    <tr>
                        <td>${row.employee}</td>
                        <td>${row.position_title.split('(')[0].trim()}</td>
                        <td>${row.tin}</td>
                        <td class="text-right">${row.locked_basic}</td>
                        <td class="text-right">${row.amount}</td>
                    </tr>
                `);
            });

            // GSIS Table
            response.gsis.forEach(row => {
                $('#gsisTable').append(`
                    <tr>
                        <td>${row.employee}</td>
                        <td>${row.position_title.split('(')[0].trim()}</td>
                        <td>${row.gsis_bp}</td>
                        <td class="text-right">${row.locked_basic}</td>
                        <td class="text-right">${row.employee_share}</td>
                        <td class="text-right">${row.employer_share}</td>
                        <td class="text-right">${row.total_amount}</td>
                    </tr>
                `);
            });

            // GSIS ECC Table
            response.gsis_ecc.forEach(row => {
                $('#gsisECCTable').append(`
                    <tr>
                        <td>${row.employee}</td>
                        <td>${row.position_title.split('(')[0].trim()}</td>
                        <td>${row.gsis_bp}</td> 
                        <td class="text-right">${row.employer_share}</td>
                    </tr>
                `);
            });

            // PhilHealth Table
            response.philhealth.forEach(row => {
                $('#philhealthTable').append(`
                    <tr>
                        <td>${row.employee}</td>
                        <td>${row.position_title.split('(')[0].trim()}</td>
                        <td>${row.philhealth_no}</td>
                        <td class="text-right">${row.locked_basic}</td>
                        <td class="text-right">${row.employee_share}</td>
                        <td class="text-right">${row.employer_share}</td>
                        <td class="text-right">${row.total}</td>
                    </tr>
                `);
            });

            // Pag-IBIG Table
            response.pagibig.forEach(row => {
                $('#pagibigTable').append(`
                    <tr>
                        <td>${row.employee}</td>
                        <td>${row.position_title.split('(')[0].trim()}</td>
                        <td>${row.pagibig_mid}</td>
                        <td class="text-right">${row.locked_basic}</td>
                        <td class="text-right">${row.employee_share}</td>
                        <td class="text-right">${row.employer_share}</td>
                        <td class="text-right">${row.total}</td>
                    </tr>
                `);
            });
            
            // SSS Table
            response.sss.forEach(row => {
                $('#sssTable').append(`
                    <tr>
                        <td>${row.employee}</td>
                        <td>${row.position_title.split('(')[0].trim()}</td>
                        <td>${row.sss_no}</td>
                        <td class="text-right">${row.employee_share}</td>
                    </tr>
                `);
            });

            // Loans Table
            response.loans.forEach(row => {
                $('#loansTable').append(`
                    <tr>
                        <td>${row.deduct_title}</td>
                        <td class="text-right">${row.total_loan_amount}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info view-breakdown" 
                                    data-loanid="${row.loan_id}" 
                                    data-title="${row.deduct_title}">
                                <i class="fas fa-search"></i> View Breakdown
                            </button>
                        </td>
                    </tr>
                `);
            });

            // Others Table
            response.others.forEach(row => {
                $('#othersTable').append(`
                    <tr>
                        <td>${row.deduct_title}</td>
                        <td class="text-right">${row.total_amount}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info view-breakdown" 
                                    data-loanid="${row.config_deduction_id}"
                                    data-title="${row.deduct_title}">
                                <i class="fas fa-search"></i> View Breakdown
                            </button>
                        </td>
                    </tr>
                `);
            });
            
        },
        error: function (xhr, status, error) {
            console.error(error);
            alert("Failed to load remittance data.");
        },
        complete: function() {
            // Minimum 800ms delay to show spinner
            setTimeout(function () {
            $('#Loader').fadeOut();
            }, 800); // adjust the 800ms as needed

        }
    });
});

// Handle Breakdown Button Click
$(document).on('click', '.view-breakdown', function() {
    let loanId = $(this).data('loanid');
    let title = $(this).data('title');
    var action = 'get_loan_breakdown';

    $('#loanBreakdownModal .modal-title').text(`${title} - Breakdown`);

    // Clear old data
    $('#loanBreakdownTable tbody').empty();

    // AJAX call to fetch breakdown
    $.ajax({
        url: 'remittance_handler.php',  // <- create backend PHP to fetch per-employee details
        method: 'POST',
        data: { loan_id: loanId, pay_period: $('#remitPeriod').val(), dept: $('#remitDept').val(), action: action },
        success: function(res) {
            let data = JSON.parse(res);
            if (data.length > 0) {
                var grand_total = 0;
                data.forEach(emp => {
                    $('#loanBreakdownTable tbody').append(`
                        <tr>
                            <td>${emp.employee_name}</td>
                            <td>${emp.position_title.split('(')[0].trim()}</td>
                            <td class="text-right">${emp.total_deduction}</td>
                        </tr>
                    `);
                    grand_total += parseFloat(emp.total_deduction);
                });
                $('#loanBreakdownTable tbody').append(`
                    <tr class="font-weight-bold">
                        <td colspan="2" class="text-right">GRAND TOTAL:</td>
                        <td class="text-right">${formatMoney(grand_total)}</td>
                    </tr>
                `);
            } else {
                $('#loanBreakdownTable tbody').append('<tr><td colspan="2" class="text-center">No data found</td></tr>');
            }
            $('#loanBreakdownModal').modal('show');
        }
    });
});

// Show/hide OR + Ref fields if status is Remitted
$(document).on('change', '#remitStatus', function () {
    if ($(this).val() === 'Remitted') {
        $('#remitExtraFields').show();
    } else {
        $('#remitExtraFields').hide();
    }
});

// Save Remittance button
$(document).on('click', '#btnSaveRemittance', function () {
    let year = $('#remitYear').val();
    let period = $('#remitPeriod').val();
    let status = $('#remitStatus').val();
    let orNumber = $('#remitOrNo').val();
    let refNumber = $('#remitRefNo').val();

    if (!year || !period) {
        alert("Please select Year and Pay Period before saving.");
        return;
    }

    // Send to PHP
    $.ajax({
        url: "remittance_handler.php",
        type: "POST",
        data: {
            action: "save_remittance",
            year: year,
            period: period,
            status: status,
            or_number: orNumber,
            reference_no: refNumber
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                alert("Remittance saved successfully with status: " + status);
            } else {
                alert("Failed to save: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error(error);
            alert("Error saving remittance.");
        }
    });
});

$('#btnViewRemittances').on('click', function() {
  let year = $('#remitYearHistory').val();
  let type = $('#remitType').val();

  if (!year || !type) {
    alert("Please select both Year and Remittance Type.");
    return;
  }
    // Fetch and display remittances 
    $.ajax({
        url: 'remittance_handler.php',
        type: 'POST',
        data: { action: 'get_remittances', year: year, type: type },
        success: function(response) {
        $('#remittanceListTable tbody').html(response);
        }
    }); 
});

// Handle View Remittance button click
$(document).on('click', '.viewRemittance', function() {
    let remittanceId = $(this).data('id');
    let remittanceType = $(this).data('type'); 
    let period = $(this).data('period');

    // fetch loan remittance type separately to group by loan titles
    if (remittanceType !== 'loans' && remittanceType !== 'others') {
        $.ajax({
            url: 'remittance_handler.php',
            type: 'POST',
            data: {
                action: 'view_remittance',
                remittance_id: remittanceId,
                remittance_type: remittanceType,
                period: period
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnPrintRemittanceDetails').data('remit_id', remittanceId);
                $('#remittanceDetailsBody').html('<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>');
                $('#remittanceDetailsModalLabel').text(remittanceType.toUpperCase() + ' Remittance Details');
                $('#remittancePeriodLabel').text('For the Period: ' + period.split('_').join(' to '));
                $('#remittanceDetailsModal').modal('show');
            },
            success: function(response) {
                if (response.success) {
                    $('#remittanceDetailsBody').html(response.html);
                    
                    // Update totals if they exist
                    if (response.totals) {
                        $('#employeeShareTotal').text(formatMoney(response.totals.employee));
                        $('#employerShareTotal').text(formatMoney(response.totals.employer));
                    }
                } else {
                    $('#remittanceDetailsBody').html(
                        '<tr><td colspan="4" class="text-center text-danger">' + 
                            (response.message || 'Failed to load details.') + 
                        '</td></tr>'
                    );
                    
                }
            },
            error: function(xhr, status, error) {
                $('#remittanceDetailsBody').html(
                    '<tr><td colspan="4" class="text-center text-danger">' +
                    'Error loading details: ' + error +
                    '</td></tr>'
                );
            }
        });
    }
    else if (remittanceType === 'loans' && remittanceType !== 'others') {
        // Handle loans remittance viewing separately
        $.ajax({
            url: 'remittance_handler.php',
            type: 'POST',
            dataType: 'json', // expect JSON { success, period, html, message? }
            data: {
                action: 'view_loans_remittance',
                remittance_id: remittanceId
            },
            beforeSend: function() {
                $('#btnPrintLoansRemittanceDetails').data('loan_remit_id', remittanceId);
                $('#remittanceLoansDetailsBody').html('<tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>');
                $('#remittanceLoansDetailsModal').modal('show');
            },
            success: function(response) {
                if (response && response.success) {
                    // insert returned html and update period label (if any)
                    $('#remittanceLoansDetailsBody').html(response.html || '');
                    if (response.period) {
                        $('#remittanceLoansPeriodLabel').text('For the Period: ' + response.period.split('_').join(' to '));
                    }
                } else {
                    var msg = (response && response.message) ? response.message : 'No loan remittance details found.';
                    $('#remittanceLoansDetailsBody').html('<tr><td colspan="3" class="text-center text-muted">' + msg + '</td></tr>');
                }
            },
            error: function(xhr, status, err) {
                $('#remittanceLoansDetailsBody').html('<tr><td colspan="3" class="text-center text-danger">Failed to load details: ' + err + '</td></tr>');
            }
        });
    }
    else {
        // others remittance type handling can go here
        $.ajax({
            url: 'remittance_handler.php',
            type: 'POST',
            data: {
                action: 'view_others_remittance',
                remittance_id: remittanceId,
                remittance_type: remittanceType,
                period: period
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnPrintOthersRemittanceDetails').data('remit_id', remittanceId);
                $('#remittanceOthersDetailsBody').html('<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>');
                $('#remittanceOthersPeriodLabel').text('For the Period: ' + period.split('_').join(' to '));
                $('#remittanceOthersDetailsModal').modal('show');
            },
            success: function(response) {
                if (response.success) {
                    $('#remittanceOthersDetailsBody').html(response.html);
                } else {
                    $('#remittanceOthersDetailsBody').html(
                        '<tr><td colspan="4" class="text-center text-danger">' + 
                            (response.message || 'Failed to load details.') + 
                        '</td></tr>'
                    );
                }
            },
            error: function(xhr, status, error) {
                $('#remittanceOthersDetailsBody').html(
                    '<tr><td colspan="4" class="text-center text-danger">' +
                    'Error loading details: ' + error +
                    '</td></tr>'
                );
            }
        });
    }
});

$(document).on('click', '.viewLoanRemittanceBreakdown', function() {
    let employees = $(this).data('employees');
    const loanTitle = $(this).data('loan-title');
    const period = $(this).data('period');

    // Fix: Parse only if it's still a string
    if (typeof employees === 'string') {
        employees = JSON.parse(employees);
    }

    let detailRows = '';
    let total = 0;

    employees.forEach(emp => {
        total += parseFloat(emp.amount);
        detailRows += `
            <tr>
                <td>${emp.employee_name}</td>
                <td>${emp.position_title.split('(')[0].trim()}</td>
                <td class="text-right">${formatMoney(emp.amount)}</td>
            </tr>
        `;
    });

    detailRows += `
        <tr class="font-weight-bold">
            <td colspan="2" class="text-right">TOTAL:</td>
            <td class="text-right">${formatMoney(total)}</td>
        </tr>
    `;

    $('#btnPrintLoanRemitBreakdown').data('loans_breakdown', JSON.stringify(employees));
    $('#btnPrintLoanRemitBreakdown').data('breakdown_period', period);
    $('#loanRemitBreakdownTitle').text(loanTitle);
    $('#loanRemitBreakdownPeriodLabel').text('For the Period: ' + period.split('_').join(' to '));
    $('#loanRemitBreakdownTable tbody').html(detailRows);
    $('#loanRemitBreakdownModal').modal('show');
});

$(document).on('click', '.viewOtherRemittanceBreakdown', function() {
    let employees = $(this).data('employees');
    const deductTitle = $(this).data('deduction-title');
    const period = $(this).data('period');
    // Fix: Parse only if it's still a string
    if (typeof employees === 'string') {
        employees = JSON.parse(employees);
    }
    let detailRows = '';
    let total = 0;
    employees.forEach(emp => {
        total += parseFloat(emp.amount);
        detailRows += `
            <tr>
                <td>${emp.employee_name}</td>
                <td>${emp.position_title.split('(')[0].trim()}</td>
                <td class="text-right">${formatMoney(emp.amount)}</td>
            </tr>
        `;
    });

    detailRows += `
        <tr class="font-weight-bold">
            <td colspan="2" class="text-right">TOTAL:</td>
            <td class="text-right">${formatMoney(total)}</td>
        </tr>
    `;

    $('#btnPrintOthersRemitBreakdown').data('others_breakdown', JSON.stringify(employees));
    $('#btnPrintOthersRemitBreakdown').data('breakdown_period', period);
    $('#othersRemitBreakdownTitle').text(deductTitle);
    $('#othersRemitBreakdownPeriodLabel').text('For the Period: ' + period.split('_').join(' to '));
    $('#othersRemitBreakdownTable tbody').html(detailRows);
    $('#othersRemitBreakdownModal').modal('show');
});

// === Print Remittance Details ===



$(document).on('click', '#btnPrintRemittanceDetails', function() {
    let remittance_id = $(this).data('remit_id');

    // Create a form dynamically
    let form = $('<form>', {
        action: '../prints/print_remit_details.php',
        method: 'POST',
        target: '_blank' // open in new tab
    });

    // Append your POST variables
    form.append($('<input>', {type: 'hidden', name: 'remittance_id', value: remittance_id}));

    // (Optional) Add more POST data here
    // form.append($('<input>', {type: 'hidden', name: 'user_id', value: user_id}));

    // Append form to body and submit
    $('body').append(form);
    form.submit();
    form.remove(); // clean up
});

// === 1. For Loans Remittance Details ===
$(document).on('click', '#btnPrintLoansRemittanceDetails', function() {
    let remittance_id = $(this).data('loan_remit_id');
    let print_type = 'loans';

    // Create and submit form
    let form = $('<form>', {
        action: '../prints/print_remit_details.php',
        method: 'POST',
        target: '_blank'
    });

    form.append($('<input>', { type: 'hidden', name: 'remittance_id', value: remittance_id }));
    form.append($('<input>', { type: 'hidden', name: 'print_type', value: print_type }));

    $('body').append(form);
    form.submit();
    form.remove();
});

$(document).on('click', '#btnPrintOthersRemittanceDetails', function() {
    let remittance_id = $(this).data('remit_id');
    let print_type = 'others';
    // Create and submit form
    let form = $('<form>', {
        action: '../prints/print_remit_details.php',
        method: 'POST',
        target: '_blank'
    });
    form.append($('<input>', { type: 'hidden', name: 'remittance_id', value: remittance_id }));
    form.append($('<input>', { type: 'hidden', name: 'print_type', value: print_type }));

    $('body').append(form);
    form.submit();
    form.remove();
});

// === 2. For Loan Remittance Breakdown ===
$(document).on('click', '#btnPrintLoanRemitBreakdown', function() {
    let loans_breakdown = $(this).data('loans_breakdown');
    let breakdown_period = $(this).data('breakdown_period');
    let loan_title = $('#loanRemitBreakdownTitle').text();
    let print_type = 'loans_breakdown';

    // Create and submit form
    let form = $('<form>', {
        action: '../prints/print_remit_details.php',
        method: 'POST',
        target: '_blank'
    });

    form.append($('<input>', { type: 'hidden', name: 'breakdown', value: loans_breakdown }));
    form.append($('<input>', { type: 'hidden', name: 'loan_title', value: loan_title }));
    form.append($('<input>', { type: 'hidden', name: 'print_type', value: print_type }));
    form.append($('<input>', { type: 'hidden', name: 'period', value: breakdown_period }));

    $('body').append(form);
    form.submit();
    form.remove();
});

// === 3. For Other Deduction Remittance Breakdown ===
$(document).on('click', '#btnPrintOthersRemitBreakdown', function() {
    let others_breakdown = $(this).data('others_breakdown');
    let breakdown_period = $(this).data('breakdown_period');
    let deduct_title = $('#othersRemitBreakdownTitle').text();
    let print_type = 'others_breakdown';
    // Create and submit form
    let form = $('<form>', {
        action: '../prints/print_remit_details.php',
        method: 'POST',
        target: '_blank'
    });
    form.append($('<input>', { type: 'hidden', name: 'breakdown', value: others_breakdown }));
    form.append($('<input>', { type: 'hidden', name: 'deduct_title', value: deduct_title }));
    form.append($('<input>', { type: 'hidden', name: 'print_type', value: print_type }));
    form.append($('<input>', { type: 'hidden', name: 'period', value: breakdown_period }));
    $('body').append(form);
    form.submit();
    form.remove();
});


