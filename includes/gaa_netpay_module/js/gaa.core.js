/**
 * gaa.core.js
 * ─────────────────────────────────────────────────────────
 *  GAA Net Pay Intelligence Module — Core AJAX Engine
 *
 *  Provides the GAACore namespace: the low-level HTTP layer
 *  that all other GAA JS files depend on.
 *
 *  Usage (in any page that has included this file):
 *
 *    GAACore.classify(5300).then(res => console.log(res));
 *
 *    GAACore.analyzeEmployee(42, 18000, 12000)
 *      .then(profile => GAAui.renderEmployeeBadge('#badge', profile));
 *
 *    GAACore.headroom(42, 18000, 12000, 500)
 *      .then(res => console.log(res.data.can_add_deduction));
 * ─────────────────────────────────────────────────────────
 */

const GAACore = (() => {

    // ── Configuration ─────────────────────────────────────
    // Endpoint is read from a <meta> tag so PHP can inject it:
    // <meta name="gaa-api-endpoint" content="<?= GAAModule::cssUrl() ?>">
    // Falls back to default path.
    const _getEndpoint = () => {
        const meta = document.querySelector('meta[name="gaa-api-endpoint"]');
        return meta ? meta.content : '/gaa_netpay_module/api/gaa_api.php';
    };

    // ── Private request helper ────────────────────────────
    /**
     * Send a POST request to the GAA API endpoint.
     *
     * @param  {Object} payload   Key/value pairs matching API action params
     * @returns {Promise<Object>} Parsed JSON response envelope
     */
    const _post = async (payload) => {
        const endpoint = _getEndpoint();
        let response;

        try {
            response = await fetch(endpoint, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin',
            });
        } catch (networkError) {
            console.error('[GAACore] Network error:', networkError);
            return { success: false, status: 'NETWORK_ERROR', message: networkError.message };
        }

        if (!response.ok) {
            return { success: false, status: 'HTTP_ERROR', message: `HTTP ${response.status}` };
        }

        try {
            return await response.json();
        } catch (parseError) {
            console.error('[GAACore] JSON parse error:', parseError);
            return { success: false, status: 'PARSE_ERROR', message: 'Invalid JSON response.' };
        }
    };

    // ═════════════════════════════════════════════════════
    //  PUBLIC API METHODS  (map 1:1 with PHP bridge actions)
    // ═════════════════════════════════════════════════════

    /**
     * Classify a single net pay value.
     * @param {number} netPay
     * @returns {Promise}
     */
    const classify = (netPay) =>
        _post({ action: 'classify', net_pay: netPay });

    /**
     * Stage-1: Validate a deduction entry in real-time.
     * @param {number} employeeId
     * @param {number} proposedTotalDeductions
     * @param {number|null} currentGross
     * @returns {Promise}
     */
    const validateEntry = (employeeId, proposedTotalDeductions, currentGross = null) => {
        const payload = {
            action: 'validate_entry',
            employee_id: employeeId,
            proposed_total_deductions: proposedTotalDeductions,
        };
        if (currentGross !== null) payload.current_gross = currentGross;
        return _post(payload);
    };

    /**
     * Stage-2: Validate payroll before save.
     * @param {number} employeeId
     * @param {number} gross
     * @param {number} totalDeductions
     * @param {string} frequency  'monthly' | 'semi-monthly'
     * @returns {Promise}
     */
    const validatePayroll = (employeeId, gross, totalDeductions, frequency = 'monthly') =>
        _post({
            action: 'validate_payroll',
            employee_id: employeeId,
            gross,
            total_deductions: totalDeductions,
            payroll_frequency: frequency,
        });

    /**
     * Stage-3: Batch validation before approval.
     * @param {number} payrollPeriodId
     * @param {number|null} deptId
     * @returns {Promise}
     */
    const validateBatch = (payrollPeriodId, deptId = null) => {
        const payload = { action: 'validate_batch', payroll_period_id: payrollPeriodId };
        if (deptId) payload.dept_id = deptId;
        return _post(payload);
    };

    /**
     * Full AI intelligence profile for one employee.
     * @param {number} employeeId
     * @param {number} gross
     * @param {number} totalDeductions
     * @param {string} frequency
     * @returns {Promise}
     */
    const analyzeEmployee = (employeeId, gross, totalDeductions, frequency = 'monthly') =>
        _post({
            action: 'analyze_employee',
            employee_id: employeeId,
            gross,
            total_deductions: totalDeductions,
            payroll_frequency: frequency,
        });

    /**
     * Batch AI analysis for a full payroll period.
     * @param {number} payrollPeriodId
     * @param {number|null} deptId
     * @returns {Promise}
     */
    const analyzeBatch = (payrollPeriodId, deptId = null) => {
        const payload = { action: 'analyze_batch', payroll_period_id: payrollPeriodId };
        if (deptId) payload.dept_id = deptId;
        return _post(payload);
    };

    /**
     * Risk score only (lighter than analyzeEmployee).
     * @param {number} employeeId
     * @param {number} gross
     * @param {number} totalDeductions
     * @returns {Promise}
     */
    const getRiskScore = (employeeId, gross, totalDeductions) =>
        _post({
            action: 'risk_score',
            employee_id: employeeId,
            gross,
            total_deductions: totalDeductions,
        });

    /**
     * Deduction headroom calculator.
     * @param {number} employeeId
     * @param {number} gross
     * @param {number} currentDeductions
     * @param {number} proposedNew
     * @returns {Promise}
     */
    const getHeadroom = (employeeId, gross, currentDeductions, proposedNew = 0) =>
        _post({
            action: 'headroom',
            employee_id: employeeId,
            gross,
            current_deductions: currentDeductions,
            proposed_new_deduction: proposedNew,
        });

    /**
     * Next-period net pay prediction.
     * @param {number} employeeId
     * @returns {Promise}
     */
    const predict = (employeeId) =>
        _post({ action: 'predict', employee_id: employeeId });

    // ── Public surface ────────────────────────────────────
    return {
        classify,
        validateEntry,
        validatePayroll,
        validateBatch,
        analyzeEmployee,
        analyzeBatch,
        getRiskScore,
        getHeadroom,
        predict,
    };

})();

// Make available globally (e.g. for inline onclick handlers)
window.GAACore = GAACore;
