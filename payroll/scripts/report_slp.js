$(document).ready(function() {
    // ============================================================
    // SLP Report Frontend Logic
    // ============================================================

    const table = $('#slpTable').DataTable({
        language: {
            emptyTable: "No data available in table"
        },
        responsive: true,
        pageLength: -1,
        lengthChange: false,
        dom: 'Bfrtip',
        buttons: [], // We use our own buttons above the card
        order: [[3, 'asc'], [1, 'asc']], // Sort by Dept, then Name
        rowGroup: {
            dataSrc: 3 // Group by Department column
        },
        columnDefs: [
            { targets: [3], visible: false } // Hide dept column as it's used for grouping
        ],
        drawCallback: function(settings) {
            const api = this.api();
            const rows = api.rows({ page: 'current' }).nodes();
            let last = null;
            const deptId = $("#department").val();

            if (deptId !== 'all') {
                api.column(3, { page: 'current' }).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group text-primary font-weight-bold" style="background-color: #f8f9fa;"><td colspan="6">' + group + '</td></tr>'
                        );
                        last = group;
                    }
                });
            }
        }
    });

    // Year Change -> Fetch Periods
    $("#slp_year").on("change", function() {
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

    // Generate Button
    $("#generateSlpBtn").on("click", function() {
        const year = $("#slp_year").val();
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val();
        const empType = $("#employment_type").val();

        if (!year || !periodId) {
            Swal.fire('Required Fields', 'Please select Year and Pay Period.', 'warning');
            return;
        }

        $.ajax({
            url: 'slp_handler.php',
            type: 'POST',
            data: {
                action: 'fetch_slp',
                period_id: periodId,
                dept_id: deptId,
                employment_type: empType
            },
            dataType: 'json',
            beforeSend: function() {
                $('#Loader').fadeIn();
                $("#generateSlpBtn").prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
                table.clear().draw();
            },
            success: function(res) {
                $('#Loader').fadeOut();
                $("#generateSlpBtn").prop('disabled', false).html('<i class="fas fa-cogs"></i> Generate Report');

                if (res.status === 'success' && res.data.length > 0) {
                    const formatted = res.data.map((row, idx) => [
                        idx + 1,
                        row.full_name,
                        row.position_title,
                        row.dept_title,
                        money(row.gross),
                        money(row.total_deductions),
                        money(row.net_pay)
                    ]);
                    if (deptId === 'all') {
                        table.order([[1, 'asc']]).column(3).visible(true);
                    } else {
                        table.order([[3, 'asc'], [1, 'asc']]).column(3).visible(false);
                    }
                    
                    table.rows.add(formatted).draw();

                    // Update Grand Totals
                    $("#grand_gross").text(money(res.totals.gross));
                    $("#grand_deductions").text(money(res.totals.deductions));
                    $("#grand_net").text(money(res.totals.net));

                    $("#printSlpBtn, #exportExcelBtn").prop('disabled', false);
                } else {
                    Swal.fire('No Data', res.message || 'No payroll records found for this selection.', 'info');
                    $("#printSlpBtn, #exportExcelBtn").prop('disabled', true);
                    resetGrandTotals();
                }
            },
            error: function() {
                $('#Loader').fadeOut();
                $("#generateSlpBtn").prop('disabled', false).html('<i class="fas fa-cogs"></i> Generate Report');
                Swal.fire('Error', 'Failed to fetch SLP data.', 'error');
            }
        });
    });

    // Print PDF
    $("#printSlpBtn").on("click", function() {
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val();
        const empType = $("#employment_type").val();
        
        window.open(`../prints/print_slp.php?period_id=${periodId}&dept_id=${deptId}&employment_type=${empType}`, '_blank');
    });

    // Export Excel
    $("#exportExcelBtn").on("click", function() {
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val();
        const empType = $("#employment_type").val();
        
        window.location.href = `../prints/export_slp_excel.php?period_id=${periodId}&dept_id=${deptId}&employment_type=${empType}`;
    });

    function resetGrandTotals() {
        $("#grand_gross, #grand_deductions, #grand_net").text('0.00');
    }

    function money(val) {
        return parseFloat(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
});
