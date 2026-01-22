<?php 
	include_once("DB_conn.php");
    include_once("Payroll.php");

	class JournalEntry {

		protected $db;
        private $PayrollData = array();
        private $NetPayTotal = 0;
        private $NetPayCreditAcctCode = '';
        private $NetPayCreditAcctTitle = '';
        private $SalaryTypeAcctCode = '';
        private $SalaryTypes = ['Regular' => '50101010', 'Casual' => '50101020'];

        public function __construct() {
        	$this->db = new DB_conn();	
            $this->NetPayCreditAcctCode = '20101020'; 
            $this->NetPayCreditAcctTitle = 'Due to Officers and Employees';
    	}

        public function setPayrollData($data) {
            $this->PayrollData = $data;
        }

        public function setSalaryTypeAcctCodeByEmploymentType($employment_type) {
            if (isset($this->SalaryTypes[$employment_type])) {
                $this->SalaryTypeAcctCode = $this->SalaryTypes[$employment_type];
            } else {
                $this->SalaryTypeAcctCode = $this->SalaryTypes['Regular'];
            }
        }

        public function getEarningTitleByAccountCode($acct_code) {
            $query = "SELECT earning_title 
                      FROM config_earnings 
                      WHERE earning_acct_code = '".$this->db->escape_string($acct_code)."' 
                      LIMIT 1";
            $result = $this->db->query($query) or die($this->db->error);
            if ($row = $this->db->fetch_array($result)) {
                return $row['earning_title'];
            }
            return '';
        }

        public function setNetPayTotal() {
            $payroll_data = $this->PayrollData;
            $net_pay_total = 0;

            foreach ($payroll_data as $entry) {
                if (isset($entry['net_pay'])) {
                    $net_pay_total += $entry['net_pay'];
                }
            }
            $this->NetPayTotal = $net_pay_total;
        }

        public function GetPayrollEarningsWithAccountCodes() {
            $payroll_data = $this->PayrollData;
            $all_earning_comps = [];

            // Step 1: Collect all earning breakdowns across filtered PayrollData
            foreach ($payroll_data as $entry) {
                if (!empty($entry['earnings_breakdown'])) {
                    $breakdown = json_decode($entry['earnings_breakdown'], true);
                    if (is_array($breakdown)) {
                        foreach ($breakdown as $b) {
                            $earning_comp_id = $b['earning_comp_id'];
                            $amount = isset($b['amount']) ? $b['amount'] : 0;
                            if (!isset($all_earning_comps[$earning_comp_id])) {
                                $all_earning_comps[$earning_comp_id] = [
                                    'earning_comp_id' => $earning_comp_id,
                                    'total_amount' => $amount
                                ];
                            } else {
                                $all_earning_comps[$earning_comp_id]['total_amount'] += $amount;
                            }
                        }
                    }
                }
            }

            if (empty($all_earning_comps)) {
                return [];
            }

            // Step 2: Prepare list of earning_comp_id for lookup
            $ids = implode(",", array_keys($all_earning_comps));

            // Step 3: Query to get earning account codes and titles
            $query = "SELECT eec.earning_component_id, 
                            eec.config_earning_id, 
                            ce.earning_acct_code, 
                            ce.earning_title
                    FROM employee_earnings_components eec
                    INNER JOIN config_earnings ce 
                        ON eec.config_earning_id = ce.config_earning_id
                    WHERE eec.earning_component_id IN ($ids)";
            $result = $this->db->query($query) or die($this->db->error);

            // Step 4: Map account details to earnings
            $acct_map = [];
            while ($row = $this->db->fetch_array($result)) {
                $acct_map[$row['earning_component_id']] = [
                    'config_earning_id' => $row['config_earning_id'],
                    'earning_title' => $row['earning_title'],
                    'earning_acct_code' => $row['earning_acct_code']
                ];
            }

            // Step 5: Merge the account details into the earnings summary
            $final_earnings = [];
            foreach ($all_earning_comps as $comp_id => $data) {
                $merged = [
                    'earning_comp_id' => $comp_id,
                    'total_amount' => $data['total_amount'],
                    'earning_title' => isset($acct_map[$comp_id]) ? $acct_map[$comp_id]['earning_title'] : '',
                    'earning_acct_code' => isset($acct_map[$comp_id]) ? $acct_map[$comp_id]['earning_acct_code'] : ''
                ];
                $final_earnings[] = $merged;
            }

            return $final_earnings;
        }

        public function GetPayrollGovSharesWithAccountCodes() {
            $payroll_data = $this->PayrollData;
            $all_gov_shares = [];

            // Similar implementation as GetPayrollEarningsWithAccountCodes
            // Step 1: Collect all government share breakdowns across filtered PayrollData
            foreach ($payroll_data as $entry) {
                if (!empty($entry['govshares_breakdown'])) {
                    $breakdown = json_decode($entry['govshares_breakdown'], true);
                    if (is_array($breakdown)) {
                        foreach ($breakdown as $b) {
                            $emp_govshare_id = $b['emp_govshare_id'];
                            $amount = isset($b['amount']) ? $b['amount'] : 0;
                            if (!isset($all_gov_shares[$emp_govshare_id])) {
                                $all_gov_shares[$emp_govshare_id] = [
                                    'emp_govshare_id' => $emp_govshare_id,
                                    'total_amount' => $amount
                                ];
                            } else {
                                $all_gov_shares[$emp_govshare_id]['total_amount'] += $amount;
                            }
                        }
                    }
                }
            }

            if (empty($all_gov_shares)) {
                return [];
            }

            // Step 2: Prepare list of emp_govshare_id for lookup
            $ids = implode(",", array_keys($all_gov_shares));

            // Step 3: Query to get government share account codes and titles
            $query = "SELECT eg.employee_govshare_id,
                            eg.govshare_id, 
                            g.govshare_acctcode, 
                            g.govshare_name
                    FROM employee_govshares eg
                    INNER JOIN govshares g 
                        ON eg.govshare_id = g.govshare_id
                    WHERE eg.employee_govshare_id IN ($ids)";
            $result = $this->db->query($query) or die($this->db->error);

            // Step 4: Map account details to government shares
            $acct_map = [];
            while ($row = $this->db->fetch_array($result)) {
                $acct_map[$row['employee_govshare_id']] = [
                    'govshare_id' => $row['govshare_id'],
                    'govshare_name' => $row['govshare_name'],
                    'govshare_acctcode' => $row['govshare_acctcode']
                ];
            }

            // Step 5: Merge the account details into the government shares summary
            $final_gov_shares = [];
            foreach ($all_gov_shares as $share_id => $data) {
                $merged = [
                    'emp_govshare_id' => $share_id,
                    'total_amount' => $data['total_amount'],
                    'govshare_name' => isset($acct_map[$share_id]) ? $acct_map[$share_id]['govshare_name'] : '',
                    'govshare_acctcode' => isset($acct_map[$share_id]) ? $acct_map[$share_id]['govshare_acctcode'] : ''
                ];
                $final_gov_shares[] = $merged;
            }
            return $final_gov_shares;
        }

        public function GetPayrollDeductionsWithAccountCodes() {
            $payroll_data = $this->PayrollData;
            $all_deductions = [];

            // Implementation similar to earnings and govshares
            // Step 1: Collect all deduction breakdowns across filtered PayrollData
            foreach ($payroll_data as $entry) {
                if (!empty($entry['deductions_breakdown'])) {
                    $breakdown = json_decode($entry['deductions_breakdown'], true);
                    if (is_array($breakdown)) {
                        foreach ($breakdown as $b) {
                            $deduct_comp_id = $b['deduct_comp_id'];
                            $amount = isset($b['amount']) ? $b['amount'] : 0;
                            if (!isset($all_deductions[$deduct_comp_id])) {
                                $all_deductions[$deduct_comp_id] = [
                                    'deduct_comp_id' => $deduct_comp_id,
                                    'total_amount' => $amount
                                ];
                            } else {
                                $all_deductions[$deduct_comp_id]['total_amount'] += $amount;
                            }
                        }
                    }
                }
            }

            if (empty($all_deductions)) {
                return [];
            }

            // Step 2: Prepare list of deduct_comp_id for lookup
            $ids = implode(",", array_keys($all_deductions));

            // Step 3: Query to get deduction account codes and titles
            $query = "SELECT edc.deduction_component_id, 
                            cd.deduct_acct_code, 
                            cd.deduct_title
                    FROM employee_deductions_components edc
                    INNER JOIN config_deductions cd 
                        ON edc.config_deduction_id = cd.config_deduction_id
                    WHERE edc.deduction_component_id IN ($ids)";
            $result = $this->db->query($query) or die($this->db->error);

            // Step 4: Map account details to deductions
            $acct_map = [];
            while ($row = $this->db->fetch_array($result)) {
                $acct_map[$row['deduction_component_id']] = [
                    'deduct_title' => $row['deduct_title'],
                    'deduct_acct_code' => $row['deduct_acct_code']
                ];
            }

            // Step 5: Merge the account details into the deductions summary
            $final_deductions = [];
            foreach ($all_deductions as $comp_id => $data) {
                $merged = [
                    'deduct_comp_id' => $comp_id,
                    'total_amount' => $data['total_amount'],
                    'deduct_title' => isset($acct_map[$comp_id]) ? $acct_map[$comp_id]['deduct_title'] : '',
                    'deduct_acct_code' => isset($acct_map[$comp_id]) ? $acct_map[$comp_id]['deduct_acct_code'] : ''
                ];
                $final_deductions[] = $merged;
            }
            return $final_deductions;            
        }

        public function BuildDebitEntriesFromEarningsAndGovShares() {
            
            $Payroll = new Payroll();
            // Determine payroll frequency
            $active_frequency = $Payroll->GetCurrentActiveFrequency();
            $payroll_frequency = $active_frequency['freq_code'];        // 'monthly' or 'semi-monthly'

            $all_earnings = $this->GetPayrollEarningsWithAccountCodes();
            $all_govshares = $this->GetPayrollGovSharesWithAccountCodes();
            $all_deductions = $this->GetPayrollDeductionsWithAccountCodes();

            // Define codes for additional earnings (allowances)
            $additional_earnings_codes = ['50102010', '50102020', '50102030', '50102050', '50102110', '50102060'];

            $total_deductions_amount = 0;
            $total_additional_earnings_amount = 0;
            
            // Set net pay total
            $this->setNetPayTotal();
            $total_net_pay_amount = $this->NetPayTotal;

            // Compute total deductions
            foreach ($all_deductions as $deduction) {
                $total_deductions_amount += $deduction['total_amount'];
            }

            // Compute total of additional earnings (allowances)
            foreach ($all_earnings as $earning) {
                if (in_array($earning['earning_acct_code'], $additional_earnings_codes)) {
                    $total_additional_earnings_amount += $earning['total_amount'];
                }
            }
            if ($payroll_frequency === 'semi-monthly') {
                // For semi-monthly, we consider half of additional earnings
                $total_additional_earnings_amount = $total_additional_earnings_amount / 2;
            }

            // Formula: Salaries Regular/Casual Amount = Total Deductions + Total Net Pay - Total Additional Earnings/2 if semi-monthly
            $salaries_regular_casual_amount = $total_deductions_amount + $total_net_pay_amount - $total_additional_earnings_amount;

            // Initialize array to store grouped entries by account code
            $grouped_entries = [];

            // Process earnings (only add those that are NOT the regular/casual salary accounts yet)
            foreach ($all_earnings as $earning) {
                $acct_code = $earning['earning_acct_code'];
                if (in_array($acct_code, ['50101010', '50101020'])) {
                    // Skip for now; we’ll insert computed amount later
                    continue;
                }
                if (!isset($grouped_entries[$acct_code])) {
                    if ($payroll_frequency === 'semi-monthly' && in_array($acct_code, $additional_earnings_codes)) {
                        // For semi-monthly, only half of additional earnings
                        $amount = $earning['total_amount'] / 2;
                    } else {
                        $amount = $earning['total_amount'];
                    }
                    $grouped_entries[$acct_code] = [
                        'description' => $earning['earning_title'],
                        'acct_code'   => $acct_code,
                        'debit_amt'   => $amount,
                        'credit_amt'  => 0
                    ];
                } else {
                    if ($payroll_frequency === 'semi-monthly' && in_array($acct_code, $additional_earnings_codes)) {
                        // For semi-monthly, only half of additional earnings
                        $grouped_entries[$acct_code]['debit_amt'] += $earning['total_amount'] / 2;
                    } 
                    else {
                        $grouped_entries[$acct_code]['debit_amt'] += $earning['total_amount'];
                    }
                }
            }

            // Add Salaries Regular (50101010) and/or Casual (50101020)
            // If your payroll distinguishes between regular/casual, you can modify this logic
            if ($salaries_regular_casual_amount > 0) {
                // For simplicity, let’s default to Salaries Regular (50101010)
                // You can dynamically switch this based on employment type if needed
                $salary_acct_code = $this->SalaryTypeAcctCode;  //'50101010';
                $salary_description = $this->getEarningTitleByAccountCode($salary_acct_code);

                if (!isset($grouped_entries[$salary_acct_code])) {
                    $grouped_entries[$salary_acct_code] = [
                        'description' => $salary_description,
                        'acct_code'   => $salary_acct_code,
                        'debit_amt'   => $salaries_regular_casual_amount,
                        'credit_amt'  => 0
                    ];
                } else {
                    $grouped_entries[$salary_acct_code]['debit_amt'] += $salaries_regular_casual_amount;
                }
            }

            // Process government shares (add to debit side)
            foreach ($all_govshares as $govshare) {
                $acct_code = $govshare['govshare_acctcode'];
                if (!isset($grouped_entries[$acct_code])) {
                    $grouped_entries[$acct_code] = [
                        'description' => $govshare['govshare_name'],
                        'acct_code'   => $acct_code,
                        'debit_amt'   => $govshare['total_amount'],
                        'credit_amt'  => 0
                    ];
                } else {
                    $grouped_entries[$acct_code]['debit_amt'] += $govshare['total_amount'];
                }
            }

            // Sort entries by account code
            ksort($grouped_entries);

            // Convert associative array to indexed array
            $debit_entries = array_values($grouped_entries);

            return $debit_entries;
        }

        public function BuildCreditEntriesFromDeductions() {
            $all_deductions = $this->GetPayrollDeductionsWithAccountCodes();
            $all_govshares = $this->GetPayrollGovSharesWithAccountCodes();

            // Initialize array to store grouped entries by account code
            $grouped_entries = [];

            // Process deductions (personal shares) - group by deduction acct code
            foreach ($all_deductions as $deduction) {
                $acct_code = $deduction['deduct_acct_code'];
                if (!isset($grouped_entries[$acct_code])) {
                    $grouped_entries[$acct_code] = [
                        'description' => $deduction['deduct_title'],
                        'acct_code'   => $acct_code,
                        'debit_amt'   => 0,
                        'credit_amt'  => $deduction['total_amount']
                    ];
                } else {
                    $grouped_entries[$acct_code]['credit_amt'] += $deduction['total_amount'];
                }
            }

            // Map government share account codes to personal-share deduction account codes
            $gov_to_ps_map = [
                '50103010' => '20201020-1', // GSIS Life and Retirement GS -> GSIS PS (20201020-1)
                '50103020' => '20201030-1', // PAG-IBIG GS -> PAG-IBIG PS (20201030-1)
                '50103030' => '20201040',   // PhilHealth GS -> PhilHealth PS (20201040)
                '50103040' => '20201020-2'  // ECC GS -> ECC PS (20201020-2)
            ];

            // Add govshare amounts into the appropriate personal-share deduction totals
            foreach ($all_govshares as $gov) {
                $gov_code = $gov['govshare_acctcode'];
                $amount = $gov['total_amount'];

                if (isset($gov_to_ps_map[$gov_code])) {
                    $target_acct = $gov_to_ps_map[$gov_code];

                    // Try to get a meaningful description from existing deductions if present
                    $desc = $gov['govshare_name'];
                    foreach ($all_deductions as $d) {
                        if (isset($d['deduct_acct_code']) && $d['deduct_acct_code'] === $target_acct) {
                            $desc = $d['deduct_title'];
                            break;
                        }
                    }

                    if (!isset($grouped_entries[$target_acct])) {
                        $grouped_entries[$target_acct] = [
                            'description' => $desc,
                            'acct_code'   => $target_acct,
                            'debit_amt'   => 0,
                            'credit_amt'  => $amount
                        ];
                    } else {
                        $grouped_entries[$target_acct]['credit_amt'] += $amount;
                    }
                } else {
                    // If govshare has no mapping, keep it as its own credit line (govshare account)
                    $acct = $gov_code;
                    if (!isset($grouped_entries[$acct])) {
                        $grouped_entries[$acct] = [
                            'description' => $gov['govshare_name'],
                            'acct_code'   => $acct,
                            'debit_amt'   => 0,
                            'credit_amt'  => $amount
                        ];
                    } else {
                        $grouped_entries[$acct]['credit_amt'] += $amount;
                    }
                }
            }

            // Add Net Pay credit entry
            $this->setNetPayTotal();
            $grouped_entries[$this->NetPayCreditAcctCode] = [
                'description' => $this->NetPayCreditAcctTitle,
                'acct_code'   => $this->NetPayCreditAcctCode,
                'debit_amt'   => 0,
                'credit_amt'  => $this->NetPayTotal
            ];

            // Sort by account code before converting to indexed array
            ksort($grouped_entries);

            // Convert to indexed array after sorting
            $credit_entries = array_values($grouped_entries);

            return $credit_entries;
        }

	}   
?>