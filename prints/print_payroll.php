<?php
ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');
// ==========================================================
// INITIALIZATION & DATA FETCHING (Logic remains same)
// ==========================================================
require_once('../includes/tcpdf/tcpdf.php'); 
require_once('../includes/view/functions.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Department.php');
require_once('../includes/class/Employee.php');
require_once('../includes/class/Employment.php');
require_once('../includes/class/Signatory.php');

$Payroll = new Payroll();
$Department = new Department();
$Employee = new Employee();
$Employment = new Employment();
$Payroll_Signatory = new Signatory();

$period = $_GET['period'] ?? '';
$dept_id = $_GET['department'] ?? '';
$employment_type = $_GET['employment_type'] ?? '';

if (!$period || !$dept_id || !$employment_type) {
    die("Missing required parameters.");
}

// ... (Date processing logic)
if (strpos($period, '_') !== false) {
    list($start_date, $end_date) = explode('_', $period);
    $payroll_period_id = $Payroll->GetPayrollPeriodByDates($start_date, $end_date)['payroll_period_id'];
} else {
    $payroll_period_id = $period;
    $period_dates = $Payroll->GetPeriodDatesByID($payroll_period_id);
    $start_date = $period_dates['date_start'];
    $end_date = $period_dates['date_end'];
}

$department_name = $Department->GetDepartmentDetails($dept_id)['dept_name'];
$pay_period_start = OutputDate($start_date);
$pay_period_end = OutputDate($end_date);

$configured_earnings = $Payroll->FetchConfigEarningsIDandCode();
$configured_deductions = $Payroll->FetchConfigDeductionsIDandCode();
$configured_govshares = $Payroll->FetchGovSharesIDandCode();

$deduction_headers = [];
foreach ($configured_deductions as $deduct) { $deduction_headers[] = $deduct['deduct_code']; }
$deduction_headers[] = 'TOTAL';

$payroll_entries = $Payroll->FetchPayrollByPayPeriodAndDept($payroll_period_id, $dept_id, $employment_type);

// Build Payroll Data Object
$payrollData = [];
foreach ($payroll_entries as $row) {
    $pos = $Employment->FetchEmployeeEmploymentDetailsByIDs($row['employee_id'], $row['employment_id'])['position_title'];
    $position_title = strpos($pos, '(') !== false ? substr($pos, 0, strpos($pos, '(')) : $pos;
    $employee_name = $Employee->GetEmployeeFullNameByID($row['employee_id'])['full_name'];

    $payrollData[] = [
        'name' => strtoupper($employee_name),
        'designation' => trim($position_title),
        'earnings' => buildEarningsArray($configured_earnings, json_decode($row['earnings_breakdown'], true) ?? []),
        'deductions' => buildDeductionsArray($configured_deductions, json_decode($row['deductions_breakdown'], true) ?? []),
        'govshares' => buildGovSharesArray($configured_govshares, json_decode($row['govshares_breakdown'], true) ?? []),
        'net' => (float)$row['net_pay']
    ];
}

// ==========================================================
// DYNAMIC DEDUCTION FILTERING (Remove zero-value deductions)
// ==========================================================
// Analyze all payroll data to find which deductions have non-zero values
$active_deduction_indices = [];
$deduction_totals = [];

// Initialize totals array
for ($i = 0; $i < count($deduction_headers); $i++) {
    $deduction_totals[$i] = 0;
}

// Scan all payroll entries to find deductions with values
foreach ($payrollData as $row) {
    foreach ($row['deductions'] as $di => $ded) {
        if ($ded['amount'] > 0) {
            $active_deduction_indices[$di] = true;
        }
        $deduction_totals[$di] += $ded['amount'];
    }
}

// Build filtered deduction headers and create index mapping
$filtered_deduction_headers = [];
$deduction_index_map = []; // Maps old index to new index
$new_index = 0;

for ($old_index = 0; $old_index < count($deduction_headers); $old_index++) {
    if (isset($active_deduction_indices[$old_index]) || $deduction_headers[$old_index] === 'TOTAL') {
        $filtered_deduction_headers[] = $deduction_headers[$old_index];
        $deduction_index_map[$old_index] = $new_index;
        $new_index++;
    }
}

// If no deductions with values found, at least show TOTAL
if (count($filtered_deduction_headers) === 0) {
    $filtered_deduction_headers = ['TOTAL'];
    $deduction_index_map[count($deduction_headers) - 1] = 0;
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
// TCPDF EXTENSION
// ==========================================================
class MYPDF extends TCPDF {
    protected $customHeaderData = [];
    public function setCustomHeaderData($data) { $this->customHeaderData = $data; }

    public function Header() {
        $image_file = '../includes/images/polanco_logo.jpg';
        if (file_exists($image_file)) {
            $this->Image($image_file, 135, 5, 18, 0.0, 'JPEG', '', 'T', false, 300); 
        }
        $this->SetY(7);
        $this->SetFont('helvetica', '', 9);
        $html = '<div style="text-align:center;">
                    <span>Republic of the Philippines</span><br>
                    <span>Province of Zamboanga del Norte</span><br>
                    <strong style="font-size:11px;">MUNICIPALITY OF POLANCO</strong><br><br>
                    <strong style="font-size:10px;">GENERAL PAYROLL</strong><br>
                    <span>Period: '.$this->customHeaderData['pay_period_start'].' to '.$this->customHeaderData['pay_period_end'].'</span><br>
                    <span>Dept: '.$this->customHeaderData['department_name'].'</span>
                </div>';
        $this->writeHTMLCell(0, 0, 0.0, 0.0, $html, 0, 1, 0, true, 'C', true);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 7);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// ==========================================================
// PDF CONFIGURATION
// ==========================================================
// Custom "Super Wide" paper size: 17in x 8.5in (431.8mm x 215.9mm)
$custom_paper_size = array(431.8, 215.9);
$pdf = new MYPDF('L', 'mm', $custom_paper_size, true, 'UTF-8', false); 
$pdf->SetMargins(2, 40, 5); 
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(TRUE, 25); 
$pdf->setCustomHeaderData([
    'pay_period_start' => $pay_period_start,
    'pay_period_end' => $pay_period_end,
    'department_name' => $department_name
]);
$pdf->AddPage();

// Layout Constants
$name_w = 7;      // Increased from 7 for wider Name column
$earn_w = 13.5;    // Increased from 11.5 for wider Earnings column
$ded_w = 61;       // Slightly reduced to compensate
$gov_w = 12; 
$net_w = 3.5; 
$sig_w = 3.5;

$earn_sub_w = $earn_w / 4;
// --- DYNAMIC: Use filtered deduction count for column width calculation ---
$active_ded_count = count($filtered_deduction_headers);
$ded_sub_w = $ded_w / $active_ded_count;
$gov_sub_w = $gov_w / 4;

// ==========================================================
// HTML TABLE GENERATION
// ==========================================================
$html = '
<style>
    table { border-collapse: collapse; width: 100%; }
    th { border: 0.5pt solid black; background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle; font-size: 9pt; }
    td { border: 0.5pt solid black; vertical-align: middle; }
    .sub-header { font-size: 7pt; font-weight: normal; background-color: #fafafa; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total-row { font-weight: bold; background-color: #eeeeee; font-size: 7pt; }
</style>
<table cellpadding="2">
    <thead>
        <tr>
            <th rowspan="2" width="'.$name_w.'%">EMPLOYEE</th>
            <th colspan="4" width="'.$earn_w.'%">EARNINGS</th>
            <th colspan="'.$active_ded_count.'" width="'.$ded_w.'%">DEDUCTIONS</th>
            <th colspan="4" width="'.$gov_w.'%">GOV SHARES</th>
            <th rowspan="2" width="'.$net_w.'%">NET</th>
            <th rowspan="2" width="'.$sig_w.'%"><span style="font-size: 5pt;">SIGNATURE</span></th>
        </tr>
        <tr>
            <th class="sub-header" width="'.$earn_sub_w.'%">Basic</th>
            <th class="sub-header" width="'.$earn_sub_w.'%">PERA</th>
            <th class="sub-header" width="'.$earn_sub_w.'%">Others</th>
            <th class="sub-header" width="'.$earn_sub_w.'%">Gross</th>';
            // --- DYNAMIC: Loop through filtered deduction headers only ---
            foreach($filtered_deduction_headers as $h) {
                $html .= '<th class="sub-header" width="'.$ded_sub_w.'%">'.strtoupper($h).'</th>';
            }
            $html .= '
            <th class="sub-header" width="'.$gov_sub_w.'%">L/R</th>
            <th class="sub-header" width="'.$gov_sub_w.'%">HDMF</th>
            <th class="sub-header" width="'.$gov_sub_w.'%">PHIC</th>
            <th class="sub-header" width="'.$gov_sub_w.'%">ECC</th>
        </tr>
    </thead>
    <tbody>';

$total_basic = 0; $total_pera = 0; $total_others = 0; $total_gross = 0; 
// --- DYNAMIC: Use active deduction count for totals array ---
$total_ded_cols = array_fill(0, $active_ded_count, 0);
$total_gov_cols = array_fill(0, 4, 0);
$total_net = 0;

foreach ($payrollData as $index => $row) {
    $count = $index + 1;
    $html .= '<tr nobr="true">
                <td width="'.$name_w.'%" style="font-size:8pt;">'.$count.'.) <b>'.$row['name'].'</b><br><i style="font-size:7pt;">'.$row['designation'].'</i></td>';
    
    // Earnings
    foreach ($row['earnings'] as $ei => $val) {
        $html .= '<td class="text-right" width="'.$earn_sub_w.'%" style="font-size:7pt;">' . ($val['amount'] != 0 ? number_format($val['amount'], 2) : '-') . '</td>';
        if($val['label'] == 'Basic') $total_basic += $val['amount'];
        if($val['label'] == 'PERA') $total_pera += $val['amount'];
        if($val['label'] == 'Others') $total_others += $val['amount'];
        if($val['label'] == 'Gross') $total_gross += $val['amount'];
    }
    // Deductions
    foreach ($row['deductions'] as $di => $val) {
        // Only include deductions that are in the filtered list
        if (isset($deduction_index_map[$di])) {
            $new_di = $deduction_index_map[$di];
            $html .= '<td class="text-right" width="'.$ded_sub_w.'%" style="font-size:7pt;">' . ($val['amount'] != 0 ? number_format($val['amount'], 2) : '-') . '</td>';
            $total_ded_cols[$new_di] += $val['amount'];
        }
    }
    // Gov Shares
    foreach ($row['govshares'] as $gi => $val) {
        $html .= '<td class="text-right" width="'.$gov_sub_w.'%" style="font-size:7pt;">' . ($val['amount'] != 0 ? number_format($val['amount'], 2) : '-') . '</td>';
        $total_gov_cols[$gi] += $val['amount'];
    }
    
    $total_net += $row['net'];
    $html .= '<td class="text-right" width="'.$net_w.'%" style="font-size:7pt;"><b>'.number_format($row['net'], 2).'</b></td>
              <td width="'.$sig_w.'%" class="text-left" style="font-size:7pt;">'.$count.'.)</td>
            </tr>';
}

// TOTAL ROW - Aligned with filtered deduction columns
$html .= '<tr class="total-row">
            <td class="text-right" width="'.$name_w.'%" style="font-size:7pt;">TOTAL</td>
            <td class="text-right" width="'.$earn_sub_w.'%" style="font-size:7pt;">'.number_format($total_basic, 2).'</td>
            <td class="text-right" width="'.$earn_sub_w.'%" style="font-size:7pt;">'.number_format($total_pera, 2).'</td>
            <td class="text-right" width="'.$earn_sub_w.'%" style="font-size:7pt;">'.number_format($total_others, 2).'</td>
            <td class="text-right" width="'.$earn_sub_w.'%" style="font-size:7pt;">'.number_format($total_gross, 2).'</td>';
            // --- DYNAMIC: Loop through filtered deduction totals only ---
            foreach($total_ded_cols as $val) {
                $html .= '<td class="text-right" width="'.$ded_sub_w.'%" style="font-size:7pt;">'.($val != 0 ? number_format($val, 2) : '-').'</td>';
            }
            foreach($total_gov_cols as $val) {
                $html .= '<td class="text-right" width="'.$gov_sub_w.'%" style="font-size:7pt;">'.($val != 0 ? number_format($val, 2) : '-').'</td>';
            }
$html .= '  <td class="text-right" width="'.$net_w.'%" style="font-size:7pt;">'.number_format($total_net, 2).'</td>
            <td width="'.$sig_w.'%"></td>
          </tr>';

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

// ==========================================================
// SIGNATORIES
// ==========================================================
$pdf->Ln(5);
if($pdf->GetY() > 160) { $pdf->AddPage(); }

$signatories = $Payroll_Signatory->FetchActiveSignatoriesByReportType('PAYROLL');
$sig_html = '<table border="0" cellpadding="5" cellspacing="0" width="100%"><tr>';
$col_count = 0;
foreach ($signatories as $sign) {
    if ($sign['role_type'] === 'HEAD' && $sign['dept_id'] != $dept_id) continue;
    if ($col_count > 0 && $col_count % 4 == 0) $sig_html .= '</tr><tr><td colspan="4" height="15"></td></tr><tr>';
    $sig_html .= '<td width="25%" align="center">
                    <span style="font-size:8pt;">'.$sign['sign_particulars'].'</span><br><br><br>
                    <span style="font-size:10pt;"><b>'.$sign['full_name'].'</b></span><br>
                    <span style="font-size:8pt;">'.$sign['position_title'].'</span>
                  </td>';
    $col_count++;
}
while($col_count % 4 != 0) { $sig_html .= '<td width="25%"></td>'; $col_count++; }
$sig_html .= '</tr></table>';
$pdf->writeHTML($sig_html, true, false, true, false, '');

ob_end_clean();
$pdf->Output('municipal_payroll.pdf', 'I');
?>