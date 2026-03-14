$(document).ready(function() {
    // Store all data for flexible filtering
    let allData = {};
    let currentLoanBreakdown = null;
    let currentPeriodLabel = null;  // Store period label for loan breakdown

    // ===== YEAR CHANGE: Fetch Pay Periods =====
    $("#report_year").on("change", function() {
        const year = $(this).val();
        if (year) {
            $.ajax({
                url: "payroll_handler.php",
                type: "POST",
                data: { year: year, action: 'fetch_payroll_periods_of_year' },
                dataType: "json",
                success: function(response) {
                    const $dropdown = $("#pay_period");
                    $dropdown.empty().append("<option value='' hidden>Select Pay Period...</option>");
                    if (response.length > 0) {
                        $.each(response, function(i, p) {
                            $dropdown.append($("<option>", { 
                                value: p.payroll_period_id, 
                                text: p.period_label 
                            }));
                        });
                    }
                }
            });
        }
    });

    // ===== GENERATE REPORT BUTTON: Fetch ALL Deduction Types =====
    $("#generateReportBtn").on("click", function() {
        const year = $("#report_year").val();
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val() || null;

        if (!year || !periodId) {
            Swal.fire('Required Fields', 'Please select Year and Pay Period.', 'warning');
            return;
        }

        $.ajax({
            url: 'deduction_registers_handler.php',
            type: 'POST',
            data: {
                action: 'fetch_deduction_register',
                year: year,
                period_id: periodId,
                dept_id: deptId
            },
            dataType: 'json',
            beforeSend: function() {
                $('#Loader').fadeIn();
                $("#generateReportBtn").prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Generating...');
            },
            success: function(res) {
                $('#Loader').fadeOut();
                $("#generateReportBtn").prop('disabled', false)
                    .html('<i class="fas fa-cogs"></i> Generate Report');

                if (res.status === 'success') {
                    allData = res.data;
                    // Store period label for loan breakdown requests
                    currentPeriodLabel = res.period.label;
                    displayAllDeductionTypes(allData);
                    $("#resultsCard").fadeIn();
                    $("#quickSelectCard").fadeIn();
                    $('#deductionTabs > li:first-child > a').tab('show');
                } else {
                    Swal.fire('Error', res.message || 'Failed to fetch report data.', 'error');
                    $("#resultsCard").fadeOut();
                    $("#quickSelectCard").fadeOut();
                }
            },
            error: function() {
                $('#Loader').fadeOut();
                $("#generateReportBtn").prop('disabled', false)
                    .html('<i class="fas fa-cogs"></i> Generate Report');
                Swal.fire('Error', 'Failed to fetch report data.', 'error');
            }
        });
    });

    // ===== QUICK VIEW BUTTONS: Show/Hide Tabs =====
    $(document).on('click', '.quickViewBtn', function() {
        const type = $(this).data('type');
        
        if (type === 'all') {
            // Show all tabs
            $('[role="tabpanel"]').show();
            $('#deductionTabs li > a').removeClass('hidden-tab');
        } else {
            // Hide all tabs except selected
            $('[role="tabpanel"]').hide();
            $(`#tab_${type}`).show();
            // Update active tab
            $(`.nav-tabs a[href="#tab_${type}"]`).tab('show');
        }
        
        // Highlight active button
        $('.quickViewBtn').removeClass('active');
        $(this).addClass('active');
    });

    // ===== QUICK EXPORT BUTTONS: Export All =====
    $(document).on('click', '.quickExportBtn', function() {
        const exportType = $(this).data('export-type');
        exportAllDeductions(exportType);
    });

    // ===== QUICK PRINT BUTTON: Print All =====
    $(document).on('click', '.quickPrintBtn', function() {
        printAllDeductions();
    });

    // ===== TYPE-SPECIFIC EXPORT: Export One Type =====
    $(document).on('click', '.typeExportBtn', function() {
        const type = $(this).data('type');
        const exportFormat = $(this).data('export');
        exportDeductionType(type, exportFormat);
    });

    // ===== TYPE-SPECIFIC PRINT: Print One Type =====
    $(document).on('click', '.typePrintBtn', function() {
        const type = $(this).data('type');
        printDeductionType(type);
    });

    // ===== LOAN BREAKDOWN: View Loan Details =====
    $(document).on('click', '.viewLoanBreakdown', function() {
        const loanId = $(this).data('loan-id');
        showBreakdown(loanId, 'loans_breakdown');
    });

    // ===== OTHER BREAKDOWN: View Details =====
    $(document).on('click', '.viewOtherBreakdown', function() {
        const deductId = $(this).data('deduct-id');
        showBreakdown(deductId, 'others_breakdown');
    });

    function showBreakdown(deductId, type) {
        // Use stored period label (format: YYYY-MM-DD_YYYY-MM-DD)
        if (!currentPeriodLabel) {
            Swal.fire('Error', 'Please generate a report first', 'error');
            return;
        }
        
        $.ajax({
            url: 'deduction_registers_handler.php',
            type: 'POST',
            data: {
                action: 'fetch_loan_breakdown',
                loan_id: deductId,
                period: currentPeriodLabel
            },
            dataType: 'json',
            beforeSend: function() {
                $('#Loader').fadeIn();
            },
            success: function(res) {
                $('#Loader').fadeOut();
                
                if (res.status === 'success') {
                    let total = 0;
                    let rows = '';
                    res.data.forEach((emp, idx) => {
                        rows += `
                            <tr>
                                <td>${idx + 1}</td>
                                <td>${emp.full_name || emp.employee_name || emp.employee || 'Unknown'}</td>
                                <td>${(emp.position_title || '').split('(')[0].trim()}</td>
                                <td style="text-align: right;">${money(emp.total_deduction || 0)}</td>
                            </tr>
                        `;
                        total += parseFloat(emp.total_deduction || 0);
                    });
                    
                    $('#loanBreakdownTable tbody').html(rows);
                    $('#loanBreakdownTotal').text(money(total));
                    
                    const title = (res.loan_name || 'Deduction') + ' - Breakdown';
                    $('#loanBreakdownTitle').text(title);
                    $('#loanBreakdownPeriod').text($("#report_year").val());
                    
                    // Update table header based on type
                    const amtHeader = type === 'loans_breakdown' ? 'Loan Amortization' : 'Deduction Amount';
                    $('#loanBreakdownTable thead th:last-child').text(amtHeader);
                    
                    // Store data for export
                    currentLoanBreakdown = { 
                        data: res.data, 
                        loan_name: res.loan_name,
                        loan_id: deductId,
                        type: type
                    };
                    
                    $('#loanBreakdownModal').modal('show');
                } else {
                    Swal.fire('Error', res.message || 'Failed to fetch breakdown', 'error');
                }
            },
            error: function() {
                $('#Loader').fadeOut();
                Swal.fire('Error', 'Failed to fetch breakdown', 'error');
            }
        });
    }

    // ===== LOAN BREAKDOWN EXPORT EXCEL =====
    $(document).on('click', '#exportLoanBreakdownExcel', function() {
        if (!currentLoanBreakdown || !currentLoanBreakdown.data) return;

        // Map data to expected format for breakdown tables
        const mappedData = currentLoanBreakdown.data.map(emp => ({
            employee_name: emp.full_name || emp.employee_name || emp.employee || 'Unknown',
            position_title: emp.position_title || '',
            amount: emp.total_deduction || 0
        }));

        const deptId = $("#department").val() || '';
        const exportUrl = `../prints/export_deduction_registers_excel.php?action=export_breakdown&dept_id=${deptId}&year=${$("#report_year").val()}&period_id=${$("#pay_period").val()}`;

        const form = $('<form>', {
            method: 'POST',
            action: exportUrl,
            target: '_blank'
        }).append(
            $('<input>', { type: 'hidden', name: 'type', value: currentLoanBreakdown.type }),
            $('<input>', { type: 'hidden', name: 'breakdown', value: JSON.stringify(mappedData) }),
            $('<input>', { type: 'hidden', name: 'title', value: currentLoanBreakdown.loan_name }),
            $('<input>', { type: 'hidden', name: 'period', value: currentPeriodLabel })
        );
        form.appendTo('body').submit().remove();
    });

    // ===== LOAN BREAKDOWN EXPORT PDF =====
    $(document).on('click', '#exportLoanBreakdownPDF', function() {
        if (!currentLoanBreakdown || !currentLoanBreakdown.data) return;

        // Map data to expected format for breakdown tables
        const mappedData = currentLoanBreakdown.data.map(emp => ({
            employee_name: emp.full_name || emp.employee_name || emp.employee || 'Unknown',
            position_title: emp.position_title || '',
            amount: emp.total_deduction || 0
        }));

        const deptId = $("#department").val() || '';
        const exportUrl = `../prints/print_deduction_registers.php?dept_id=${deptId}`;

        const form = $('<form>', {
            method: 'POST',
            action: exportUrl,
            target: '_blank'
        }).append(
            $('<input>', { type: 'hidden', name: 'type', value: currentLoanBreakdown.type }),
            $('<input>', { type: 'hidden', name: 'breakdown', value: JSON.stringify(mappedData) }),
            $('<input>', { type: 'hidden', name: 'title', value: currentLoanBreakdown.loan_name }),
            $('<input>', { type: 'hidden', name: 'period', value: currentPeriodLabel }),
            $('<input>', { type: 'hidden', name: 'year', value: $("#report_year").val() }),
            $('<input>', { type: 'hidden', name: 'period_id', value: $("#pay_period").val() })
        );
        form.appendTo('body').submit().remove();
    });

    // ===== LOAN BREAKDOWN PRINT =====
    $(document).on('click', '#printLoanBreakdown', function() {
        const table = $('#loanBreakdownTable').clone();
        const html = `
            <div class="print-header">
                <h2>${$('#loanBreakdownTitle').text()}</h2>
                <p>Period: ${$("#report_year").val()}</p>
            </div>
            ${table.prop('outerHTML')}
        `;
        
        printToWindow(html);
    });

    // ===== DISPLAY ALL DEDUCTION TYPES IN TABS =====
    function displayAllDeductionTypes(data) {
        // Clear all tables
        $('#taxTable, #gsisTable, #gsisEccTable, #philhealthTable, #pagibigTable, #sssTable, #loansTable, #othersTable').empty();
        
        // TAX TABLE
        if (data.tax && data.tax.length > 0) {
            let taxTotal = 0;
            data.tax.forEach((row, idx) => {
                $('#taxTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.employee}</td>
                        <td>${(row.position_title || '').split('(')[0].trim()}</td>
                        <td>${row.tin || 'N/A'}</td>
                        <td class="text-right">${money(row.locked_basic)}</td>
                        <td class="text-right">${money(row.amount)}</td>
                    </tr>
                `);
                taxTotal += parseFloat(row.amount || 0);
            });
            $('.tax-total').text(money(taxTotal));
            $('#tax-count').text((data.tax.length || 0) + ' records');
        } else {
            $('#taxTable').html('<tr><td colspan="6" class="text-center text-muted">No data available</td></tr>');
            $('.tax-total').text('0.00');
            $('#tax-count').text('0 records');
        }
        
        // GSIS TABLE
        if (data.gsis && data.gsis.length > 0) {
            let gsisTotal = 0;
            data.gsis.forEach((row, idx) => {
                $('#gsisTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.employee}</td>
                        <td>${(row.position_title || '').split('(')[0].trim()}</td>
                        <td>${row.gsis_bp || 'N/A'}</td>
                        <td class="text-right">${money(row.locked_basic)}</td>
                        <td class="text-right">${money(row.employee_share)}</td>
                        <td class="text-right">${money(row.employer_share)}</td>
                        <td class="text-right">${money(row.total_amount)}</td>
                    </tr>
                `);
                gsisTotal += parseFloat(row.total_amount || 0);
            });
            $('.gsis-total').text(money(gsisTotal));
            $('#gsis-count').text((data.gsis.length || 0) + ' records');
        } else {
            $('#gsisTable').html('<tr><td colspan="8" class="text-center text-muted">No data available</td></tr>');
            $('.gsis-total').text('0.00');
            $('#gsis-count').text('0 records');
        }
        
        // GSIS ECC TABLE
        if (data.gsis_ecc && data.gsis_ecc.length > 0) {
            let gsisEccTotal = 0;
            data.gsis_ecc.forEach((row, idx) => {
                $('#gsisEccTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.employee}</td>
                        <td>${(row.position_title || '').split('(')[0].trim()}</td>
                        <td>${row.gsis_bp || 'N/A'}</td>
                        <td class="text-right">${money(row.employer_share)}</td>
                    </tr>
                `);
                gsisEccTotal += parseFloat(row.employer_share || 0);
            });
            $('.gsis_ecc-total').text(money(gsisEccTotal));
            $('#gsis_ecc-count').text((data.gsis_ecc.length || 0) + ' records');
        } else {
            $('#gsisEccTable').html('<tr><td colspan="5" class="text-center text-muted">No data available</td></tr>');
            $('.gsis_ecc-total').text('0.00');
            $('#gsis_ecc-count').text('0 records');
        }
        
        // PHILHEALTH TABLE
        if (data.philhealth && data.philhealth.length > 0) {
            let philhealthTotal = 0;
            data.philhealth.forEach((row, idx) => {
                $('#philhealthTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.employee}</td>
                        <td>${(row.position_title || '').split('(')[0].trim()}</td>
                        <td>${row.philhealth_no || 'N/A'}</td>
                        <td class="text-right">${money(row.locked_basic)}</td>
                        <td class="text-right">${money(row.employee_share)}</td>
                        <td class="text-right">${money(row.employer_share)}</td>
                        <td class="text-right">${money(row.total)}</td>
                    </tr>
                `);
                philhealthTotal += parseFloat(row.total || 0);
            });
            $('.philhealth-total').text(money(philhealthTotal));
            $('#philhealth-count').text((data.philhealth.length || 0) + ' records');
        } else {
            $('#philhealthTable').html('<tr><td colspan="8" class="text-center text-muted">No data available</td></tr>');
            $('.philhealth-total').text('0.00');
            $('#philhealth-count').text('0 records');
        }
        
        // PAGIBIG TABLE
        if (data.pagibig && data.pagibig.length > 0) {
            let pagibigTotal = 0;
            data.pagibig.forEach((row, idx) => {
                $('#pagibigTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.employee}</td>
                        <td>${(row.position_title || '').split('(')[0].trim()}</td>
                        <td>${row.mid_no || 'N/A'}</td>
                        <td class="text-right">${money(row.locked_basic)}</td>
                        <td class="text-right">${money(row.employee_share)}</td>
                        <td class="text-right">${money(row.employer_share)}</td>
                        <td class="text-right">${money(row.total)}</td>
                    </tr>
                `);
                pagibigTotal += parseFloat(row.total || 0);
            });
            $('.pagibig-total').text(money(pagibigTotal));
            $('#pagibig-count').text((data.pagibig.length || 0) + ' records');
        } else {
            $('#pagibigTable').html('<tr><td colspan="8" class="text-center text-muted">No data available</td></tr>');
            $('.pagibig-total').text('0.00');
            $('#pagibig-count').text('0 records');
        }
        
        // SSS TABLE
        if (data.sss && data.sss.length > 0) {
            let sssTotal = 0;
            data.sss.forEach((row, idx) => {
                $('#sssTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.employee}</td>
                        <td>${(row.position_title || '').split('(')[0].trim()}</td>
                        <td>${row.sss_no || 'N/A'}</td>
                        <td class="text-right">${money(row.employee_share || row.contribution_amount || 0)}</td>
                    </tr>
                `);
                sssTotal += parseFloat(row.employee_share || row.contribution_amount || 0);
            });
            $('.sss-total').text(money(sssTotal));
            $('#sss-count').text((data.sss.length || 0) + ' records');
        } else {
            $('#sssTable').html('<tr><td colspan="5" class="text-center text-muted">No data available</td></tr>');
            $('.sss-total').text('0.00');
            $('#sss-count').text('0 records');
        }
        
        // LOANS TABLE
        if (data.loans && data.loans.length > 0) {
            let loansTotal = 0;
            data.loans.forEach((row, idx) => {
                const loanId = row.config_deduction_id || row.loan_id;
                $('#loansTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.deduct_code || row.loan_code} - ${row.deduct_title || row.loan_name}</td>
                        <td class="text-right">${money(row.total_loan_amount || row.total_amount || 0)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm viewLoanBreakdown" data-loan-id="${loanId}">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                `);
                loansTotal += parseFloat(row.total_loan_amount || row.total_amount || 0);
            });
            $('.loans-total').text(money(loansTotal));
            $('#loans-count').text((data.loans.length || 0) + ' records');
        } else {
            $('#loansTable').html('<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>');
            $('.loans-total').text('0.00');
            $('#loans-count').text('0 records');
        }
        
        // OTHERS TABLE
        if (data.others && data.others.length > 0) {
            let othersTotal = 0;
            data.others.forEach((row, idx) => {
                const deductId = row.config_deduction_id || row.deduction_id;
                $('#othersTable').append(`
                    <tr>
                        <td>${idx + 1}</td>
                        <td>${row.deduct_code || row.code} - ${row.deduct_title || row.name}</td>
                        <td class="text-right">${money(row.total_amount || row.amount || 0)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm viewOtherBreakdown" data-deduct-id="${deductId}">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                `);
                othersTotal += parseFloat(row.total_amount || row.amount || 0);
            });
            $('.others-total').text(money(othersTotal));
            $('#others-count').text((data.others.length || 0) + ' records');
        } else {
            $('#othersTable').html('<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>');
            $('.others-total').text('0.00');
            $('#others-count').text('0 records');
        }
    }

    // ===== EXPORT FUNCTIONS =====
    function exportDeductionType(type, format) {
        const year = $("#report_year").val();
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val() || '';
        
        if (format === 'excel') {
            // Excel export via GET
            const url = `../prints/export_deduction_registers_excel.php?action=export_type&type=${type}&year=${year}&period_id=${periodId}&dept_id=${deptId}`;
            window.open(url, '_blank');
        } else if (format === 'pdf') {
            // PDF export via POST (period label will be constructed server-side from period_id)
            const form = $('<form>', {
                method: 'POST',
                action: '../prints/print_deduction_registers.php'
            }).append(
                $('<input>', { type: 'hidden', name: 'type', value: type }),
                $('<input>', { type: 'hidden', name: 'year', value: year }),
                $('<input>', { type: 'hidden', name: 'period_id', value: periodId }),
                $('<input>', { type: 'hidden', name: 'dept_id', value: deptId })
            );
            form.appendTo('body').submit().remove();
        }
    }

    function exportAllDeductions(format) {
        const year = $("#report_year").val();
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val() || '';

        if (format === 'excel') {
            // Excel export via GET - export all in one file
            const url = `../prints/export_deduction_registers_excel.php?action=export_all&year=${year}&period_id=${periodId}&dept_id=${deptId}`;
            window.open(url, '_blank');
        } else if (format === 'pdf') {
            // PDF export - loop through all types (period label will be constructed server-side)
            const types = ['tax', 'gsis', 'gsis_ecc', 'philhealth', 'pagibig', 'sss', 'loans', 'others'];
            
            types.forEach((t, index) => {
                // Slight delay between requests to avoid overwhelming
                setTimeout(() => {
                    const form = $('<form>', {
                        method: 'POST',
                        action: '../prints/print_deduction_registers.php'
                    }).append(
                        $('<input>', { type: 'hidden', name: 'type', value: t }),
                        $('<input>', { type: 'hidden', name: 'year', value: year }),
                        $('<input>', { type: 'hidden', name: 'period_id', value: periodId }),
                        $('<input>', { type: 'hidden', name: 'dept_id', value: deptId })
                    );
                    form.appendTo('body').submit().remove();
                }, index * 500); // 500ms delay between each export
            });
        }
    }

    // ===== PRINT FUNCTIONS =====
    function printDeductionType(type) {
        const typeTitle = type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' ');
        const printContent = `
            <div class="print-header">
                <h2>Deduction Register - ${typeTitle}</h2>
                <p>Period: ${$("#report_year").val()} | Department: ${$("#department option:selected").text()}</p>
            </div>
            ${$(`#tab_${type} table`).clone().prop('outerHTML')}
        `;
        printToWindow(printContent);
    }

    function printAllDeductions() {
        let printContent = `
            <div class="print-header">
                <h2>Complete Deduction Register Report</h2>
                <p>Period: ${$("#report_year").val()} | Department: ${$("#department option:selected").text()}</p>
                <p>Report Generated: ${new Date().toLocaleString()}</p>
            </div>
        `;

        const types = ['tax', 'gsis', 'gsis_ecc', 'philhealth', 'pagibig', 'sss', 'loans', 'others'];
        types.forEach(type => {
            const $table = $(`#tab_${type} table`);
            if ($table.find('tbody tr').length > 0 && $table.find('tbody tr:first td:first').text() !== '') {
                printContent += `<h3 style="margin-top: 40px; page-break-before: always;">${type.toUpperCase()}</h3>`;
                printContent += $table.clone().prop('outerHTML');
            }
        });

        printToWindow(printContent);
    }

    function printToWindow(html) {
        const printWindow = window.open('', '', 'height=600,width=900');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Deduction Register Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 12px; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { border: 1px solid #333; padding: 6px; text-align: left; font-size: 11px; }
                        th { background-color: #f0f0f0; font-weight: bold; }
                        .print-header { margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
                        .text-right { text-align: right; }
                        .fw-bold { font-weight: bold; }
                        .table-active { background-color: #f9f9f9; }
                        @media print {
                            body { margin: 0; padding: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${html}
                </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => printWindow.print(), 250);
    }

    // ===== UTILITY FUNCTION =====
    function money(val) {
        return parseFloat(val || 0).toLocaleString(undefined, { 
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        });
    }
});
