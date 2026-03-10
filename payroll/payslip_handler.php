<?php
require_once '../includes/class/Payroll.php';

if($action = isset($_POST['action'])?$_POST['action']:'') {
    switch($action) {
        case 'fetch_payslip_month_periods_of_year':
            $Payroll = new Payroll();

            $year = isset($_POST['year']) ? $_POST['year'] : '';

            $frequency = 'monthly'; // payslip is always monthly
            $periods = $Payroll->GetPayrollPeriodsByYear($year,$frequency);
            
            $data = [];
            foreach ($periods as $period) {
                $data[] = [
                    "period_id" => $period['payroll_period_id'],
                    "payroll_period" => $period['date_start'] . '_' . $period['date_end'],
                    "period_label" => $period['period_label']
                ];
            }
            header('Content-Type: application/json');
            echo json_encode($data);
            break;

        case'generate_payslip':
            require_once '../includes/class/Payslip.php';
            $PaySlip = new Payslip();

            $employee_id = isset($_POST['payslipEmployee']) ? $_POST['payslipEmployee'] : '';
            $year = isset($_POST['payslipYear']) ? $_POST['payslipYear'] : '';
            $payroll_period = isset($_POST['payslipPeriod']) ? $_POST['payslipPeriod'] : '';
            
            // Re-use logic to block restricted unapproved / time-locked payslips
            $is_downloadable = $PaySlip->IsPayslipDownloadable($employee_id, $payroll_period);
            if (!$is_downloadable) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Payslip is locked and not yet available for downloading/printing. Ensure payroll is approved and the period has ended.']);
                exit;
            }
            
            $payslip = $PaySlip->GeneratePayslip($employee_id, $year, $payroll_period);
            header('Content-Type: application/json');
            echo json_encode($payslip);
            break;
    }
}