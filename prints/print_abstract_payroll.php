<?php
ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

require_once('../includes/tcpdf/tcpdf.php');
require_once('../includes/view/functions.php');
require_once('../includes/class/DB_conn.php');
require_once('../includes/class/Payroll.php');
require_once('../includes/class/Signatory.php');

$db = new DB_conn();
$Payroll = new Payroll();
$Signatory = new Signatory();

$period_id = $_GET['period_id'] ?? '';
$dept_id_filter = $_GET['dept_id'] ?? 'all';
$emp_type_filter = $_GET['employment_type'] ?? 'all';

if (empty($period_id)) {
    die("Error: Payroll period is required.");
}

// Fetch Period info
$periodInfo = $Payroll->GetPayrollPeriodByID($period_id);
if (!$periodInfo) {
    die("Error: Payroll period not found.");
}

// Fetch Abstract Data
$query = "SELECT earnings_breakdown, deductions_breakdown, govshares_breakdown, gross, total_deductions, net_pay 
          FROM payroll_entries 
          WHERE payroll_period_id = '$period_id'";

if ($dept_id_filter !== 'all') {
    $query .= " AND dept_id = '" . $db->escape_string($dept_id_filter) . "'";
}
if ($emp_type_filter !== 'all') {
    $query .= " AND emp_type_stamp = '" . $db->escape_string($emp_type_filter) . "'";
}

$result = $db->query($query);
$earnings_map = []; $deductions_map = []; $govshares_map = [];
$totals = ['gross' => 0, 'deductions' => 0, 'net' => 0, 'govshares' => 0];

// Config mappings
$config_e = []; $res_e = $db->query("SELECT earning_code, earning_title, earning_acct_code FROM config_earnings");
while($re = $res_e->fetch_assoc()) { $config_e[$re['earning_code']] = $re; }
$config_d = []; $res_d = $db->query("SELECT deduct_code, deduct_title, deduct_acct_code FROM config_deductions");
while($rd = $res_d->fetch_assoc()) { $config_d[$rd['deduct_code']] = $rd; }
$config_g = []; $res_g = $db->query("SELECT govshare_code, govshare_name, govshare_acctcode FROM govshares");
while($rg = $res_g->fetch_assoc()) { $config_g[$rg['govshare_code']] = $rg; }

while ($row = $result->fetch_assoc()) {
    $totals['gross'] += floatval($row['gross']);
    $totals['deductions'] += floatval($row['total_deductions']);
    $totals['net'] += floatval($row['net_pay']);

    $e_list = json_decode($row['earnings_breakdown'], true) ?: [];
    foreach ($e_list as $e) {
        $code = $e['label'];
        if (!isset($earnings_map[$code])) {
            $earnings_map[$code] = ['title' => $config_e[$code]['earning_title'] ?? $code, 'acct' => $config_e[$code]['earning_acct_code'] ?? '', 'amount' => 0];
        }
        $earnings_map[$code]['amount'] += floatval($e['amount']);
    }

    $d_list = json_decode($row['deductions_breakdown'], true) ?: [];
    foreach ($d_list as $d) {
        $code = $d['label'];
        if (!isset($deductions_map[$code])) {
            $deductions_map[$code] = ['title' => $config_d[$code]['deduct_title'] ?? $code, 'acct' => $config_d[$code]['deduct_acct_code'] ?? '', 'amount' => 0];
        }
        $deductions_map[$code]['amount'] += floatval($d['amount']);
    }

    $g_list = json_decode($row['govshares_breakdown'], true) ?: [];
    foreach ($g_list as $g) {
        $code = $g['label'];
        if (!isset($govshares_map[$code])) {
            $govshares_map[$code] = ['title' => $config_g[$code]['govshare_name'] ?? $code, 'acct' => $config_g[$code]['govshare_acctcode'] ?? '', 'amount' => 0];
        }
        $govshares_map[$code]['amount'] += floatval($g['amount']);
        $totals['govshares'] += floatval($g['amount']);
    }
}

// Setup PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('MunSoft Payroll System');
$pdf->SetTitle('Abstract of Payroll');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage();

// Header
$pdf->Image('../includes/images/polanco_logo.jpg', 30, 15, 20, 20);
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 7, 'Republic of the Philippines', 0, 1, 'C');
$pdf->Cell(0, 7, 'Province of Zamboanga del Norte', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 7, 'Municipality of Polanco', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'CONSOLIDATED ABSTRACT OF PAYROLL', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'For the Period: ' . $periodInfo['period_label'], 0, 1, 'C');
if ($dept_id_filter !== 'all') {
    $deptInfo = $db->query("SELECT dept_title FROM departments_tbl WHERE dept_id = '$dept_id_filter'")->fetch_assoc();
    $pdf->Cell(0, 6, 'Department: ' . ($deptInfo['dept_title'] ?? $dept_id_filter), 0, 1, 'C');
} else {
    $pdf->Cell(0, 6, 'Department: All Departments', 0, 1, 'C');
}
$pdf->Ln(5);

// Build Table
$earnings = array_values($earnings_map);
$deductions = array_values($deductions_map);
$govshares = array_values($govshares_map);

$html = '
<style>
    th { color: #000; font-weight: bold; background-color: #f2f2f2; text-align: center; border: 0.5px solid #000; }
    td { border: 0.5px solid #000; }
    .cat-head { background-color: #f8f9fa; font-weight: bold; }
    .total-row { background-color: #f2f2f2; font-weight: bold; }
</style>
<table cellpadding="4" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th width="50%">PARTICULARS</th>
            <th width="20%">ACCOUNT CODE</th>
            <th width="30%">AMOUNT</th>
        </tr>
    </thead>
    <tbody>
        <tr class="cat-head"><td colspan="3">I. EARNINGS</td></tr>';

foreach ($earnings as $e) {
    $html .= '<tr>
                <td width="50%">' . $e['title'] . '</td>
                <td width="20%" align="center">' . $e['acct'] . '</td>
                <td width="30%" align="right">' . number_format($e['amount'], 2) . '</td>
              </tr>';
}
$html .= '<tr class="total-row">
            <td colspan="2" align="right">TOTAL GROSS PAY:</td>
            <td align="right">' . number_format($totals['gross'], 2) . '</td>
          </tr>';

$html .= '<tr class="cat-head"><td colspan="3">II. DEDUCTIONS</td></tr>';
foreach ($deductions as $d) {
    $html .= '<tr>
                <td width="50%">' . $d['title'] . '</td>
                <td width="20%" align="center">' . $d['acct'] . '</td>
                <td width="30%" align="right">' . number_format($d['amount'], 2) . '</td>
              </tr>';
}
$html .= '<tr class="total-row">
            <td colspan="2" align="right">TOTAL DEDUCTIONS:</td>
            <td align="right">' . number_format($totals['deductions'], 2) . '</td>
          </tr>';

$html .= '<tr style="background-color: #343a40; color: #ffffff; font-weight: bold;">
            <td colspan="2" align="right">TOTAL NET PAY:</td>
            <td align="right">' . number_format($totals['net'], 2) . '</td>
          </tr>';

$html .= '<tr class="cat-head"><td colspan="3">III. GOV\'T SHARES (Employer Share)</td></tr>';
foreach ($govshares as $g) {
    $html .= '<tr>
                <td width="50%">' . $g['title'] . '</td>
                <td width="20%" align="center">' . $g['acct'] . '</td>
                <td width="30%" align="right">' . number_format($g['amount'], 2) . '</td>
              </tr>';
}
$html .= '<tr class="total-row">
            <td colspan="2" align="right">TOTAL GOV\'T SHARES:</td>
            <td align="right">' . number_format($totals['govshares'], 2) . '</td>
          </tr>';

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Signatories
$pdf->Ln(15);
$signatories = $Signatory->FetchActiveSignatoriesByReportType('ACCTG');
if (!empty($signatories)) {
    $pdf->SetFont('helvetica', '', 9);
    $sig_html = '<table border="0" cellpadding="4" width="100%"><tr>';
    $col_width = floor(100 / count($signatories)) . '%';
    foreach ($signatories as $sig) {
        $sig_html .= '<td width="' . $col_width . '">
                        <br>' . $sig['sign_particulars'] . '<br><br><br>
                        <b>' . $sig['full_name'] . '</b><br>
                        <span>' . $sig['position_title'] . '</span>
                      </td>';
    }
    $sig_html .= '</tr></table>';
    $pdf->writeHTML($sig_html, true, false, true, false, '');
}

ob_end_clean();
$pdf->Output('Abstract_of_Payroll.pdf', 'I');
?>
