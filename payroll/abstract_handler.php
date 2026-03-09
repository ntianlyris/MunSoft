<?php
header('Content-Type: application/json');
require_once '../includes/class/DB_conn.php';
require_once '../includes/class/Payroll.php';

$db = new DB_conn();
$Payroll = new Payroll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'fetch_abstract') {
        $period_id = $_POST['period_id'] ?? '';
        $dept_id = $_POST['dept_id'] ?? 'all';
        $employment_type = $_POST['employment_type'] ?? 'all';

        if (empty($period_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Period is required.']);
            exit;
        }

        $query = "SELECT earnings_breakdown, deductions_breakdown, govshares_breakdown, gross, total_deductions, net_pay 
                  FROM payroll_entries 
                  WHERE payroll_period_id = '$period_id'";
        
        if ($dept_id !== 'all') {
            $query .= " AND dept_id = '" . $db->escape_string($dept_id) . "'";
        }
        if ($employment_type !== 'all') {
            $query .= " AND emp_type_stamp = '" . $db->escape_string($employment_type) . "'";
        }

        $result = $db->query($query);
        
        $earnings_map = [];
        $deductions_map = [];
        $govshares_map = [];
        
        $totals = ['gross' => 0, 'deductions' => 0, 'net' => 0, 'govshares' => 0];

        // Config mappings
        $config_e = [];
        $res_e = $db->query("SELECT earning_code, earning_title, earning_acct_code FROM config_earnings");
        while($re = $res_e->fetch_assoc()) { $config_e[$re['earning_code']] = $re; }

        $config_d = [];
        $res_d = $db->query("SELECT deduct_code, deduct_title, deduct_acct_code FROM config_deductions");
        while($rd = $res_d->fetch_assoc()) { $config_d[$rd['deduct_code']] = $rd; }

        $config_g = [];
        $res_g = $db->query("SELECT govshare_code, govshare_name, govshare_acctcode FROM govshares");
        while($rg = $res_g->fetch_assoc()) { $config_g[$rg['govshare_code']] = $rg; }

        while ($row = $result->fetch_assoc()) {
            $totals['gross'] += floatval($row['gross']);
            $totals['deductions'] += floatval($row['total_deductions']);
            $totals['net'] += floatval($row['net_pay']);

            // Parse Earnings
            $e_list = json_decode($row['earnings_breakdown'], true) ?: [];
            foreach ($e_list as $e) {
                $code = $e['label'];
                if (!isset($earnings_map[$code])) {
                    $earnings_map[$code] = [
                        'title' => $config_e[$code]['earning_title'] ?? $code,
                        'acct' => $config_e[$code]['earning_acct_code'] ?? '',
                        'amount' => 0
                    ];
                }
                $earnings_map[$code]['amount'] += floatval($e['amount']);
            }

            // Parse Deductions
            $d_list = json_decode($row['deductions_breakdown'], true) ?: [];
            foreach ($d_list as $d) {
                $code = $d['label'];
                if (!isset($deductions_map[$code])) {
                    $deductions_map[$code] = [
                        'title' => $config_d[$code]['deduct_title'] ?? $code,
                        'acct' => $config_d[$code]['deduct_acct_code'] ?? '',
                        'amount' => 0
                    ];
                }
                $deductions_map[$code]['amount'] += floatval($d['amount']);
            }

            // Parse GovShares
            $g_list = json_decode($row['govshares_breakdown'], true) ?: [];
            foreach ($g_list as $g) {
                $code = $g['label'];
                if (!isset($govshares_map[$code])) {
                    $govshares_map[$code] = [
                        'title' => $config_g[$code]['govshare_name'] ?? $code,
                        'acct' => $config_g[$code]['govshare_acctcode'] ?? '',
                        'amount' => 0
                    ];
                }
                $govshares_map[$code]['amount'] += floatval($g['amount']);
                $totals['govshares'] += floatval($g['amount']);
            }
        }

        if ($totals['gross'] == 0 && empty($earnings_map)) {
            echo json_encode(['status' => 'error', 'message' => 'No payroll records found for this selection.']);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'data' => [
                'earnings' => array_values($earnings_map),
                'deductions' => array_values($deductions_map),
                'govshares' => array_values($govshares_map),
                'totals' => $totals
            ]
        ]);
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
