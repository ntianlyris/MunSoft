$(document).ready(function() {
    // ============================================================
    // Internal Deduction Registers Report Frontend Logic
    // ============================================================

let table = null;

function initDataTable(deptId, isSummary) {
    if ($.fn.DataTable.isDataTable('#deductionTable')) {
        $('#deductionTable').DataTable().destroy();
        $('#deductionTable').empty(); // Clear everything including headers
    }

    const isAllDept = deptId === 'all' && !isSummary;
    
    table = $('#deductionTable').DataTable({
        language: { emptyTable: "No data available in table" },
        responsive: true,
        pageLength: -1,
        lengthChange: false,
        dom: 'Bfrtip',
        buttons: ["excel", "pdf", "print"],
        order: isSummary ? [[0, 'asc']] : [[2, 'asc'], [1, 'asc']], // Order by first col for summary
        rowGroup: {
            dataSrc: 2,
            enable: isAllDept
        },
        columnDefs: [
            { targets: [2], visible: isSummary ? true : !isAllDept } // Always show col 2 (deduct title) for summary
        ]
    });
}

    // Year Change -> Fetch Periods
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
                            $dropdown.append($("<option>", { value: p.payroll_period_id, text: p.period_label }));
                        });
                    }
                }
            });
        }
    });

    // Deduction Type or Category Change -> Fetch Specific Deductions
    $("#deduction_type, #deduction_category").on("change", function() {
        const typeId = $("#deduction_type").val();
        const typeText = $("#deduction_type option:selected").text().toUpperCase();
        const category = $("#deduction_category").val();
        
        // Logical Toggle for Category
        if (typeText.includes("OTHERS")) {
            $("#deduction_category").prop("disabled", false);
        } else {
            $("#deduction_category").prop("disabled", true).val("");
        }

        // Fetch Specific Deductions if Type is set
        if (typeId) {
            $.ajax({
                url: "deduction_registers_handler.php",
                type: "POST",
                data: { 
                    action: 'fetch_specific_deductions', 
                    type_id: typeId,
                    category: category
                },
                dataType: "json",
                success: function(res) {
                    const $specific = $("#specific_deduction");
                    const $col = $("#specific_deduction_col");
                    
                    $specific.empty().append("<option value=''>All Specific Deductions</option>");
                    
                    if (res.status === 'success' && res.data.length > 0) {
                        $col.fadeIn();
                        $.each(res.data, function(i, d) {
                            $specific.append($("<option>", { 
                                value: d.config_deduction_id, 
                                text: `[${d.deduct_code}] ${d.deduct_title}` 
                            }));
                        });
                    } else {
                        $col.fadeOut();
                    }
                }
            });
        } else {
            $("#specific_deduction_col").fadeOut();
        }
    });

    // Generate Button
    $("#generateReportBtn").on("click", function() {
        const year = $("#report_year").val();
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val();
        const typeId = $("#deduction_type").val();
        const typeName = $("#deduction_type option:selected").text();
        const category = $("#deduction_category").val();
        const specificId = $("#specific_deduction").val();

        if (!year || !periodId || !typeId) {
            Swal.fire('Required Fields', 'Please select Year, Pay Period, and Deduction Type.', 'warning');
            return;
        }

        $.ajax({
            url: 'deduction_registers_handler.php',
            type: 'POST',
            data: {
                action: 'fetch_deduction_register',
                year: year,
                period_id: periodId,
                dept_id: deptId,
                type_id: typeId,
                type_name: typeName,
                category: category,
                specific_id: specificId
            },
            dataType: 'json',
            beforeSend: function() {
                $('#Loader').fadeIn();
                $("#generateReportBtn").prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
                if (table) table.clear().draw();
            },
            success: function(res) {
                $('#Loader').fadeOut();
                $("#generateReportBtn").prop('disabled', false).html('<i class="fas fa-cogs"></i> Generate Report');

                if (res.status === 'success' && res.data.length > 0) {
                    processReportData(res.data, typeName, deptId);
                    $("#resultsCard").fadeIn();
                } else {
                    Swal.fire('No Data', res.message || 'No records found for this selection.', 'info');
                    $("#resultsCard").fadeOut();
                }
            },
            error: function() {
                $('#Loader').fadeOut();
                $("#generateReportBtn").prop('disabled', false).html('<i class="fas fa-cogs"></i> Generate Report');
                Swal.fire('Error', 'Failed to fetch report data.', 'error');
            }
        });
    });

    function processReportData(data, typeName, deptId) {
        // 0. Detect if this is a Summary (no employee mapping)
        const isSummary = data.length > 0 && data[0].full_name === null;

        // Dynamic Column Handling
        const name = typeName.toUpperCase();
        let headers = [];
        let columns = [];
        let totalVal = 0;

        if (isSummary) {
            headers = ['#', 'Deduction Code', 'Deduction Title', 'Total Amount'];
            columns = data.map((r, i) => [
                i + 1, 
                r.deduct_code || 'N/A', 
                r.deduct_title || typeName, 
                money(r.total_amount || r.total_loan_amount || 0)
            ]);
            totalVal = data.reduce((sum, r) => sum + parseFloat(r.total_amount || r.total_loan_amount || 0), 0);
        } else if (name.includes('PHILHEALTH')) {
            headers = ['#', 'Employee Name', 'Department', 'Position', 'EE Share', 'ER Share', 'Total'];
            columns = data.map((r, i) => [i+1, r.full_name, r.dept_title, r.position_title, money(r.ee_share), money(r.er_share), money(r.total_phic)]);
            totalVal = data.reduce((sum, r) => sum + parseFloat(r.total_phic), 0);
        } else if (name.includes('BIR') || name.includes('TAX')) {
            headers = ['#', 'Employee Name', 'Department', 'Position', 'Gross Earned', 'Tax Deducted'];
            columns = data.map((r, i) => [i+1, r.full_name, r.dept_title, r.position_title, money(r.gross), money(r.tax_deducted)]);
            totalVal = data.reduce((sum, r) => sum + parseFloat(r.tax_deducted), 0);
        } else if (name.includes('GSIS')) {
            headers = ['#', 'Employee Name', 'Department', 'Position', 'Personal', 'Gov\'t', 'Total'];
            columns = data.map((r, i) => [i+1, r.full_name, r.dept_title, r.position_title, money(r.gsis_personal), money(r.gsis_govt), money(r.total_gsis)]);
            totalVal = data.reduce((sum, r) => sum + parseFloat(r.total_gsis), 0);
        } else if (name.includes('PAGIBIG') || name.includes('PAG-IBIG')) {
            headers = ['#', 'Employee Name', 'Department', 'Position', 'EE Share', 'ER Share', 'Total'];
            columns = data.map((r, i) => [i+1, r.full_name, r.dept_title, r.position_title, money(r.ee_share), money(r.er_share), money(r.total_pagibig)]);
            totalVal = data.reduce((sum, r) => sum + parseFloat(r.total_pagibig), 0);
        } else {
            headers = ['#', 'Employee Name', 'Department', 'Position', 'Deduction Name', 'Amount'];
            columns = data.map((r, i) => [i+1, r.full_name, r.dept_title, r.position_title, r.deduct_title || typeName, money(r.deduction_amount || 0)]);
            totalVal = data.reduce((sum, r) => sum + parseFloat(r.deduction_amount || 0), 0);
        }

        // 1. Destroy old table and clear headers
        if ($.fn.DataTable.isDataTable('#deductionTable')) {
            table.destroy();
            $('#deductionTable').empty();
        }

        // 2. Rebuild the table structure
        let headHtml = '<thead><tr class="text-center table-light">';
        headers.forEach(h => headHtml += `<th>${h}</th>`);
        headHtml += '</tr></thead><tbody></tbody>';
        $('#deductionTable').html(headHtml);

        // 3. Re-init DataTable with new configuration
        initDataTable(deptId, isSummary);

        // 4. Add the data
        table.rows.add(columns).draw();
        
        $("#grand_total").text(money(totalVal));
    }

    function money(val) {
        return parseFloat(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
});
