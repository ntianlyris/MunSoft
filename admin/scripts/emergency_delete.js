$(function () {
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    let payrollTable = $("#payrollTable").DataTable({
        "pageLength": 25,
        "responsive": true,
        "autoWidth": false
    });

    $('#filterForm').on('submit', function (e) {
        e.preventDefault();
        
        let formData = {
            action: 'fetch_payroll_data',
            payroll_period_id: $('#payroll_period_id').val(),
            department_id: $('#dept_id').val(),
            employment_type: $('#emp_type_stamp').val()
        };

        $.ajax({
            url: 'emergency_payroll_handler.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (data) {
                payrollTable.clear();
                if (data.length > 0) {
                    $('#resultsCard').show();
                    $('#btnDeleteAll').show();
                    
                    data.forEach((row, index) => {
                        let statusBadge = '';
                        switch(row.status) {
                            case 'DRAFT': statusBadge = '<span class="badge badge-info">DRAFT</span>'; break;
                            case 'REVIEW': statusBadge = '<span class="badge badge-warning">REVIEW</span>'; break;
                            case 'APPROVED': statusBadge = '<span class="badge badge-success">APPROVED</span>'; break;
                            case 'PAID': statusBadge = '<span class="badge badge-primary">PAID</span>'; break;
                            default: statusBadge = '<span class="badge badge-secondary">' + row.status + '</span>';
                        }

                        payrollTable.row.add([
                            index + 1,
                            row.id_num,
                            row.full_name,
                            row.position_title,
                            parseFloat(row.gross).toLocaleString(undefined, {minimumFractionDigits: 2}),
                            parseFloat(row.total_deductions).toLocaleString(undefined, {minimumFractionDigits: 2}),
                            parseFloat(row.net_pay).toLocaleString(undefined, {minimumFractionDigits: 2}),
                            statusBadge
                        ]);
                    });
                    payrollTable.draw();
                } else {
                    $('#resultsCard').show();
                    $('#btnDeleteAll').hide();
                    payrollTable.draw();
                    Swal.fire('Info', 'No records found for the selection.', 'info');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch payroll data.', 'error');
            }
        });
    });

    $('#btnDeleteAll').on('click', function () {
        let dept = $('#dept_id').find('option:selected').text();
        let period = $('#payroll_period_id').find('option:selected').text();
        let empType = $('#emp_type_stamp').val();

        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to delete <b>ALL</b> matching payroll records for:<br>
                   <b>Dept:</b> ${dept}<br>
                   <b>Period:</b> ${period}<br>
                   <b>Type:</b> ${empType}<br><br>
                   <span class="text-danger"><b>WARNING:</b> This now ignores status and will delete even PAID or APPROVED records!</span><br><br>
                   This action is logged and cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, DELETE ALL!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'emergency_payroll_handler.php',
                    type: 'POST',
                    data: {
                        action: 'delete_payroll_records',
                        payroll_period_id: $('#payroll_period_id').val(),
                        dept_id: $('#dept_id').val(),
                        emp_type_stamp: empType
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                            $('#filterForm').submit(); // Refresh table
                        } else {
                            Swal.fire(
                                'Warning',
                                response.message,
                                'warning'
                            );
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Deletion failed.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
