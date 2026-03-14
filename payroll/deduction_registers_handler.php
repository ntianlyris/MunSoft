<?php
header('Content-Type: application/json');

// Session & Authentication Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== RBAC: Permission Initialization (API-Safe) =====
require_once '../includes/class/Admin.php';
require_once '../includes/class/AccessControl.php';

// Initialize RBAC without HTML output
$MyAdmin = new Admin();
$Access = new AccessControl($MyAdmin);
$Access->syncToGlobals();

// @global bool $access_payroll - User has Payroll module access
// @global bool $manage_system  - User has admin access
global $access_payroll, $manage_system;

// Verify user has Payroll access permission
if (!isset($access_payroll) || !$access_payroll) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Forbidden - You do not have Payroll access permission']);
    exit;
}

// Request Method Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

require_once '../includes/class/DB_conn.php';
require_once '../includes/class/Remittance.php';
require_once '../includes/class/Deduction.php';

$db = new DB_conn();
$Remittance = new Remittance();
$Deduction = new Deduction();

$action = $_POST['action'] ?? '';


// ===== ACTION: Fetch Specific Deductions (for dropdowns) =====
if ($action === 'fetch_specific_deductions') {
    $type_id = intval($_POST['type_id'] ?? 0);
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';

    // Validate inputs
    if (empty($type_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid deduction type']);
        exit;
    }

    // Validate category if provided
    $allowed_categories = ['LOAN', 'OTHER', ''];
    if (!in_array($category, $allowed_categories)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid category']);
        exit;
    }

    // Use prepared statement
    $query = "SELECT config_deduction_id, deduct_title, deduct_code, deduct_category
              FROM config_deductions 
              WHERE deduction_type_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $type_id);

    if (!empty($category)) {
        $query = "SELECT config_deduction_id, deduct_title, deduct_code, deduct_category
                  FROM config_deductions 
                  WHERE deduction_type_id = ? AND deduct_category = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("is", $type_id, $category);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options[] = $row;
        }
    }
    
    echo json_encode(['status' => 'success', 'data' => $options]);
    exit;
}

// ===== ACTION: Fetch ALL Deduction Types at Once =====
if ($action === 'fetch_deduction_register') {
    // Input Validation & Type Casting
    $year = intval($_POST['year'] ?? 0);
    $period_id = intval($_POST['period_id'] ?? 0);
    $dept_id = isset($_POST['dept_id']) && $_POST['dept_id'] !== '' ? intval($_POST['dept_id']) : null;

    // Validate required fields
    if (empty($year) || empty($period_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required filters (Year and Period).']);
        exit;
    }

    // ===== Step 1: Convert Period ID to Date Range =====
    $period_query = "SELECT date_start, date_end FROM payroll_periods WHERE payroll_period_id = ? LIMIT 1";
    $stmt = $db->prepare($period_query);
    $stmt->bind_param("i", $period_id);
    $stmt->execute();
    $period_res = $stmt->get_result();
    
    if ($period_res->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Payroll Period ID.']);
        exit;
    }
    
    $period_row = $period_res->fetch_assoc();
    $remittance_period = $period_row['date_start'] . '_' . $period_row['date_end'];

    // ===== Step 2: Fetch ALL Deduction Types at Once =====
    $all_data = [
        "tax"        => $Remittance->GetRemittanceBIRTax($year, $remittance_period),
        "gsis"       => $Remittance->GetRemittanceGSIS($year, $remittance_period),
        "gsis_ecc"   => $Remittance->GetRemittanceGSISECC($year, $remittance_period),
        "philhealth" => $Remittance->GetRemittancePhilHealth($year, $remittance_period),
        "pagibig"    => $Remittance->GetRemittancePagibig($year, $remittance_period),
        "sss"        => $Remittance->GetRemittanceSSS($year, $remittance_period),
        "loans"      => $Remittance->GetRemittanceLoans($year, $remittance_period),
        "others"     => $Remittance->GetRemittanceOthers($year, $remittance_period)
    ];

    // ===== Step 3: Apply Department Filtering =====
    if ($dept_id !== null && $dept_id > 0) {
        $dept_cache = [];
        
        foreach ($all_data as $type => $rows) {
            if (is_array($rows) && !empty($rows)) {
                $filtered_rows = [];
                
                foreach ($rows as $row) {
                    $emp_id = intval($row['employee_id'] ?? 0);
                    
                    // Fetch department info for employee
                    if ($emp_id > 0) {
                        if (!isset($dept_cache[$emp_id])) {
                            $d_query = "SELECT DISTINCT d.dept_id FROM employee_employments_tbl e 
                                       INNER JOIN positions_tbl p ON e.position_id = p.position_id 
                                       INNER JOIN departments_tbl d ON p.dept_id = d.dept_id 
                                       WHERE e.employee_id = ? AND e.employment_status = 1 LIMIT 1";
                            $d_stmt = $db->prepare($d_query);
                            $d_stmt->bind_param("i", $emp_id);
                            $d_stmt->execute();
                            $d_res = $d_stmt->get_result();
                            
                            if ($d_res->num_rows > 0) {
                                $d_row = $d_res->fetch_assoc();
                                $dept_cache[$emp_id] = intval($d_row['dept_id']);
                            } else {
                                $dept_cache[$emp_id] = 0;
                            }
                        }
                        
                        // Include row if department matches
                        if ($dept_cache[$emp_id] === $dept_id) {
                            $filtered_rows[] = $row;
                        }
                    }
                }
                
                $all_data[$type] = $filtered_rows;
            }
        }
    }

    // ===== Step 4: Return Structured Data =====
    echo json_encode([
        'status' => 'success',
        'data' => $all_data,
        'period' => [
            'start' => $period_row['date_start'],
            'end' => $period_row['date_end'],
            'label' => $remittance_period,
            'id' => $period_id
        ]
    ]);
    exit;
}

// ===== ACTION: Fetch Loan Breakdown =====
if ($action === 'fetch_loan_breakdown') {
    $loan_id = intval($_POST['loan_id'] ?? 0);
    $period = $_POST['period'] ?? '';

    if (empty($loan_id) || empty($period)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }

    try {
        $loan_breakdown = $Remittance->GetRemittanceLoanBreakdown($loan_id, $period);
        
        if (!$loan_breakdown || !is_array($loan_breakdown)) {
            echo json_encode(['status' => 'error', 'message' => 'No breakdown data found']);
            exit;
        }

        // Get loan name from config
        $loan_query = "SELECT deduct_title FROM config_deductions WHERE config_deduction_id = ? LIMIT 1";
        $stmt = $db->prepare($loan_query);
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
        $loan_res = $stmt->get_result();
        
        $loan_name = 'Loan Breakdown';
        if ($loan_res->num_rows > 0) {
            $loan_row = $loan_res->fetch_assoc();
            $loan_name = $loan_row['deduct_title'] ?? 'Loan Breakdown';
        }

        echo json_encode([
            'status' => 'success',
            'data' => $loan_breakdown,
            'loan_name' => $loan_name
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching loan breakdown: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
?>
