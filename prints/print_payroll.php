<?php
require_once('..//includes/tcpdf/tcpdf.php'); // Make sure TCPDF is installed and required
require_once('..//includes/view/functions.php');
require_once('..//includes/class/Payroll.php');
require_once('..//includes/class/Department.php');
require_once('..//includes/class/Employee.php');
require_once('..//includes/class/Employment.php');
require_once('..//includes/class/Signatory.php');

$Payroll = new Payroll();
$Department = new Department();
$Employee = new Employee();
$Employment = new Employment();
$Payroll_Signatory = new Signatory();

// ==========================================================
// GET parameters and Initialization
// ==========================================================
$period = $_GET['period'] ?? '';
$dept_id = $_GET['department'] ?? '';
$employment_type = $_GET['employment_type'] ?? '';
$start_date = '';
$end_date = '';
$results = [];
$department_name = '';
$pay_period_start = '';
$pay_period_end = '';

if (!$period || !$dept_id || !$employment_type) {
    die("Missing required parameters.");
}

if (strpos($period, '_') !== false) {
    // Period is a date range (e.g., "2025-08-01_2025-08-15")
    list($start_date, $end_date) = explode('_', $period);
    $payroll_period_id = $Payroll->GetPayrollPeriodByDates($start_date, $end_date)['payroll_period_id'];
} 
else {
    // Period is already a payroll_period_id
    $payroll_period_id = $period;
    $period_dates = $Payroll->GetPeriodDatesByID($payroll_period_id);
    if ($period_dates) {
        $start_date = $period_dates['date_start'];
        $end_date = $period_dates['date_end'];
    } else {
        die("Invalid payroll period ID.");
    }
}

// ==========================================================
// FETCH PAYROLL DATA ENTRIES
// ==========================================================

$department_name = $Department->GetDepartmentDetails($dept_id)['dept_name'];
$pay_period_start = OutputDate($start_date);
$pay_period_end = OutputDate($end_date);

$configured_earnings = $Payroll->FetchConfigEarningsIDandCode();
$configured_deductions = $Payroll->FetchConfigDeductionsIDandCode();
$configured_govshares = $Payroll->FetchGovSharesIDandCode();

$deduction_headers = [];
foreach ($configured_deductions as $deduct) {
    $deduction_headers[] = $deduct['deduct_code'];
}
$deduction_headers[] = 'TOTAL'; // Add total deductions column

$payroll_entries = $Payroll->FetchPayrollByPayPeriodAndDept($payroll_period_id, $dept_id, $employment_type);


//echo '<pre>';
//print_r($configured_earnings);
//echo '</pre>';

// ==========================================================
// BUILD PAYROLL DATA
// ==========================================================

$payrollData = [];

foreach ($payroll_entries as $row) {
    // Fetch employee full name and position
    $position_full = $Employment->FetchEmployeeEmploymentDetailsByIDs($row['employee_id'], $row['employment_id'])['position_title'];
    $position_title = strpos($position_full, '(') !== false ? substr($position_full, 0, strpos($position_full, '(')) : $position_full;
    $employee_name = $Employee->GetEmployeeFullNameByID($row['employee_id'])['full_name'];

    // Decode JSON breakdowns safely
    $earnings = json_decode($row['earnings_breakdown'], true) ?? [];
    $deductions = json_decode($row['deductions_breakdown'], true) ?? [];
    $govshares = json_decode($row['govshares_breakdown'], true) ?? [];

    // Map detailed arrays
    $earningsArr = buildEarningsArray($configured_earnings, $earnings);
    $deductionsArr = buildDeductionsArray($configured_deductions, $deductions);
    $govsharesArr = buildGovSharesArray($configured_govshares, $govshares);

    // Build structured payroll entry
    $payrollData[] = [
        'name' => strtoupper($employee_name),
        'designation' => trim($position_title),
        'earnings' => $earningsArr,
        'deductions' => $deductionsArr,
        'govshares' => $govsharesArr,
        'net' => (float)$row['net_pay']
    ];
}

// ==========================================================
// HELPER FUNCTIONS For BUILDING EARNINGS AND DEDUCTIONS ARRAYS
// ==========================================================

function buildEarningsArray($configured_earnings, $earnings) {
    // --- STEP 1: Index earnings by config_earning_id for fast lookup ---
    $earnings_map = [];
    foreach ($earnings as $e) {
        $earnings_map[$e['config_earning_id']] = $e;
    }

    // --- STEP 2: Build result array ---
    $result = [];
    $others_total = 0;

    // Handle IDs 1 & 2 (Sal-Reg & Sal-Cas) as "Basic"
    $basic_amount = 0;
    $basic_ids = [1, 2];
    foreach ($basic_ids as $id) {
        if (isset($earnings_map[$id]) && $earnings_map[$id]['amount'] > 0) {
            $basic_amount += $earnings_map[$id]['amount'];
        }
    }
    if ($basic_amount > 0) {
        $result[] = ['label' => 'Basic', 'amount' => $basic_amount];
    }

    // Handle PERA (ID 3) directly
    if (isset($earnings_map[3])) {
        $result[] = ['label' => 'PERA', 'amount' => $earnings_map[3]['amount']];
    } else {
        $result[] = ['label' => 'PERA', 'amount' => 0];
    }

    // Handle all others (non-1,2,3)
    foreach ($configured_earnings as $conf) {
        $id = $conf['config_earning_id'];
        if (!in_array($id, [1, 2, 3])) {
            $others_total += $earnings_map[$id]['amount'] ?? 0;
        }
    }

    // Add "Others" and "Gross"
    $result[] = ['label' => 'Others', 'amount' => $others_total];
    $result[] = ['label' => 'Gross', 'amount' => array_sum(array_column($result, 'amount'))];

    // --- Output ---
    return $result;
}

function buildDeductionsArray($configured_deductions, $deductions) {
    // --- STEP 1: Index deductions by config_deduction_id for fast lookup ---
    $deductions_map = [];
    foreach ($deductions as $d) {
        $deductions_map[$d['config_deduction_id']] = $d;
    }

    // --- STEP 2: Build result array ---
    $result = [];
    $total_deductions = 0;

    foreach ($configured_deductions as $conf) {
        $id = $conf['config_deduction_id'];
        $amount = $deductions_map[$id]['amount'] ?? 0;
        $result[] = ['label' => $conf['deduct_code'], 'amount' => $amount];
        $total_deductions += $amount;
    }

    // Add total deductions at the end
    $result[] = ['label' => 'Total', 'amount' => $total_deductions];

    // --- Output ---
    return $result;
}

function buildGovSharesArray($configured_govshares, $govshares) {
    // --- STEP 1: Index govshares by govshare_id for fast lookup ---
    $govshares_map = [];
    foreach ($govshares as $g) {
        $govshares_map[$g['govshare_id']] = $g;
    }

    // --- STEP 2: Build result array ---
    $result = [];

    foreach ($configured_govshares as $conf) {
        $id = $conf['govshare_id'];
        $amount = $govshares_map[$id]['amount'] ?? 0;
        $result[] = ['label' => $conf['govshare_code'], 'amount' => $amount];
    }

    // --- Output ---
    return $result;
}

// ==========================================================
// DEFINE DYNAMIC HEADER STRUCTURE
// ==========================================================

// Group headers
$name_width = 11;
$earnings_width = 14;
$deductions_width = 56;
$govshares_width = 12;
$net_width = 4;
$signature_width = 4;

$headerGroups = [
    ['label' => 'EMPLOYEE', 'colspan' => 1, 'rowspan' => 2, 'width' => $name_width],
    ['label' => 'EARNINGS', 'colspan' => 4, 'width' => $earnings_width],
    ['label' => 'DEDUCTIONS', 'colspan' => count($deduction_headers) , 'width' => $deductions_width],
    ['label' => 'GOV SHARES', 'colspan' => 4, 'width' => $govshares_width],
    ['label' => 'NET AMOUNT', 'colspan' => 1, 'rowspan' => 2, 'width' => $net_width],
    ['label' => 'SIGNATURE', 'colspan' => 1, 'rowspan' => 2, 'width' => $signature_width],
];

// Subheaders (single-level now)
$subHeader = [
    'EARNINGS' => ['Basic', 'PERA', 'Others', 'Gross'],
    'DEDUCTIONS' => $deduction_headers,
    'GOVSHARES' => ['L/R', 'HDMF', 'PHIC', 'ECC']
];

// ==========================================================
// TCPDF SETTINGS
// ==========================================================
class MYPDF extends TCPDF {

    protected $customHeaderData = [];

    public function setCustomHeaderData($data) {
        $this->customHeaderData = $data;
    }

    // Page header
    public function Header() {
        // Path to the logo file
        $image_file = '../includes/images/polanco_logo.jpg'; // fixed double slashes

        // Add logo (x=15mm, y=8mm, width=20mm)
        if (file_exists($image_file)) {
            $this->Image($image_file, 135, 5, 18, '', 'JPEG', '', 'T', false, 300); 
        }

        // Move down a bit after logo
        $this->SetY(7);
        $this->SetFont('arialnarrow', '', 9);

        // Safely fetch header data
        $pay_period_start = isset($this->customHeaderData['pay_period_start']) ? $this->customHeaderData['pay_period_start'] : '';
        $pay_period_end   = isset($this->customHeaderData['pay_period_end']) ? $this->customHeaderData['pay_period_end'] : '';
        $department_name  = isset($this->customHeaderData['department_name']) ? $this->customHeaderData['department_name'] : '';

        // HTML header content
        $html = '
        <div style="text-align:center; line-height:1.4;">
            <span>Republic of the Philippines</span><br>
            <span>Province of Zamboanga del Norte</span><br>
            <span style="font-size:10px; font-weight:bold;">MUNICIPALITY OF POLANCO</span><br><br>
            <span style="font-size:10px;">GENERAL PAYROLL</span><br>
            <span>Payroll Period: ' . htmlspecialchars($pay_period_start) . ' to ' . htmlspecialchars($pay_period_end) . '</span><br>
            <span>Department: ' . htmlspecialchars($department_name) . '</span>
        </div>';

        $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);
    }

    // Optional Footer
    public function Footer() {
        $this->SetY(-10);
        $this->SetFont('arialnarrow', 'I', 7);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new MYPDF('L', 'mm', 'LEGAL', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Municipality of Polanco');
$pdf->SetTitle('POLANCO MUNICIPAL PAYROLL');
$pdf->SetMargins(5, 30, 5);    // left, top, right
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->setCustomHeaderData([
    'pay_period_start' => $pay_period_start,
    'pay_period_end' => $pay_period_end,
    'department_name' => $department_name
]);

$pdf->AddPage();


// ==========================================================
// HEADER / TITLE
// ==========================================================
$html = '<p style="font-size:9px;">We acknowledge receipt of the sum shown opposite our names as full compensation for services rendered for the period stated.
        </p><br>';

// ==========================================================
// TABLE STRUCTURE
// ==========================================================
$html .= '<style>
            table { border: 1px solid black; border-collapse: collapse; width:100%;}
            th, td { border: 1px solid black; padding: 4px; }
          </style>
        <table cellspacing="0" cellpadding="2">';

// === TOP HEADER ROW (Group Headers) ===
$html .= '<tr style="font-weight:bold;">';
foreach ($headerGroups as $group) {
    $rowspan = isset($group['rowspan']) ? ' rowspan="'.$group['rowspan'].'"' : '';
    $colspan = isset($group['colspan']) ? ' colspan="'.$group['colspan'].'"' : '';
    $width = isset($group['width']) ? 'width:'.$group['width'].'%;' : ''; // ✅ fixed syntax
    $html .= '<th'.$rowspan.$colspan.' style="text-align:center;'.$width.'">'.strtoupper($group['label']).'</th>';
}
$html .= '</tr>';

// === SECOND HEADER ROW (Subheaders) ===
$html .= '<tr style="font-weight:bold; background-color:#fafafa;">';
foreach ($subHeader['EARNINGS'] as $sub) {
    $html .= '<th style="text-align:center;">'.strtoupper($sub).'</th>';
}
foreach ($subHeader['DEDUCTIONS'] as $sub) {
    $html .= '<th style="text-align:center;">'.strtoupper($sub).'</th>';
}
foreach ($subHeader['GOVSHARES'] as $sub) {
    $html .= '<th style="text-align:center;">'.strtoupper($sub).'</th>';
}
$html .= '</tr>';

// ==========================================================
// TABLE BODY (Single row per employee)
// ==========================================================
$count = 0;
$total_basic = 0;
$total_gross = 0;
$total_deductions = 0;
foreach ($payrollData as $row) {
    $count++;
    $html .= '<tr>';
    $html .= '<td style="text-align:left; font-size:7px; width:'.$name_width.'%;">'
                .$count.'.) <strong> '.htmlspecialchars($row['name']).'</strong><br>
                <span style="font-size:8px;"> '.' '.htmlspecialchars($row['designation']).'</span>
              </td>';

    foreach ($row['earnings'] as $val) {
        $html .= '<td style="text-align:right;">' . ($val['amount'] != 0 ? number_format($val['amount'], 2) : '-') . '</td>';
        $total_basic += $val['label'] === 'Basic' ? $val['amount'] : 0;
        $total_gross += $val['label'] === 'Gross' ? $val['amount'] : 0;
    }
    foreach ($row['deductions'] as $val) {
        $html .= '<td style="text-align:right;">' . ($val['amount'] != 0 ? number_format($val['amount'], 2) : '-') . '</td>';
        $total_deductions += $val['label'] === 'Total' ? $val['amount'] : 0;
    }
    foreach ($row['govshares'] as $val) {
        $html .= '<td style="text-align:right;">' . ($val['amount'] !== 0 ? number_format($val['amount'],2) : '-') . '</td>';
    }
    $html .= '<td style="text-align:right; font-weight:bold;">'.($row['net'] !== 0 ? number_format($row['net'],2) : '-') .'</td>';
    $html .= '<td>'.$count.'.)</td>';
    $html .= '</tr>';
}

// ==========================================================
// TOTAL ROW
// ==========================================================

$totalNet = array_sum(array_column($payrollData, 'net'));

$html .= '<tr style="font-weight:bold;">';
$html .= '<td style="text-align:right;">TOTAL</td>';
foreach ($headerGroups as $group) {
    if ($group['label'] === 'EARNINGS') {
        // Earnings total
        $html .= '<td style="text-align:right;">' . ($total_gross != 0 ? number_format($total_basic, 2) : '-') . '</td>'; // Basic
        $html .= '<td style="text-align:right;">-</td>'; // PERA (not totaled here)
        $html .= '<td style="text-align:right;">-</td>'; // Others (not totaled here)
        $html .= '<td style="text-align:right;">' . ($total_gross != 0 ? number_format($total_gross, 2) : '-') . '</td>'; // Gross
    } 
    elseif ($group['label'] === 'DEDUCTIONS') {
        // Deductions total
        foreach ($deduction_headers as $deduct) {
            if ($deduct === 'TOTAL') {
                $html .= '<td style="text-align:right;">' . ($total_deductions != 0 ? number_format($total_deductions, 2) : '-') . '</td>';
            } else {
                $html .= '<td style="text-align:right;">-</td>'; // Individual deductions not totaled here
            }
        }
    } 
    elseif ($group['label'] === 'GOV SHARES') {
        // Gov shares total (not calculated here)
        for ($i = 0; $i < count($subHeader['GOVSHARES']); $i++) {
            $html .= '<td style="text-align:right;">-</td>';
        }
    }
}
$html .= '<td style="text-align:right;">' . ($totalNet != 0 ? number_format($totalNet, 2) : '-') . '</td>';
$html .= '<td></td>';
$html .= '</tr>';
$html .= '</table>';

// ==========================================================
// OUTPUT PDF
// ==========================================================
$pdf->SetXY(5, $pdf->GetY() + 12);
$pdf->SetFont('arialnarrow', '', 7);
$pdf->writeHTML($html, true, false, true, false, '');


// ==========================================================
// SIGNATORIES FOOTER
// ==========================================================

// Footer for signatories
//$pdf->Ln(1); // Space before footer
$pdf->SetFont('arialnarrow', '', 9);

// Get active signatories for payroll
$signatories = $Payroll_Signatory->FetchActiveSignatoriesByReportType('PAYROLL');
$filtered_signatories = array();

// Filter signatories based on role and department
foreach ($signatories as $sign) {
    // Include non-department head signatories
    if ($sign['role_type'] !== 'HEAD') {
        $filtered_signatories[] = $sign;
        continue;
    }
    
    // For department heads, only include if department matches
    if ($sign['role_type'] === 'HEAD' && $sign['dept_id'] == $dept_id) {
        $filtered_signatories[] = $sign;
    }
}

// Calculate rows needed (4 signatories per row)
$signatories_per_row = 4;
$total_signatories = count($filtered_signatories);
$total_rows = ceil($total_signatories / $signatories_per_row);

// Build signatory table with multiple rows
$signatory_table = '<table border="0" cellpadding="4" cellspacing="0" width="100%">';

for ($row = 0; $row < $total_rows; $row++) {
    $signatory_table .= '<tr>';
    
    // Calculate start and end index for current row
    $start_index = $row * $signatories_per_row;
    $end_index = min(($row + 1) * $signatories_per_row, $total_signatories);
    
    // Add cells for current row
    for ($i = $start_index; $i < $end_index; $i++) {
        $sign = $filtered_signatories[$i];
        $signatory_table .= '
            <td width="25%" align="center">
                ' . $sign['sign_particulars'] . '<br>
                <br><br>
                <span style="font-size:10px;";><b>' . $sign['full_name'] . '</b></span><br>
                ' . $sign['position_title'] . '
            </td>';
    }
    
    // Fill remaining cells in the last row if needed
    $remaining_cells = $signatories_per_row - ($end_index - $start_index);
    for ($i = 0; $i < $remaining_cells; $i++) {
        $signatory_table .= '<td width="25%"></td>';
    }
    
    $signatory_table .= '</tr>';
}

$signatory_table .= '</table>';

// Add some spacing between rows
$signatory_table = str_replace('</tr>', '</tr><tr><td colspan="4" height="10"></td></tr>', $signatory_table);

$pdf->writeHTML($signatory_table, true, false, true, false, '');


//debugger
//echo '<pre>';
//print_r($earningsArr);
//echo '</pre>';


ob_end_clean();
$pdf->Output('municipal_payroll.pdf', 'I');
?>