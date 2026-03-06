/**
 * BULK SELECTION AND BULK ACTIONS MANAGEMENT
 * Handles checkbox selection, status validation, and bulk workflow transitions
 * Date: March 6, 2026
 */

// Track selected payroll IDs and their statuses
let selectedPayrolls = {};

/**
 * Check if all payroll entries in the table are APPROVED or PAID status
 * If all are APPROVED or PAID, enable Print and Export buttons
 * Otherwise, disable them
 */
function checkPrintExportButtonStatus() {
    // Get all data rows from the table
    const tableRows = $('#payrollTable tbody tr');
    
    if (tableRows.length === 0) {
        // No data - disable buttons
        $('#printPayrollBtn').addClass('disabled').prop('disabled', true);
        $('#exportPayrollExcelBtn').addClass('disabled').prop('disabled', true);
        return;
    }
    
    // Check if all rows have APPROVED or PAID status
    let allApprovedOrPaid = true;
    tableRows.each(function() {
        const statusAttr = $(this).data('payroll-status');
        if (statusAttr !== 'APPROVED' && statusAttr !== 'PAID') {
            allApprovedOrPaid = false;
            return false; // Break the loop
        }
    });
    
    // Enable or disable buttons based on status check
    if (allApprovedOrPaid) {
        $('#printPayrollBtn').removeClass('disabled').prop('disabled', false);
        $('#exportPayrollExcelBtn').removeClass('disabled').prop('disabled', false);
    } else {
        $('#printPayrollBtn').addClass('disabled').prop('disabled', true);
        $('#exportPayrollExcelBtn').addClass('disabled').prop('disabled', true);
    }
}

/**
 * Initialize bulk selection event handlers
 * Must be called after table is rendered
 */
function initializeBulkSelection() {
    // Select All checkbox handler
    $(document).on('change', '#selectAllCheckbox', function() {
        const isChecked = $(this).is(':checked');
        
        $('.payrollCheckbox').each(function() {
            $(this).prop('checked', isChecked);
        });
        
        updateBulkSelectionUI();
    });
    
    // Individual checkbox handler
    $(document).on('change', '.payrollCheckbox', function() {
        updateBulkSelectionUI();
    });
}

/**
 * Update the bulk selection UI based on checked items
 * Enables/disables bulk action buttons based on selection and status
 */
function updateBulkSelectionUI() {
    // Get all checked checkboxes
    const checkedBoxes = $('.payrollCheckbox:checked');
    const count = checkedBoxes.length;
    
    // Update selection counter
    $('#bulkSelectionCount').text(count);
    
    // Show/hide bulk actions panel
    if (count > 0) {
        $('#bulkActionsPanel').removeClass('d-none');
    } else {
        $('#bulkActionsPanel').addClass('d-none');
        // Hide all bulk buttons
        $('#bulkSubmitBtn').addClass('d-none');
        $('#bulkApproveBtn').addClass('d-none');
        $('#bulkReturnBtn').addClass('d-none');
        $('#bulkMarkPaidBtn').addClass('d-none');
        return;
    }
    
    // Get statuses of all selected items
    const statuses = {};
    checkedBoxes.each(function() {
        const status = $(this).data('status') || 'DRAFT';
        statuses[status] = (statuses[status] || 0) + 1;
    });
    
    // Determine if all selected are same status
    const uniqueStatuses = Object.keys(statuses);
    const allSameStatus = uniqueStatuses.length === 1;
    
    if (!allSameStatus) {
        // Mixed statuses - disable all bulk actions
        $('#bulkSubmitBtn').addClass('d-none');
        $('#bulkApproveBtn').addClass('d-none');
        $('#bulkReturnBtn').addClass('d-none');
        $('#bulkMarkPaidBtn').addClass('d-none');
        
        // Show error message in place of buttons
        Swal.fire({
            icon: "warning",
            title: "Mixed Status Selection",
            text: "Cannot perform bulk actions on entries with different statuses. Please select only entries with the same status.",
            confirmButtonColor: '#ffc107',
        });
        
        // Clear selection
        clearBulkSelection();
        return;
    }
    
    // Hide all buttons first
    $('#bulkSubmitBtn').addClass('d-none');
    $('#bulkApproveBtn').addClass('d-none');
    $('#bulkReturnBtn').addClass('d-none');
    $('#bulkMarkPaidBtn').addClass('d-none');
    
    // Show buttons based on single status
    const status = uniqueStatuses[0];
    
    if (status === 'DRAFT') {
        $('#bulkSubmitBtn').removeClass('d-none');
    } else if (status === 'REVIEW') {
        $('#bulkApproveBtn').removeClass('d-none');
        $('#bulkReturnBtn').removeClass('d-none');
    } else if (status === 'APPROVED') {
        $('#bulkMarkPaidBtn').removeClass('d-none');
    }
    // If PAID, no bulk actions available (locked state)
}

/**
 * Get array of selected payroll entry IDs
 * @returns {array}
 */
function getSelectedPayrollIds() {
    const ids = [];
    $('.payrollCheckbox:checked').each(function() {
        ids.push($(this).data('payroll-id'));
    });
    return ids;
}

/**
 * Clear all bulk selections
 */
function clearBulkSelection() {
    $('#selectAllCheckbox').prop('checked', false);
    $('.payrollCheckbox').prop('checked', false);
    selectedPayrolls = {};
    updateBulkSelectionUI();
}

/**
 * BULK ACTION: Submit multiple payroll entries for review (DRAFT → REVIEW)
 */
function bulkSubmitForReview() {
    const payrollIds = getSelectedPayrollIds();
    
    if (payrollIds.length === 0) {
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
            <p>You are about to submit <strong>${payrollIds.length}</strong> payroll record(s) for review.</p>
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
            performBulkStatusUpdate(payrollIds, 'REVIEW', null);
        }
    });
}

/**
 * BULK ACTION: Approve multiple payroll entries (REVIEW → APPROVED)
 */
function bulkApprovePayroll() {
    const payrollIds = getSelectedPayrollIds();
    
    if (payrollIds.length === 0) {
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
            <p>You are about to approve <strong>${payrollIds.length}</strong> payroll record(s).</p>
            <p>These records will move from <strong>REVIEW</strong> to <strong>APPROVED</strong> status.</p>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            performBulkStatusUpdate(payrollIds, 'APPROVED', null);
        }
    });
}

/**
 * BULK ACTION: Return multiple payroll entries to draft (REVIEW → DRAFT)
 */
function bulkReturnToDraft() {
    const payrollIds = getSelectedPayrollIds();
    
    if (payrollIds.length === 0) {
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
            <p>You are about to return <strong>${payrollIds.length}</strong> payroll record(s) to DRAFT status.</p>
            <p>These records will move from <strong>REVIEW</strong> back to <strong>DRAFT</strong>.</p>
            <p>Please provide a reason for returning to draft:</p>
            <textarea id="returnReason" class="form-control" rows="3" placeholder="e.g., Rate correction needed"></textarea>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Return to Draft',
        cancelButtonText: 'Cancel',
        didOpen: (modal) => {
            setTimeout(() => {
                $('#returnReason').focus();
            }, 100);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = $('#returnReason').val();
            if (!reason || reason.trim() === '') {
                Swal.fire({
                    icon: "warning",
                    title: "Reason Required",
                    text: "Please provide a reason for returning to draft.",
                    confirmButtonColor: '#ffc107',
                });
                return;
            }
            performBulkStatusUpdate(payrollIds, 'DRAFT', reason.trim());
        }
    });
}

/**
 * BULK ACTION: Mark multiple payroll entries as paid (APPROVED → PAID)
 */
function bulkMarkAsPaid() {
    const payrollIds = getSelectedPayrollIds();
    
    if (payrollIds.length === 0) {
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
            <p><strong>⚠️ WARNING:</strong> This action finalizes the payroll and cannot be undone.</p>
            <p>You are about to mark <strong>${payrollIds.length}</strong> payroll record(s) as PAID.</p>
            <p>These records will be locked and no further changes will be allowed.</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Mark as Paid',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            performBulkStatusUpdate(payrollIds, 'PAID', null);
        }
    });
}

/**
 * Perform bulk status update via AJAX
 * @param {array} payrollIds
 * @param {string} newStatus
 * @param {string} reason - Optional reason (for DRAFT transition)
 */
function performBulkStatusUpdate(payrollIds, newStatus, reason) {
    $('#Loader').fadeIn();
    
    const postData = {
        payroll_entry_ids: payrollIds,
        new_status: newStatus,
        payroll_period_id: $('#payrollPeriodYearDropdown').val(),
        dept_id: $('#department').val(),
        emp_type_stamp: $('#employment_type').val(),
        action: 'update_payroll_status_bulk'
    };
    
    // Add reason if provided (for returns to draft)
    if (reason) {
        postData.reason = reason;
    }
    
    $.ajax({
        url: 'payroll_handler.php',
        type: 'POST',
        dataType: 'json',
        data: postData,
        success: function(response) {
            $('#Loader').fadeOut();
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: response.message,
                    confirmButtonColor: '#28a745',
                }).then(() => {
                    clearBulkSelection();
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
                text: "An error occurred while updating payroll status. Please try again.",
                confirmButtonColor: '#dc3545',
            });
            console.error('AJAX Error:', error);
        }
    });
}

/**
 * Initialize bulk selection when document is ready
 * and after payroll table is retrieved
 */
$(document).ready(function() {
    initializeBulkSelection();
});
