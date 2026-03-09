$(document).ready(function() {
    // Year Change -> Fetch Periods
    $("#abs_year").on("change", function() {
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

    $("#generateAbstractBtn").on("click", function() {
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val();
        const empType = $("#employment_type").val();

        if (!periodId) {
            Swal.fire('Required', 'Please select a pay period.', 'warning');
            return;
        }

        $.ajax({
            url: 'abstract_handler.php',
            type: 'POST',
            data: {
                action: 'fetch_abstract',
                period_id: periodId,
                dept_id: deptId,
                employment_type: empType
            },
            dataType: 'json',
            beforeSend: function() {
                $('#Loader').fadeIn();
                $("#generateAbstractBtn").prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
                $("#abstractResultsContainer").empty();
            },
            success: function(res) {
                $('#Loader').fadeOut();
                $("#generateAbstractBtn").prop('disabled', false).html('<i class="fas fa-sync"></i> Generate');

                if (res.status === 'success') {
                    renderAbstractTable(res.data);
                    $("#printAbstractBtn").prop('disabled', false);
                } else {
                    Swal.fire('No Data', res.message, 'info');
                    $("#printAbstractBtn").prop('disabled', true);
                    $("#abstractResultsContainer").html('<div class="text-center text-muted p-5"><p>No data found for this selection.</p></div>');
                }
            },
            error: function() {
                $('#Loader').fadeOut();
                $("#generateAbstractBtn").prop('disabled', false).html('<i class="fas fa-sync"></i> Generate');
                Swal.fire('Error', 'Failed to generate abstract.', 'error');
            }
        });
    });

    function renderAbstractTable(data) {
        let html = `<div class="row justify-content-center">
                        <div class="col-md-10">
                            <table class="table table-bordered table-sm table-hover">
                                <thead class="text-center bg-light">
                                    <tr>
                                        <th width="50%">Particulars</th>
                                        <th width="20%">Account Code</th>
                                        <th width="30%">Consolidated Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-primary text-white"><td colspan="3"><b>I. EARNINGS</b></td></tr>`;

        // Earnings
        data.earnings.forEach(e => {
            html += `<tr>
                        <td>${e.title}</td>
                        <td class="text-center">${e.acct}</td>
                        <td class="text-right font-weight-bold">${money(e.amount)}</td>
                    </tr>`;
        });
        html += `<tr class="bg-info text-white font-weight-bold">
                    <td colspan="2" class="text-right">TOTAL GROSS PAY:</td>
                    <td class="text-right">${money(data.totals.gross)}</td>
                 </tr>`;

        // Deductions
        html += `<tr class="bg-danger text-white"><td colspan="3"><b>II. DEDUCTIONS</b></td></tr>`;
        data.deductions.forEach(d => {
            html += `<tr>
                        <td>${d.title}</td>
                        <td class="text-center">${d.acct}</td>
                        <td class="text-right font-weight-bold">${money(d.amount)}</td>
                    </tr>`;
        });
        html += `<tr class="bg-warning text-dark font-weight-bold">
                    <td colspan="2" class="text-right">TOTAL DEDUCTIONS:</td>
                    <td class="text-right">${money(data.totals.deductions)}</td>
                 </tr>`;

        // Net Pay Row
        html += `<tr class="bg-dark text-white font-weight-bold" style="font-size: 1.1rem;">
                    <td colspan="2" class="text-right">TOTAL NET PAY:</td>
                    <td class="text-right">${money(data.totals.net)}</td>
                 </tr>`;

        // GovShares
        html += `<tr class="bg-success text-white"><td colspan="3"><b>III. GOV'T SHARES (Employer)</b></td></tr>`;
        data.govshares.forEach(g => {
            html += `<tr>
                        <td>${g.title}</td>
                        <td class="text-center">${g.acct}</td>
                        <td class="text-right font-weight-bold">${money(g.amount)}</td>
                    </tr>`;
        });
        html += `<tr class="bg-success text-white font-weight-bold">
                    <td colspan="2" class="text-right">TOTAL GOV'T SHARES:</td>
                    <td class="text-right">${money(data.totals.govshares)}</td>
                 </tr>`;

        html += `       </tbody>
                            </table>
                        </div>
                    </div>`;

        $("#abstractResultsContainer").html(html);
    }

    $("#printAbstractBtn").on("click", function() {
        const periodId = $("#pay_period").val();
        const deptId = $("#department").val();
        const empType = $("#employment_type").val();
        
        window.open(`../prints/print_abstract_payroll.php?period_id=${periodId}&dept_id=${deptId}&employment_type=${empType}`, '_blank');
    });

    function money(val) {
        return parseFloat(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
});
