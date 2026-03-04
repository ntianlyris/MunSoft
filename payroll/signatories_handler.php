<?php

include_once '../includes/class/Signatory.php';

if($action = isset($_REQUEST['action'])?$_REQUEST['action']:'') {

    switch ($action) {
        case 'saveSignatory':
            $Signatory = new Signatory();

            $sign_order  = $_POST['sign_order']  ?? '';
            $report_type = $_POST['report_code'] ?? '';
            $signatory_id = $_POST['signatory_id'] ?? null;
            $dept_id = $_POST['dept_id'] ?? null;

            $exists = $Signatory->checkSignatoryOrderExistsForReport($report_type, $sign_order, $signatory_id, $dept_id);
            if ($exists) {
                echo json_encode(['status' => 'error', 'message' => 'Signatory order already exists for this report type']);
                exit;
            }

            $Signatory->setSignatoryData($_POST);
            if($Signatory->SaveSignatory($_POST)) {
                $response = ['status' => 'success', 'message' => 'Signatory saved successfully'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to save signatory'];
            }
            echo json_encode($response);
            break;

        case 'getSignatoryDetails':
            $signatory_id = isset($_GET['signatory_id']) ? $_GET['signatory_id'] : '';
            $Signatory = new Signatory();
            $details = $Signatory->GetSignatoryDetails($signatory_id);
            if($details) {
                echo json_encode(['status' => 'success', 'data' => $details]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Signatory not found']);
            }
            break;

        case 'delete_signatory':
            $signatory_id = isset($_POST['signatory_id']) ? $_POST['signatory_id'] : '';
            $Signatory = new Signatory();
            if($Signatory->DeleteSignatory($signatory_id)) {
                echo json_encode(['status' => 'success', 'message' => 'Signatory deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete signatory']);
            }
            break;

        case 'fetchSignatories':
            $report_type = isset($_GET['report_code']) ? $_GET['report_code'] : '';
            $Signatory = new Signatory();
            $signatories = $Signatory->FetchActiveSignatoriesByReportType($report_type);
            echo json_encode(['status' => 'success', 'data' => $signatories]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} 
else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}
?>