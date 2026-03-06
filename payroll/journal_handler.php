<?php

include_once '../includes/class/Payroll.php';
include_once '../includes/class/Department.php';
include_once '../includes/class/JournalEntry.php';

// Initialize response array
$response = array(
    'status' => 'error',
    'message' => '',
    'data' => array()
);

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    switch ($action) {
        case 'generate_journal':
            try {
                // Validate required parameters
                $year = isset($_REQUEST['year']) ? trim($_REQUEST['year']) : '';
                $period_id = isset($_REQUEST['period_id']) ? trim($_REQUEST['period_id']) : '';
                $dept_id = isset($_REQUEST['dept_id']) ? trim($_REQUEST['dept_id']) : '';
                $employment_type = isset($_REQUEST['employment_type']) ? trim($_REQUEST['employment_type']) : '';
                $consolidate_all = isset($_REQUEST['consolidate_all']) ? (int)$_REQUEST['consolidate_all'] : 0;

                if (empty($year) || empty($period_id) || empty($employment_type)) {
                    throw new Exception('Missing required parameters');
                }

                // If not consolidated, dept_id is required
                if (!$consolidate_all && empty($dept_id)) {
                    throw new Exception('Please select a department or choose All Departments');
                }

                // Initialize classes
                $JournalEntry = new JournalEntry();
                $Payroll = new Payroll();
                $Department = new Department();

                // Generate journal entries
                $journal_entries = array();

                // Get payroll data for the period - use appropriate method based on consolidation flag
                if ($consolidate_all) {
                    $payroll_data = $Payroll->FetchPayrollByPayPeriodAllDepts($period_id, $employment_type);
                } else {
                    $payroll_data = $Payroll->FetchPayrollByPayPeriodAndDept($period_id, $dept_id, $employment_type);
                }
                
                if (!$payroll_data || empty($payroll_data)) {
                    throw new Exception('No payroll data found for the selected period');
                }

                // Set payroll data in JournalEntry class
                $JournalEntry->setPayrollData($payroll_data);
                $JournalEntry->setSalaryTypeAcctCodeByEmploymentType($employment_type);
                
                /* For debugging purposes
                echo "<pre>";
                print_r($JournalEntry->BuildDebitEntriesFromEarningsAndGovShares());
                echo "<br/>";
                
                print_r($JournalEntry->BuildCreditEntriesFromDeductions());
                echo "</pre>";
                */

                // Build journal entries
                $debit_entries = $JournalEntry->BuildDebitEntriesFromEarningsAndGovShares();
                $credit_entries = $JournalEntry->BuildCreditEntriesFromDeductions();
                $journal_entries = array_merge($debit_entries, $credit_entries);

                $response['status'] = 'success';
                $response['message'] = 'Journal entries generated successfully';
                $response['data'] = $journal_entries;

            } catch (Exception $e) {
                $response['status'] = 'error';
                $response['message'] = $e->getMessage();
            }
            break;

        default:
            $response['message'] = 'Invalid action';
            break;
    }
} else {
    $response['message'] = 'No action specified';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>