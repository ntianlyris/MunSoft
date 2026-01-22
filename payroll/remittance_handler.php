<?php
require_once '../includes/class/Remittance.php';

if($action = isset($_POST['action'])?$_POST['action']:'') {
    switch ($action) {
        case 'fetch_remit_month_periods_of_year':
            require_once '../includes/class/Payroll.php';
            $Payroll = new Payroll();
            $year = intval($_POST['year']);

            $frequency = 'monthly'; // remittance is always monthly
            $periods = $Payroll->GetPayrollPeriodsByYear($year,$frequency);

            $data = [];
            foreach ($periods as $period) {
                $data[] = [
                    "payroll_period" => $period['date_start'] . '_' . $period['date_end'],
                    "period_label"      => $period['period_label']
                ];
            }
            header('Content-Type: application/json');
            echo json_encode($data);
            break;

        case 'generate_remittance':
            $Remittance = new Remittance();

            $year = $_POST['year'];
            $period = $_POST['period'];
            //$dept = $_POST['dept'];

            $data = [
                "philhealth" => $Remittance->GetRemittancePhilHealth($year, $period),    //($year, $period, $dept) in case department field is added in payroll_entries table in the future
                "tax"        => $Remittance->GetRemittanceBIRTax($year, $period),
                "gsis"       => $Remittance->GetRemittanceGSIS($year, $period),
                "gsis_ecc"   => $Remittance->GetRemittanceGSISECC($year, $period),
                "pagibig"    => $Remittance->GetRemittancePagibig($year, $period),
                "sss"        => $Remittance->GetRemittanceSSS($year, $period),
                "loans"      => $Remittance->GetRemittanceLoans($year, $period),
                "others"     => $Remittance->GetRemittanceOthers($year, $period)
            ];

            echo json_encode($data);
            //print_r($data);
            break;

        case 'get_loan_breakdown':
            $Remittance = new Remittance();

            $loanId = $_POST['loan_id'];
            $period = $_POST['pay_period'];
            //$dept = $_POST['dept'];

            $loan_breakdown = $Remittance->GetRemittanceLoanBreakdown($loanId, $period);
            if($loan_breakdown){
                echo json_encode($loan_breakdown);
            }
            else {
                echo json_encode([]);
            }
            break;

        case 'save_remittance':
            $Remittance = new Remittance();

            $year   = $_POST['year'];
            $period = $_POST['period']; // format "YYYY-MM-DD_YYYY-MM-DD"
            $status = $_POST['status'];
            $orNo   = $_POST['or_number'] ?? null;
            $refNo  = $_POST['reference_no'] ?? null;

            list($startDate, $endDate) = explode("_", $period);
            $data = [
                "philhealth" => $Remittance->GetRemittancePhilHealth($year, $period),    //($year, $period, $dept) in case department field is added in payroll_entries table in the future
                "tax"        => $Remittance->GetRemittanceBIRTax($year, $period),
                "gsis"       => $Remittance->GetRemittanceGSIS($year, $period),
                "gsis_ecc"   => $Remittance->GetRemittanceGSISECC($year, $period),
                "pagibig"    => $Remittance->GetRemittancePagibig($year, $period),
                "sss"        => $Remittance->GetRemittanceSSS($year, $period),
                "loans"      => $Remittance->GetRemittanceLoans($year, $period),
                "others"     => $Remittance->GetRemittanceOthers($year, $period)
            ];

            // Build array of remittance_type => total_amount
            // Compute totals for each deduction type
            $totals = [
                'tax'        => array_sum(array_column($data['tax'], 'amount')),
                'gsis'       => array_sum(array_column($data['gsis'], 'total_amount')),
                'gsis_ecc'   => array_sum(array_column($data['gsis_ecc'], 'employer_share')), // if needed separately
                'philhealth' => array_sum(array_column($data['philhealth'], 'total')),
                'pagibig'    => array_sum(array_column($data['pagibig'], 'total')),
                'sss'        => array_sum(array_column($data['sss'], 'employee_share')),
                'loans'      => array_sum(array_column($data['loans'], 'total_loan_amount')),
                'others'     => array_sum(array_column($data['others'], 'total_amount')),
            ];

            $employee_totals = [
                'tax'        => array_sum(array_column($data['tax'], 'amount')),
                'gsis'       => array_sum(array_column($data['gsis'], 'employee_share')),
                'gsis_ecc'   => array_sum(array_column($data['gsis_ecc'], 'employee_share')),
                'philhealth' => array_sum(array_column($data['philhealth'], 'employee_share')),
                'pagibig'    => array_sum(array_column($data['pagibig'], 'employee_share')),
                'sss'        => array_sum(array_column($data['sss'], 'employee_share')),
                'loans'      => array_sum(array_column($data['loans'], 'total_loan_amount')),
                'others'     => array_sum(array_column($data['others'], 'total_amount')),
            ];

            $employer_totals = [
                'gsis'       => array_sum(array_column($data['gsis'], 'employer_share')),
                'gsis_ecc'   => array_sum(array_column($data['gsis_ecc'], 'employer_share')),
                'philhealth' => array_sum(array_column($data['philhealth'], 'employer_share')),
                'pagibig'    => array_sum(array_column($data['pagibig'], 'employer_share')),
                'sss'        => array_sum(array_column($data['sss'], 'employer_share')),
                // Loans and Tax usually do not have employer share
            ];

            // Build details array for remittance_details table
            $details = [];

            // PhilHealth
            $details['philhealth'] = array_map(function($row) {
                return [
                    'employee_id' => $row['employee_id'],
                    'config_deduction_id' => $row['config_deduction_id'] ?? null, // Add this line
                    'govshare_id' => $row['govshare_id'] ?? null, // Add this line
                    'amount'      => $row['employee_share'],
                    'employer_share' => $row['employer_share'] ?? 0 // Ensure employer share is included
                ];
            }, $data['philhealth']);

            // Tax
            $details['tax'] = array_map(function($row) {
                return [
                    'employee_id' => $row['employee_id'],
                    'config_deduction_id' => $row['config_deduction_id'] ?? null, // Add this line
                    'govshare_id' => $row['govshare_id'] ?? null, // Add this line
                    'amount'      => $row['amount'],
                    'employer_share' => 0 // Tax usually does not have employer share
                ];
            }, $data['tax']);

            // GSIS
            $details['gsis'] = array_map(function($row) {
                return [
                    'employee_id' => $row['employee_id'],
                    'config_deduction_id' => $row['config_deduction_id'] ?? null, // Add this line
                    'govshare_id' => $row['govshare_id'] ?? null, // Add this line
                    'amount'      => $row['employee_share'],
                    'employer_share' => $row['employer_share'] ?? 0 // Ensure employer share is included
                ];
            }, $data['gsis']);

            // GSIS ECC (employer share only, no employee share)
            $details['gsis_ecc'] = array_map(function($row) {
                return [
                    'employee_id' => $row['employee_id'],
                    'config_deduction_id' => $row['config_deduction_id'] ?? null, // Add this line
                    'govshare_id' => $row['govshare_id'] ?? null, // Add this line
                    'amount'      => 0, // No employee share for ECC
                    'employer_share' => $row['employer_share'] ?? 0 // Ensure employer share is included
                ];
            }, $data['gsis_ecc']);

            // Pag-IBIG
            $details['pagibig'] = array_map(function($row) {
                return [
                    'employee_id' => $row['employee_id'],
                    'config_deduction_id' => $row['config_deduction_id'] ?? null, // Add this line
                    'govshare_id' => $row['govshare_id'] ?? null, // Add this line
                    'amount'      => $row['employee_share'],
                    'employer_share' => $row['employer_share'] ?? 0 // Ensure employer share is included
                ];
            }, $data['pagibig']);

            // SSS
            $details['sss'] = array_map(function($row) {
                return [
                    'employee_id' => $row['employee_id'],
                    'config_deduction_id' => $row['config_deduction_id'] ?? null, // Add this line
                    'govshare_id' => $row['govshare_id'] ?? null, // Add this line
                    'amount'      => $row['employee_share'],
                    'employer_share' => 0 // Ensure employer share is included
                ];
            }, $data['sss']);

            // Loans: handle multiple loan types and employees
            /*foreach ($data['loans'] as $loan) {
                $loan_id = $loan['loan_id'];
                $deduct_code = $loan['deduct_code']; // Use as remittance_type label
                // Get breakdown for this loan type
                $loan_breakdown = $Remittance->GetRemittanceLoanBreakdown($loan_id, $period);
                foreach ($loan_breakdown as $employee_loan) {
                    $details[$deduct_code][] = [
                        'employee_id' => $employee_loan['employee_id'],
                        'amount'      => $employee_loan['total_deduction']
                    ];
                }
            }*/

            $remittance_arr = [
                "period" => $period,
                "deductions" => $totals,
                "employee_totals" => $employee_totals,
                "employer_totals" => $employer_totals,
                "details" => $details,
                "loans" => $data['loans'],                  // <-- Pass the loans array here
                "other_deductions" => $data['others']       // <-- Pass the other deductions array here
            ];

            $success = $Remittance->SaveRemittances($remittance_arr, $status, $orNo, $refNo);

            echo json_encode([
                "success" => $success ? true : false,
                "message" => $success ? "Remittance saved successfully." : "Failed to save remittance."
            ]);
            break;
        
        case 'get_remittances':
            require_once '../includes/view/functions.php';

            $year = $_POST['year'];
            $type = $_POST['type'];

            $rows = [];

            $Remittance = new Remittance();
            $remittances = $Remittance->getRemittancesByYearAndType($year, $type);

            if (!empty($remittances) && is_array($remittances)) {
                foreach ($remittances as $row) {
                    $rows[] = "
                        <tr>
                            <td>" . OutputShortDate($row['period_start']) . ' - ' . OutputShortDate($row['period_end']) . "</td>
                            <td>" . strtoupper($row['remittance_type']) . "</td>
                            <td>{$row['or_number']}</td>
                            <td>{$row['reference_no']}</td>
                            <td class='text-right'>" . OutputMoney($row['employee_totals']) . "</td>
                            <td class='text-right'>" . OutputMoney($row['employer_totals']) . "</td>
                            <td class='text-right'>" . OutputMoney($row['total_amount']) . "</td>
                            <td class='text-center'>" . ($row['status'] == 'Remitted' ? "<span class='badge badge-success'>Remitted</span>" : "<span class='badge badge-warning'>Pending</span>") . "</td>
                            <td class='text-center'>
                                <button class='btn btn-info btn-sm viewRemittance' 
                                    data-id='{$row['remittance_id']}'
                                    data-type='{$row['remittance_type']}'
                                    data-period='{$row['period_start']}_{$row['period_end']}'>
                                        <i class='fas fa-eye'></i> View Details
                                </button>
                            </td>
                        </tr>
                    ";
                }
            } else {
                $rows[] = "<tr><td colspan='7' class='text-center text-muted'>No remittances found</td></tr>";
            }

            echo implode('', $rows);
            break;

        case 'view_remittance':
            require_once '../includes/view/functions.php';

            $Remittance = new Remittance();

            $remittance_id = $_POST['remittance_id'] ?? null;
            $remittance_type = $_POST['remittance_type'] ?? '';
            $period = $_POST['period'] ?? '';

            // If remittance_type indicates loans, stop here because loans are handled separately
            if ($remittance_type === 'loans') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Loans are handled separately',
                    'type' => 'loans'
                ]);
                exit;
            }

            // If no remittance id was provided, return error
            if (empty($remittance_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No remittance specified',
                    'html' => "<tr><td colspan='4' class='text-center text-muted'>No remittance specified</td></tr>"
                ]);
                break;
            }

            $details = $Remittance->getRemittanceDetails($remittance_id);

            $rows = [];
            $response = [
                'success' => true,
                'type' => $remittance_type,
                'period' => $period,
                'html' => '',
                'totals' => [
                    'employee' => 0,
                    'employer' => 0
                ]
            ];

            if (!empty($details) && is_array($details)) {
                $employee_totals = 0;
                $employer_totals = 0;
                foreach ($details as $row) {
                    $employee_totals += $row['employee_share'];
                    $employer_totals += $row['employer_share'];
                    $rows[] = "
                        <tr>
                            <td>{$row['employee_name']}</td>
                            <td>{$row['remittance_title']}</td>
                            <td class='text-right'>" . ($row['employee_share'] != 0 ? OutputMoney($row['employee_share']) : ' ') . "</td>
                            <td class='text-right'>" . ($row['employer_share'] != 0 ? OutputMoney($row['employer_share']) : ' ') . "</td>
                        </tr>
                    ";
                }
                // Add totals row
                $rows[] = "
                    <tr class='font-weight-bold'>
                        <td colspan='2' class='text-right'>TOTAL:</td>
                        <td class='text-right'>" . OutputMoney($employee_totals) . "</td>
                        <td class='text-right'>" . OutputMoney($employer_totals) . "</td>
                    </tr>";

                $response['totals']['employee'] = $employee_totals;
                $response['totals']['employer'] = $employer_totals;
            } else {
                $rows[] = "<tr><td colspan='4' class='text-center text-muted'>No remittance details found</td></tr>";
            }

            $response['html'] = implode('', $rows);
            
            header('Content-Type: application/json');
            echo json_encode($response);
            break;

        case 'view_loans_remittance':
            require_once '../includes/view/functions.php';

            $Remittance = new Remittance();

            $remittance_id = $_POST['remittance_id'];
            $remit_details = $Remittance->getRemittanceById($remittance_id);
            $period = $remit_details ? $remit_details['period_start'] . '_' . $remit_details['period_end'] : '';
            $loan_details = $Remittance->getLoansRemittanceDetails($remittance_id);
            $rows = [];

            if (!empty($loan_details) && is_array($loan_details)) {
                $grand_total = 0;
                foreach ($loan_details as $loan) {
                    $grand_total += $loan['total_amount'];
                    $rows[] = "
                        <tr>
                            <td>{$loan['loan_title']}</td>
                            <td class='text-right'>" . OutputMoney($loan['total_amount']) . "</td>
                            <td class='text-center'>
                                <button class='btn btn-info btn-sm viewLoanRemittanceBreakdown' 
                                    data-loan-type='{$loan['loan_type']}'
                                    data-loan-title='" . htmlspecialchars($loan['loan_title'], ENT_QUOTES, 'UTF-8') . "'
                                    data-period='{$period}'
                                    data-employees='" . htmlspecialchars(json_encode($loan['employees']), ENT_QUOTES, 'UTF-8') . "'>
                                    <i class='fas fa-eye'></i> Breakdown
                                </button>
                            </td>
                        </tr>
                    ";
                }
                // Add grand total row
                $rows[] = "
                    <tr class='font-weight-bold'>
                        <td class='text-right'>GRAND TOTAL:</td>
                        <td class='text-right'>" . OutputMoney($grand_total) . "</td>
                        <td></td>
                    </tr>";
            } else {
                $rows[] = "<tr><td colspan='3' class='text-center text-muted'>No loan remittance details found</td></tr>";
            }

            // Return JSON including period for frontend handling
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'period'  => $period,
                'html'    => implode('', $rows)
            ]);
            break;

        case 'view_others_remittance':
            require_once '../includes/view/functions.php';

            $Remittance = new Remittance();

            $remittance_id = $_POST['remittance_id'] ?? null;
            $remit_details = $Remittance->getRemittanceById($remittance_id);
            $period = $remit_details ? $remit_details['period_start'] . '_' . $remit_details['period_end'] : '';

            // If no remittance id was provided, return error
            if (empty($remittance_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No remittance specified',
                    'html' => "<tr><td colspan='3' class='text-center text-muted'>No remittance specified</td></tr>"
                ]);
                break;
            }

            $details = $Remittance->getOthersRemittanceDetails($remittance_id);

            $rows = [];
            if (!empty($details) && is_array($details)) {
                foreach ($details as $row) {
                    $rows[] = "
                        <tr>
                            <td>{$row['deduct_title']}</td>
                            <td class='text-right'>" . OutputMoney($row['total_amount']) . "</td>
                            <td class='text-center'>
                                <button class='btn btn-info btn-sm viewOtherRemittanceBreakdown' 
                                    data-deduction-id='{$row['config_deduction_id']}'
                                    data-deduction-title='" . htmlspecialchars($row['deduct_title'], ENT_QUOTES, 'UTF-8') . "'
                                    data-period='{$period}'
                                    data-employees='" . htmlspecialchars(json_encode($row['employees']), ENT_QUOTES, 'UTF-8') . "'>
                                    <i class='fas fa-eye'></i> Breakdown
                                </button>
                            </td>
                        </tr>
                    ";
                }
            } else {
                $rows[] = "<tr><td colspan='3' class='text-center text-muted'>No other deductions remittance details found</td></tr>";
            }
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'html' => implode('', $rows)
            ]);
            break;

        default:
            # code...
            break;
    }
}



?>