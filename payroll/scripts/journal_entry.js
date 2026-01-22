$(document).ready(function() {
    $("#journal_year").on("change", function() {
        let selectedYear = $(this).val();
        var action = 'fetch_payroll_periods_of_year';

        if (selectedYear) {
            $.ajax({
                url: "payroll_handler.php",
                type: "POST",
                data: { year: selectedYear, action: action },
                dataType: "json",
                success: function(response) {
                    let $periodDropdown = $("#pay_period");
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
            $("#pay_period").html("<option value=''>Select Pay Period...</option>");
        }
    });
});

$(function () {
    // Initialize DataTable with custom "no data" message
    const table = $('#journalTable').DataTable({
        language: {
            emptyTable: "No journal entries available"
        },
        columns: [
            { 
                data: null,
                title: '#',
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Display the row number (1-based index)
                }
            },
            { 
                data: 'description',
                title: 'Accounts & Explanation'
            },
            { 
                data: 'acct_code',
                title: 'Account Code'
            },
            { 
                data: 'debit',
                title: 'Debit',
                className: 'text-right',
                render: function(data, type, row) {
                    if (type === 'display' && data) {
                        return Number(data.toString().replace(/,/g, '')).toLocaleString(undefined, { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        });
                    }
                    return data;
                }
            },
            { 
                data: 'credit',
                title: 'Credit',
                className: 'text-right',
                render: function(data, type, row) {
                    if (type === 'display' && data) {
                        return Number(data.toString().replace(/,/g, '')).toLocaleString(undefined, { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        });
                    }
                    return data;
                }
            }
        ],
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        pageLength: -1, // Remove pagination limit
        footerCallback: function(row, data, start, end, display) {
            const api = this.api();

            // Calculate totals for debit and credit columns
            const debitTotal = api.column(3).data()
                .reduce((acc, val) => acc + (parseFloat((val || '').toString().replace(/,/g, '')) || 0), 0);
            const creditTotal = api.column(4).data()
                .reduce((acc, val) => acc + (parseFloat((val || '').toString().replace(/,/g, '')) || 0), 0);

            // Update footer cells with formatted numbers
            $(api.column(3).footer()).html(Number(debitTotal).toLocaleString(undefined, { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            }));
            $(api.column(4).footer()).html(Number(creditTotal).toLocaleString(undefined, { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            }));

            // Display totals in the footer
            $(api.column(0).footer()).html('Total:');
        },
        dom: 'frtip'
    });

// Generate button click
    $("#generateJournalBtn").click(function() {
        const data = {
            action: 'generate_journal',
            year: $("#journal_year").val(),
            period_id: $("#pay_period").val(),
            dept_id: $("#department").val(),
            employment_type: $("#employment_type").val()
        };

        if (!data.year || !data.period_id || !data.dept_id || !data.employment_type) {
            alert("Please fill all required fields.");
            return;
        }

        // Populate the summary fields when Generate is clicked
        $("#summary_department").val($("#department option:selected").text() || "");
        $("#summary_period").val($("#pay_period option:selected").text() || "");
        $("#summary_year").val($("#journal_year").val() || "");
  
        // Optionally clear on change of selects
        $("#department, #pay_period, #journal_year").on("change", function(){
            if (!$("#department").val() && !$("#pay_period").val() && !$("#journal_year").val()) {
                $("#summary_department, #summary_period, #summary_year").val("");
            }
        });

        $.ajax({
            url: 'journal_handler.php',
            type: 'GET',
            data: {
            action: 'generate_journal',
            year: $("#journal_year").val(),
            period_id: $("#pay_period").val(),
            dept_id: $("#department").val(),
            employment_type: $("#employment_type").val()
            },
            dataType: 'json',
            beforeSend: function() {
            $("#generateJournalBtn")
                .html('<i class="fas fa-spinner fa-spin"></i> Generating...')
                .prop('disabled', true);
            table.clear().draw();
            },
            success: function(response) {
            $("#generateJournalBtn")
                .html('<i class="fas fa-cogs"></i> Generate')
                .prop('disabled', false);

            if (response.status === 'success' && response.data && response.data.length > 0) {
                // Format the data for DataTable
                var formattedData = response.data.map(function(entry) {
                return {
                    acct_code: entry.acct_code,
                    description: entry.description,
                    debit: entry.debit_amt > 0 ? Number(entry.debit_amt).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '',
                    credit: entry.credit_amt > 0 ? Number(entry.credit_amt).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : ''
                };
                });

                table.clear().rows.add(formattedData).draw();

                // Calculate and display totals
                var totalDebit = response.data.reduce((sum, entry) => sum + (parseFloat(entry.debit_amt) || 0), 0);
                var totalCredit = response.data.reduce((sum, entry) => sum + (parseFloat(entry.credit_amt) || 0), 0);

                // Update totals display with consistent formatting
                $("#total_debit").text(Number(totalDebit).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $("#total_credit").text(Number(totalCredit).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                
            } else {
                table.clear().draw();
                if (response.message) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Data',
                    text: response.message
                });
                }
            }
            },
            error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error generating journal entries: ' + error
            });
            $("#generateJournalBtn")
                .html('<i class="fas fa-cogs"></i> Generate')
                .prop('disabled', false);
            table.clear().draw();
            }
        });
    });

    // enable print button when table has rows (and keep it updated)
    function togglePrintBtn() {
        var hasRows = table.data().count() > 0; // reliable: counts actual data rows
        $('#printJournalBtn').prop('disabled', !hasRows);
    }

    togglePrintBtn();
    table.on('draw', togglePrintBtn);

    // watch for changes to the table body (rows added/removed)
    var tbody = document.querySelector('#journalTable tbody');
    if (tbody) {
        var mo = new MutationObserver(togglePrintBtn);
        mo.observe(tbody, { childList: true, subtree: true });
    }

});

$('#printJournalBtn').on('click', function() {
    var year = $('#journal_year').val();
    var period_id = $('#pay_period').val();
    var dept_id = $('#department').val();
    var employment_type = $('#employment_type').val();
    if (!year || !period_id || !dept_id || !employment_type) {
        alert('Please select Year, Pay Period, Department, and Employment Type.');
        return;
    }
    var url = '../prints/print_journal.php?year=' + encodeURIComponent(year) +
              '&period_id=' + encodeURIComponent(period_id) +
              '&dept_id=' + encodeURIComponent(dept_id) +
              '&employment_type=' + encodeURIComponent(employment_type);
    window.open(url, '_blank');
});



