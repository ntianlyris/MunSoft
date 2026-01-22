function formatMoney(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

$(document).ready(function() {
    $("#payslipYear").on("change", function() {
        let selectedYear = $(this).val();
        var action = 'fetch_payslip_month_periods_of_year';

        if (selectedYear) {
            $.ajax({
                url: "payslip_handler.php",
                type: "POST",
                data: { year: selectedYear, action: action },
                dataType: "json",
                success: function(response) {
                    let $periodDropdown = $("#payslipPeriod");
                    $periodDropdown.empty(); // clear old options
                    $periodDropdown.append("<option value='' hidden>-- Choose Period --</option>");

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
            $("#remitPeriod").html("<option value=''hidden>-- Choose Period --</option>");
        }
    });
});

$("#formPaySlipGenerator").on('submit', function(e){
    e.preventDefault();

    var formData = $(this).serializeArray();
    formData.push({ name: "action", value: "generate_payslip" });

    $.ajax({
        url: "payslip_handler.php",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {

            // If response contains the payslip data (not "success" message)
            if(response.employee_num){  

                // Assign simple fields directly to IDs
                $("#employee_num").text(response.employee_num);
                $("#employee_name").text(response.employee_name);
                $("#position").text(response.position);
                $("#department").text(response.department);
                $("#payCoverage").text(response.coverage);
                $("#basic").text(formatMoney(response.basic));
                $("#grossPay").text(formatMoney(response.gross));
                $("#other_earnings").text(formatMoney(response.other_earnings));
                $("#totalDeductions").text(formatMoney(response.deductions.total_deductions));
                $("#netPay").text(formatMoney(response.net_pay));

                // Handle deductions list
                let dedContainer = $("#deductions_container");
                dedContainer.empty(); // clear previous contents

                $.each(response.deductions, function(key, item){
                    if(key !== "total_deductions"){ 
                        dedContainer.append(`
                            <tr>
                                <td>${item.label}</td>
                                <td class="text-right">${formatMoney(item.amount)}</td>
                            </tr>
                        `);
                    }
                });

                // Total deductions
                $("#total_deductions").text(response.deductions.total_deductions);

                // Store payslip data in data attributes for printing
                $("#btnPrintPayslip").data('employee_id', $("#payslipEmployee").val());
                $("#btnPrintPayslip").data('year', $("#payslipYear").val());
                $("#btnPrintPayslip").data('payroll_period', $("#payslipPeriod").val());
                $("#btnPrintPayslip").prop('disabled', false);

                // Show modal or update UI here
                $("#payslipResultModal").modal("show");
                
            } else {
                // Original error handling
                Swal.fire("Error", response.message, "error");
            }
        },
        error: function() {
            Swal.fire("Error", "Unexpected error occurred.", "error");
        }
    });
});

// Print Payslip Button Handler
$("#btnPrintPayslip").on("click", function() {
    var employee_id = $(this).data('employee_id');
    var year = $(this).data('year');
    var payroll_period = $(this).data('payroll_period');

    // Validate that payslip has been generated
    if (!employee_id || !year || !payroll_period) {
        Swal.fire("Warning", "Please generate a payslip first before printing.", "warning");
        return;
    }

    // Create a form and submit to print_payslip.php
    var form = $('<form>', {
        action: '../prints/print_payslip.php',
        method: 'POST',
        target: '_blank'
    });

    form.append($('<input>', {
        type: 'hidden',
        name: 'employee_id',
        value: employee_id
    }));

    form.append($('<input>', {
        type: 'hidden',
        name: 'year',
        value: year
    }));

    form.append($('<input>', {
        type: 'hidden',
        name: 'payroll_period',
        value: payroll_period
    }));

    $('body').append(form);
    form.submit();
    form.remove();
});