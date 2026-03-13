<?php
header('Content-Type: application/json');
require_once '../includes/class/DB_conn.php';
require_once '../includes/class/Remittance.php';
require_once '../includes/class/Deduction.php';

$db = new DB_conn();
$Remittance = new Remittance();
$Deduction = new Deduction();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Action to fetch specific deductions (loans/others) for the dropdown
    if ($action === 'fetch_specific_deductions') {
        $type_id = $_POST['type_id'] ?? '';
        $category = $_POST['category'] ?? '';

        $query = "SELECT config_deduction_id, deduct_title, deduct_code 
                  FROM config_deductions 
                  WHERE deduction_type_id = '$type_id'";
        
        if (!empty($category)) {
            $query .= " AND deduct_category = '$category'";
        }
        
        $query .= " ORDER BY deduct_title ASC";
        
        $result = $db->query($query);
        $options = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $options[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $options]);
        exit;
    }

    // Action to fetch the actual report data
    if ($action === 'fetch_deduction_register') {
        $year = $_POST['year'] ?? '';
        $period_id = $_POST['period_id'] ?? '';
        $dept_id = $_POST['dept_id'] ?? 'all';
        $type_id = $_POST['type_id'] ?? '';
        $type_name = $_POST['type_name'] ?? '';
        $category = $_POST['category'] ?? '';
        $specific_id = $_POST['specific_id'] ?? '';

        if (empty($year) || empty($period_id) || empty($type_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required filters.']);
            exit;
        }

        // --- FIXED: Convert Period ID to Date Range String for Remittance Class ---
        $period_query = "SELECT date_start, date_end FROM payroll_periods WHERE payroll_period_id = '$period_id' LIMIT 1";
        $period_res = $db->query($period_query);
        if ($period_res && $period_res->num_rows > 0) {
            $period_row = $period_res->fetch_assoc();
            $remittance_period = $period_row['date_start'] . '_' . $period_row['date_end'];
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Payroll Period ID.']);
            exit;
        }

        $data = [];
        $type_name_upper = strtoupper($type_name);

        // Map type_name to Remittance methods
        if (strpos($type_name_upper, 'PHILHEALTH') !== false) {
            $data = $Remittance->GetRemittancePhilHealth($year, $remittance_period);
        } elseif (strpos($type_name_upper, 'BIR') !== false || strpos($type_name_upper, 'TAX') !== false) {
            $data = $Remittance->GetRemittanceBIRTax($year, $remittance_period);
        } elseif (strpos($type_name_upper, 'GSIS') !== false) {
            if (!empty($specific_id)) {
                $data = $Remittance->GetRemittanceLoanBreakdown($specific_id, $remittance_period);
            } else {
                $data = $Remittance->GetRemittanceGSIS($year, $remittance_period);
            }
        } elseif (strpos($type_name_upper, 'PAGIBIG') !== false || strpos($type_name_upper, 'PAG-IBIG') !== false) {
            $data = $Remittance->GetRemittancePagibig($year, $remittance_period);
        } elseif (strpos($type_name_upper, 'SSS') !== false) {
            $data = $Remittance->GetRemittanceSSS($year, $remittance_period);
        } elseif ($category === 'LOAN') {
            if (!empty($specific_id)) {
                $data = $Remittance->GetRemittanceLoanBreakdown($specific_id, $remittance_period);
            } else {
                $data = $Remittance->GetRemittanceLoans($year, $remittance_period);
            }
        } else {
            // Fallback for OTHERS or specific ones
            if (!empty($specific_id)) {
                $data = $Remittance->GetRemittanceLoanBreakdown($specific_id, $remittance_period);
            } else {
                $data = $Remittance->GetRemittanceOthers($year, $remittance_period);
            }
        }

        if (!is_array($data)) {
            echo json_encode(['status' => 'error', 'message' => 'No data returned from remittance module.']);
            exit;
        }

        // --- NORMALIZATION LAYER ---
        $normalized = [];
        $dept_cache = [];
        
        foreach ($data as $row) {
            $emp_id = $row['employee_id'] ?? 0;
            
            // 1. Standardize Employee Name and Basic Info
            if ($emp_id > 0) {
                $row['full_name'] = $row['employee'] ?? $row['employee_name'] ?? 'Unknown Employee';
                
                // 2. Fetch/Cache Department Info
                if (!isset($dept_cache[$emp_id])) {
                    $d_query = "SELECT d.dept_id, d.dept_title 
                               FROM employee_employments_tbl e 
                               INNER JOIN positions_tbl p ON e.position_id = p.position_id 
                               INNER JOIN departments_tbl d ON p.dept_id = d.dept_id 
                               WHERE e.employee_id = '$emp_id' AND e.employment_status = 1 LIMIT 1";
                    $d_res = $db->query($d_query);
                    if ($d_res && $d_res->num_rows > 0) {
                        $dept_cache[$emp_id] = $d_res->fetch_assoc();
                    } else {
                        $dept_cache[$emp_id] = ['dept_id' => 0, 'dept_title' => 'Unassigned'];
                    }
                }
                $row['dept_id'] = $dept_cache[$emp_id]['dept_id'];
                $row['dept_title'] = $dept_cache[$emp_id]['dept_title'];
                
                // 3. Normalize Values for consistent mapping in JS
                // PhilHealth / Pag-IBIG
                if (isset($row['employee_share']) && isset($row['employer_share'])) {
                    $row['ee_share'] = $row['employee_share'];
                    $row['er_share'] = $row['employer_share'];
                    $row['total_phic'] = $row['total'] ?? ($row['employee_share'] + $row['employer_share']);
                    $row['total_pagibig'] = $row['total'] ?? ($row['employee_share'] + $row['employer_share']);
                }
                
                // GSIS
                if (isset($row['total_amount']) && strpos($type_name_upper, 'GSIS') !== false) {
                    $row['gsis_personal'] = $row['employee_share'] ?? 0;
                    $row['gsis_govt'] = $row['employer_share'] ?? 0;
                    $row['total_gsis'] = $row['total_amount'];
                }
                
                // BIR Tax
                if (strpos($type_name_upper, 'BIR') !== false || strpos($type_name_upper, 'TAX') !== false) {
                    $row['tax_deducted'] = $row['amount'] ?? 0;
                    $row['gross'] = $row['locked_basic'] ?? 0;
                }
                
                // Loans / Others (Specific Breakdown)
                if (isset($row['total_deduction'])) {
                    $row['deduction_amount'] = $row['total_deduction'];
                }
            } else {
                // Summary rows (no employee_id)
                $row['full_name'] = null; // Mark as summary for JS
            }

            // Post-fetch filtering by Department
            if ($dept_id !== 'all' && isset($row['dept_id'])) {
                if ($row['dept_id'] == $dept_id) {
                    $normalized[] = $row;
                }
            } else {
                $normalized[] = $row;
            }
        }

        echo json_encode(['status' => 'success', 'data' => $normalized]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
?>
