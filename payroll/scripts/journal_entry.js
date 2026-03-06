$(document).ready(function() {
    // ============================================================
    // Consolidation Toggle Handler
    // ============================================================
    $("input[name='consolidation_type']").on('change', function() {
        const isConsolidated = $("#consolidation_all").is(':checked');
        const deptField = $("#department");
        const deptLabel = $("#dept_required");
        
        if (isConsolidated) {
            // All Departments Mode
            deptField.prop('disabled', true).prop('required', false).val('');
            deptLabel.hide();
            deptField.closest('.form-group').find('label').css('color', '#6c757d');
        } else {
            // Single Department Mode
            deptField.prop('disabled', false).prop('required', true);
            deptLabel.show();
            deptField.closest('.form-group').find('label').css('color', 'inherit');
        }
        
        // Reset table when mode changes
        $('#journalTable tbody').empty();
        $('#printJournalBtn').prop('disabled', true);
        $('#summary_department, #summary_period, #summary_year').val('');
    });

    // Auto-switch to Single Department when dept is selected
    $("#department").on('change', function() {
        if ($(this).val()) {
            $("#consolidation_single").prop('checked', true).change();
        }
    });

    // ============================================================
    // Year Dropdown - Load Pay Periods
    // ============================================================
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
                    $periodDropdown.empty();
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error fetching payroll periods. Please try again.'
                    });
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

    // Handle consolidation toggle - disable/enable department selector
    $("input[name='consolidation_type']").on('change', function() {
        const isConsolidated = $("#consolidation_all").is(':checked');
        $("#department").prop('disabled', isConsolidated).prop('required', !isConsolidated);
        
        if (isConsolidated) {
            $("#department").val('').change();
        }
    });

    // Handle department change - disable consolidation toggle when dept selected
    $("#department").on('change', function() {
        if ($(this).val()) {
            $("#consolidation_single").prop('checked', true).change();
        }
    });

// Generate button click
    $("#generateJournalBtn").click(function() {
        const isConsolidated = $("#consolidation_all").is(':checked');
        
        const data = {
            action: 'generate_journal',
            year: $("#journal_year").val(),
            period_id: $("#pay_period").val(),
            employment_type: $("#employment_type").val(),
            consolidate_all: isConsolidated ? 1 : 0
        };

        // Only require dept_id if not consolidated
        if (!isConsolidated) {
            data.dept_id = $("#department").val();
        }

        if (!data.year || !data.period_id || !data.employment_type) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Fields',
                text: 'Please fill all required fields.'
            });
            return;
        }
        
        if (!isConsolidated && !data.dept_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Department Required',
                text: 'Please select a department or choose "All Depts".'
            });
            return;
        }

        // Populate the summary fields when Generate is clicked
        if (isConsolidated) {
            $("#summary_department").val("All Departments");
        } else {
            $("#summary_department").val($("#department option:selected").text() || "");
        }
        $("#summary_period").val($("#pay_period option:selected").text() || "");
        $("#summary_year").val($("#journal_year").val() || "");

        $.ajax({
            url: 'journal_handler.php',
            type: 'GET',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $('#Loader').fadeIn();
                $("#generateJournalBtn")
                    .html('<i class="fas fa-spinner fa-spin"></i> Generating...')
                    .prop('disabled', true);
                table.clear().draw();
            },
            success: function(response) {
                $('#Loader').fadeOut();
                $("#generateJournalBtn")
                    .html('<i class="fas fa-cogs"></i> Generate')
                    .prop('disabled', false);

                if (response.status === 'success' && response.data && response.data.length > 0) {
                    var formattedData = response.data.map(function(entry) {
                        return {
                            acct_code: entry.acct_code,
                            description: entry.description,
                            debit: entry.debit_amt > 0 ? Number(entry.debit_amt).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '',
                            credit: entry.credit_amt > 0 ? Number(entry.credit_amt).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : ''
                        };
                    });

                    table.clear().rows.add(formattedData).draw();

                    var totalDebit = response.data.reduce((sum, entry) => sum + (parseFloat(entry.debit_amt) || 0), 0);
                    var totalCredit = response.data.reduce((sum, entry) => sum + (parseFloat(entry.credit_amt) || 0), 0);

                    $("#total_debit").text(Number(totalDebit).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $("#total_credit").text(Number(totalCredit).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Generated',
                        text: 'Journal entries generated successfully!',
                        timer: 1500,
                        timerProgressBar: true
                    });
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
                $('#Loader').fadeOut();
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
    const isConsolidated = $("#consolidation_all").is(':checked');
    var year = $('#journal_year').val();
    var period_id = $('#pay_period').val();
    var employment_type = $('#employment_type').val();
    
    if (!year || !period_id || !employment_type) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please fill all required fields before printing.'
        });
        return;
    }
    
    if (!isConsolidated) {
        var dept_id = $('#department').val();
        if (!dept_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Department Required',
                text: 'Please select a department or choose "All Depts".'
            });
            return;
        }
        var url = '../prints/print_journal.php?year=' + encodeURIComponent(year) +
                  '&period_id=' + encodeURIComponent(period_id) +
                  '&dept_id=' + encodeURIComponent(dept_id) +
                  '&employment_type=' + encodeURIComponent(employment_type) +
                  '&consolidate_all=0';
    } else {
        var url = '../prints/print_journal.php?year=' + encodeURIComponent(year) +
                  '&period_id=' + encodeURIComponent(period_id) +
                  '&employment_type=' + encodeURIComponent(employment_type) +
                  '&consolidate_all=1';
    }
    
    $('#Loader').fadeIn();
    setTimeout(function() {
        $('#Loader').fadeOut();
    }, 500);
    
    window.open(url, '_blank');
});



