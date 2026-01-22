<?php
require_once '../includes/class/GovShare.php';
require_once '../includes/class/Employee.php';

if($action = isset($_REQUEST['action'])?$_REQUEST['action']:'') {
    switch ($action) {
        case 'save_govshare':
            $GovShare = new GovShare();

            $deduction_type_id = $_POST['deduction_type'];
            $govshare_name = $_POST['govshare_name'];
            $govshare_code = $_POST['govshare_code'];
            $govshare_rate = $_POST['govshare_rate'];
            $is_percentage = $_POST['is_percentage'];
            $govshare_acctcode = $_POST['govshare_acctcode'];

            $GovShare->setGovShareTypeID($deduction_type_id);
            $GovShare->setGovShareName($govshare_name);
            $GovShare->setGovShareCode($govshare_code);
            $GovShare->setGovShareRate($govshare_rate);
            $GovShare->setIsPercentage($is_percentage);
            $GovShare->setGovShareAcctCode($govshare_acctcode);

            if ($GovShare->SaveGovShare()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => "Contribution added successfully!"
                ]);
            }
            else{
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'xxx',
                    'message' => "Failed to add Contribution."
                ]);
            }
            break;
        
        case 'get_govshare':
            $GovShare = new GovShare();

            $govshare_id = $_GET['id'];
            $govshare_details = '';
            $json_data = '';

            if($govshare_details = $GovShare->GetGovShareDetailsByID($govshare_id)){
                $govshare_details['result'] = 'success';
                $json_data = json_encode($govshare_details);
            }
            else{
                $json_data = '{"result":"xxx"}';
            }
            echo $json_data;
            break;
        
        case 'edit_govshare':
            $GovShare = new GovShare();

            $deduction_type_id = $_POST['deduction_type'];
            $govshare_id = $_POST['govshare_id'];
            $govshare_name = $_POST['govshare_name'];
            $govshare_code = $_POST['govshare_code'];
            $govshare_rate = $_POST['govshare_rate'];
            $is_percentage = $_POST['is_percentage'];
            $govshare_acctcode = $_POST['govshare_acctcode'];

            $GovShare->setGovShareTypeID($deduction_type_id);
            $GovShare->setGovShareName($govshare_name);
            $GovShare->setGovShareCode($govshare_code);
            $GovShare->setGovShareRate($govshare_rate);
            $GovShare->setIsPercentage($is_percentage);
            $GovShare->setGovShareAcctCode($govshare_acctcode);

            if ($GovShare->EditGovShare($govshare_id)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => "Contribution edited successfully!"
                ]);
            }
            else{
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'xxx',
                    'message' => "Failed to edit Contribution."
                ]);
            }
            break;  
            
        case 'delete_govshare':
            $GovShare = new GovShare();
            $govshare_id = $_POST['govshare_id'];

            if ($GovShare->DeleteGovShare($govshare_id)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'result' => 'success',
                    'message' => "Contribution deleted successfully!"
                ]);
            }
            else{
                header('Content-Type: application/json');
                echo json_encode([
                    'result' => 'xxx',
                    'message' => "Failed to delete Contribution."
                ]);
            }
            break;

        case 'compute_govshares':
            $EmployedEmployee = new Employee();
            $GovShare = new GovShare();

            $response = ['status' => 'error', 'message' => 'Invalid request'];
            $rate = 0;
            $govshare_output = [];

            if (isset($_POST['employee_id'])) {
                $employee_id = intval($_POST['employee_id']);

                $rate_data = $EmployedEmployee->FetchBasicRateOfEmployedEmployeesWithEarning($employee_id);
                $rate = floatval($rate_data['rate']);

                if ($rate) {
                    $govshares = $GovShare->GetGovShares(); // Assume this returns all configs for all types

                    // Group govshares by deduction_type_code (GSIS, PHIC, PAGIBIG, etc.)
                    $groupedShares = [];
                    foreach ($govshares as $share) {
                        $type_code = strtoupper(trim($share['deduction_type_code']));
                        $groupedShares[$type_code][] = $share;
                    }

                    // Loop per type (GSIS, PAGIBIG, PHIC, etc.)
                    foreach ($groupedShares as $type => $configs) {
                        $type_shares = [];

                        foreach ($configs as $config) {
                            $method = strtolower($config['is_percentage']);
                            $amount = 0;

                            if ($method === '1') {
                                $amount = round($rate * floatval($config['govshare_rate']), 2);

                                // For PhilHealth, if assumed to be split with employee 50/50
                                if ($type === 'PHIC') {
                                    $amount = round($amount / 2, 2);
                                }
                            } elseif ($method === '0') {
                                $amount = round(floatval($config['govshare_rate']), 2);
                            } else {
                                continue; // skip unknown method
                            }

                            $type_shares[] = [
                                'govshare_id' => $config['govshare_id'],
                                'govshare_name' => $config['govshare_name'],
                                //'calculation_method' => $method,
                                'rate' => $config['govshare_rate'],
                                'amount' => number_format($amount, 2),
                            ];
                        }

                        $govshare_output[$type] = $type_shares;
                    }

                    $response = [
                        'status' => 'success',
                        'data' => [
                            'monthly_rate' => number_format($rate, 2),
                            'shares' => $govshare_output
                        ]
                    ];
                } else {
                    $response['message'] = 'Employee data not found.';
                }
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            break;
        
        case 'save_employee_govshares':

            $employee_id = $_POST['employee_id'];
            $employee_rate = str_replace(',', '', $_POST['monthly_rate']);
            $employee_rate = floatval($employee_rate);
            $govshare_ids = $_POST['govshare_id'] ?? [];   ## array of govshare ids
            $govshare_amts = $_POST['govshare_amount'] ?? [];  ## array of govshare amounts

            if (!empty($govshare_ids) && !empty($govshare_amts)) {
                for ($i=0; $i < count($govshare_ids) ; $i++) { 
                    $govshare_id = htmlspecialchars($govshare_ids[$i]);
                    $govshare_amt = str_replace(',', '', $govshare_amts[$i]);
                    $govshare_amt = floatval($govshare_amt); // 1234.56

                    $govshares_arr[] = array($govshare_id, $govshare_amt);   ## array of govshare id with the corresponding amounts
                }

                // Then call your method
                $GovShare = new GovShare();
                $save = $GovShare->SaveEmployeeGovShares($employee_id, $employee_rate, $govshares_arr);

                echo json_encode([
                    'status' => $save ? 'success' : 'error',
                    'message' => $save ? 'Government Share details saved successfully.' : 'Failed to save.'
                ]);
            }

            break;

        default:
            # code...
            break;
    }
}



?>
