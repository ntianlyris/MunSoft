/**
 * GAA Net Pay Validator - JavaScript Module
 * 
 * Real-time validation of deduction entries against GAA PHP 5,000 minimum threshold
 * Provides UI feedback and blocks form submission if threshold breached
 * 
 * @author Development Team
 * @version 1.0.0
 * @since 2026-03-06
 */

(function() {
    'use strict';
    
    // ────────────────────────────────────────────────────────────
    // MODULE CONFIGURATION
    // ────────────────────────────────────────────────────────────
    
    const GAAValidator = {
        // API endpoint for validation
        apiEndpoint: '/MunSoft/payroll/gaa_validator_handler.php',
        
        // Debounce delay (ms)
        debounceDelay: 400,
        
        // CSS classes
        classes: {
            statusBadge: 'gaa-status-badge',
            statusOk: 'gaa-badge-ok',
            statusBlocked: 'gaa-badge-blocked',
            blockedModal: 'gaa-blocked-modal',
            blockReasonText: 'gaa-block-reason'
        },
        
        // Debounce timer storage
        debounceTimers: {},
        
        // ────────────────────────────────────────────────────────────
        // INITIALIZATION
        // ────────────────────────────────────────────────────────────
        
        /**
         * Initialize the validator - attach to all deduction input fields
         * Call this once on document ready
         */
        init: function() {
            console.log('[GAAValidator] Initializing...');
            
            // Attach to deduction amount input fields
            document.querySelectorAll('.deduction-amount-input, input[data-deduction-type]').forEach(input => {
                input.addEventListener('input', (e) => {
                    GAAValidator.onDeductionInputChange(e.target);
                });
            });
            
            console.log('[GAAValidator] Initialization complete');
        },
        
        // ────────────────────────────────────────────────────────────
        // EVENT HANDLERS
        // ────────────────────────────────────────────────────────────
        
        /**
         * Handle deduction input change - validates in real-time
         * 
         * @param {HTMLInputElement} inputElement - The deduction amount input field
         */
        onDeductionInputChange: function(inputElement) {
            // Clear existing debounce timer for this element
            const elementId = inputElement.id || inputElement.name;
            if (GAAValidator.debounceTimers[elementId]) {
                clearTimeout(GAAValidator.debounceTimers[elementId]);
            }
            
            // Set new debounced validation call
            GAAValidator.debounceTimers[elementId] = setTimeout(() => {
                GAAValidator.validateDeductionEntry(inputElement);
            }, GAAValidator.debounceDelay);
        },
        
        // ────────────────────────────────────────────────────────────
        // VALIDATION METHODS
        // ────────────────────────────────────────────────────────────
        
        /**
         * Validate a proposed deduction entry via AJAX
         * 
         * @param {HTMLInputElement} inputElement - The deduction input field
         */
        validateDeductionEntry: function(inputElement) {
            // Extract data from input element
            const recordId = inputElement.dataset.recordId || inputElement.closest('tr')?.dataset.recordId;
            const deductionType = inputElement.dataset.deductionType || 'authorized';
            const proposedAmount = parseFloat(inputElement.value) || 0;
            
            // Validate required parameters
            if (!recordId) {
                console.warn('[GAAValidator] Missing record_id for validation');
                return;
            }
            
            if (proposedAmount <= 0) {
                // Reset badge if amount is 0
                GAAValidator.clearValidationFeedback(inputElement);
                return;
            }
            
            // Build request payload
            const payload = new URLSearchParams({
                action: 'validate_deduction_entry',
                payroll_entry_id: recordId,
                proposed_amount: proposedAmount.toFixed(2),
                deduction_type: deductionType
            });
            
            // Make AJAX request
            fetch(GAAValidator.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: payload
            })
            .then(response => response.json())
            .then(data => {
                GAAValidator.handleValidationResponse(inputElement, data);
            })
            .catch(error => {
                console.error('[GAAValidator] Validation error:', error);
                GAAValidator.showValidationError(inputElement, 'Validation service unavailable');
            });
        },
        
        /**
         * Validate entire payroll computation before save
         * Called from payroll save button handler
         * 
         * @param {Object} payrollData - {record_id, employee_id, gross, mandatory, authorized}
         * @returns {Promise} Resolves to true if compliant, false if blocked
         */
        validatePayrollComputation: function(payrollData) {
            return new Promise((resolve, reject) => {
                const payload = new URLSearchParams({
                    action: 'validate_computation',
                    payroll_entry_id: payrollData.record_id,
                    employee_id: payrollData.employee_id,
                    gross_earnings: payrollData.gross.toFixed(2),
                    mandatory_deductions: payrollData.mandatory.toFixed(2),
                    authorized_deductions: payrollData.authorized.toFixed(2)
                });
                
                fetch(GAAValidator.apiEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: payload
                })
                .then(response => {
                    // Check for 422 (blocked) vs 200 (ok)
                    if (response.status === 422) {
                        return response.json().then(data => {
                            // Compliant but blocked - should not happen
                            resolve(false);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'BLOCKED') {
                        GAAValidator.showPayrollBlockedModal(data);
                        resolve(false);
                    } else if (data.status === 'OK') {
                        resolve(true);
                    } else {
                        reject(new Error('Validation failed: ' + data.message));
                    }
                })
                .catch(error => {
                    console.error('[GAAValidator] Payroll validation error:', error);
                    reject(error);
                });
            });
        },
        
        /**
         * Validate entire payroll period before approval
         * 
         * @param {number} periodId - Payroll period ID
         * @returns {Promise} Resolves to {can_approve, violations[]}
         */
        validateBatchApproval: function(periodId) {
            return new Promise((resolve, reject) => {
                const payload = new URLSearchParams({
                    action: 'validate_batch_approval',
                    payroll_period_id: periodId
                });
                
                fetch(GAAValidator.apiEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: payload
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.can_approve && data.violations && data.violations.length > 0) {
                        GAAValidator.showApprovalBlockedModal(data);
                        resolve(data);
                    } else {
                        resolve(data);
                    }
                })
                .catch(error => {
                    console.error('[GAAValidator] Batch approval validation error:', error);
                    reject(error);
                });
            });
        },
        
        // ────────────────────────────────────────────────────────────
        // RESPONSE HANDLERS
        // ────────────────────────────────────────────────────────────
        
        /**
         * Handle validation response from API
         * 
         * @param {HTMLInputElement} inputElement - The input that triggered validation
         * @param {Object} data - Validation result from API
         */
        handleValidationResponse: function(inputElement, data) {
            const rowElement = inputElement.closest('tr');
            if (!rowElement) return;
            
            if (data.status === 'OK') {
                GAAValidator.showCompliantFeedback(rowElement);
            } else if (data.status === 'BLOCKED') {
                GAAValidator.showBlockedFeedback(rowElement, data);
            } else {
                GAAValidator.showValidationError(inputElement, data.message);
            }
        },
        
        // ────────────────────────────────────────────────────────────
        // UI FEEDBACK METHODS
        // ────────────────────────────────────────────────────────────
        
        /**
         * Display compliant status badge (green)
         */
        showCompliantFeedback: function(rowElement) {
            let badge = rowElement.querySelector('.' + GAAValidator.classes.statusBadge);
            
            if (!badge) {
                badge = document.createElement('span');
                badge.className = GAAValidator.classes.statusBadge;
                const tdCell = rowElement.querySelector('td:last-child') || rowElement.appendChild(document.createElement('td'));
                tdCell.appendChild(badge);
            }
            
            badge.className = GAAValidator.classes.statusBadge + ' ' + GAAValidator.classes.statusOk;
            badge.textContent = '✓ Compliant';
            badge.style.color = '#28a745';
            badge.title = 'Net pay meets GAA threshold';
            
            // Enable save button if exists
            const saveBtn = document.getElementById('btn-save-deductions') || document.getElementById('btn-save-payroll');
            if (saveBtn) {
                saveBtn.disabled = false;
            }
        },
        
        /**
         * Display blocked status badge (red) with shortfall info
         */
        showBlockedFeedback: function(rowElement, data) {
            let badge = rowElement.querySelector('.' + GAAValidator.classes.statusBadge);
            
            if (!badge) {
                badge = document.createElement('span');
                badge.className = GAAValidator.classes.statusBadge;
                const tdCell = rowElement.querySelector('td:last-child') || rowElement.appendChild(document.createElement('td'));
                tdCell.appendChild(badge);
            }
            
            badge.className = GAAValidator.classes.statusBadge + ' ' + GAAValidator.classes.statusBlocked;
            badge.textContent = '✗ BLOCKED - PHP ' + GAAValidator.formatCurrency(data.shortfall) + ' shortfall';
            badge.style.color = '#dc3545';
            badge.title = data.message;
            
            // Disable save button
            const saveBtn = document.getElementById('btn-save-deductions') || document.getElementById('btn-save-payroll');
            if (saveBtn) {
                saveBtn.disabled = true;
            }
        },
        
        /**
         * Clear validation feedback for an element
         */
        clearValidationFeedback: function(inputElement) {
            const rowElement = inputElement.closest('tr');
            if (!rowElement) return;
            
            const badge = rowElement.querySelector('.' + GAAValidator.classes.statusBadge);
            if (badge) {
                badge.remove();
            }
            
            // Re-enable save button
            const saveBtn = document.getElementById('btn-save-deductions') || document.getElementById('btn-save-payroll');
            if (saveBtn) {
                saveBtn.disabled = false;
            }
        },
        
        /**
         * Show validation error badge
         */
        showValidationError: function(inputElement, errorMsg) {
            const rowElement = inputElement.closest('tr');
            if (!rowElement) return;
            
            let badge = rowElement.querySelector('.' + GAAValidator.classes.statusBadge);
            if (!badge) {
                badge = document.createElement('span');
                badge.className = GAAValidator.classes.statusBadge;
                const tdCell = rowElement.querySelector('td:last-child') || rowElement.appendChild(document.createElement('td'));
                tdCell.appendChild(badge);
            }
            
            badge.textContent = '⚠ ' + errorMsg;
            badge.style.color = '#ff9800';
        },
        
        /**
         * Show payroll save blocked modal
         */
        showPayrollBlockedModal: function(data) {
            let modal = document.getElementById('gaa-payroll-blocked-modal');
            
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'gaa-payroll-blocked-modal';
                modal.className = 'modal fade';
                modal.tabIndex = -1;
                modal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">⚠️ GAA COMPLIANCE VIOLATION</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger" role="alert">
                                    <strong>Payroll Entry BLOCKED</strong><br>
                                    <br>
                                    <div id="gaa-violation-details"></div>
                                </div>
                                <p class="text-muted mb-0">
                                    The computed net pay falls below the PHP 5,000.00 minimum required by the General Appropriations Act.
                                    Please adjust deductions or contact your manager for assistance.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }
            
            // Populate violation details
            const detailsDiv = modal.querySelector('#gaa-violation-details');
            detailsDiv.innerHTML = `
                <p><strong>Computed Net Pay:</strong> PHP ${GAAValidator.formatCurrency(data.net_pay)}</p>
                <p><strong>GAA Minimum:</strong> PHP ${GAAValidator.formatCurrency(data.threshold)}</p>
                <p><strong>Shortfall:</strong> <span class="text-danger font-weight-bold">PHP ${GAAValidator.formatCurrency(data.shortfall)}</span></p>
            `;
            
            // Show modal
            const bsModal = new (window.bootstrap && window.bootstrap.Modal ? window.bootstrap.Modal : function(el) {
                this.show = function() { el.style.display = 'block'; };
            })(modal);
            bsModal.show();
        },
        
        /**
         * Show batch approval blocked modal with violation list
         */
        showApprovalBlockedModal: function(data) {
            let modal = document.getElementById('gaa-approval-blocked-modal');
            
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'gaa-approval-blocked-modal';
                modal.className = 'modal fade';
                modal.tabIndex = -1;
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">⚠️ PAYROLL APPROVAL BLOCKED</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning" role="alert">
                                    <strong>${data.blocked} of ${data.total_checked} employees are below the PHP 5,000.00 threshold</strong><br>
                                    <small>Payroll period cannot be approved until all violations are resolved.</small>
                                </div>
                                <div id="gaa-violations-table"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Return to Adjustments</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }
            
            // Build violations table
            const tableDiv = modal.querySelector('#gaa-violations-table');
            let tableHtml = `
                <table class="table table-sm table-striped">
                    <thead class="table-danger">
                        <tr>
                            <th>Employee</th>
                            <th>Net Pay</th>
                            <th>Threshold</th>
                            <th>Shortfall</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            data.violations.forEach(v => {
                tableHtml += `
                    <tr>
                        <td>${v.employee_name}</td>
                        <td>PHP ${GAAValidator.formatCurrency(v.net_pay)}</td>
                        <td>PHP ${GAAValidator.formatCurrency(5000.00)}</td>
                        <td><span class="text-danger font-weight-bold">-PHP ${GAAValidator.formatCurrency(v.shortfall)}</span></td>
                    </tr>
                `;
            });
            
            tableHtml += `
                    </tbody>
                </table>
            `;
            
            tableDiv.innerHTML = tableHtml;
            
            // Show modal
            const bsModal = new (window.bootstrap && window.bootstrap.Modal ? window.bootstrap.Modal : function(el) {
                this.show = function() { el.style.display = 'block'; };
            })(modal);
            bsModal.show();
        },
        
        // ────────────────────────────────────────────────────────────
        // UTILITY METHODS
        // ────────────────────────────────────────────────────────────
        
        /**
         * Format number as Philippine currency
         */
        formatCurrency: function(amount) {
            return parseFloat(amount).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };
    
    // ────────────────────────────────────────────────────────────
    // EXPORT & INITIALIZATION
    // ────────────────────────────────────────────────────────────
    
    // Expose to global scope
    window.GAAValidator = GAAValidator;
    
    // Auto-initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            GAAValidator.init();
        });
    } else {
        GAAValidator.init();
    }
    
})();
