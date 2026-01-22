<?php
include_once '../includes/class/Leave.php';
include_once '../includes/class/Employee.php';

if($action = isset($_POST['action'])?$_POST['action']:'') {
    switch ($action) {
        case 'submit_leave_application':
            session_start();
            $MyEmployee = new Employee();     
            $MyLeave = new Leave();

            $user_id = $_SESSION['uid'];

            $leave_data = array();
            $emp_id     = $MyEmployee->getEmployeeIDByUserId($user_id); // logged in employee
            $leave_type = $_POST['leave_type'];
            $reason     = $_POST['reason'];
            $dates      = $_POST['inclusive_dates']; // array
            $status     = 'Pending';
            $date_filed = date("Y-m-d H:i:s");
            
            // File upload
            $attachmentPath = NULL;
            if (!empty($_FILES['attachment']['name'])) {
                $targetDir = "../uploads/leave_docs/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = time() . "_" . basename($_FILES["attachment"]["name"]);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $targetFile)) {
                    $attachmentPath = $fileName;
                }
            }

            $dates_json = json_encode($dates); // Convert array to JSON string

            $leave_data = array(
                'emp_id'     => $emp_id,
                'leave_type' => $leave_type,
                'dates'      => $dates_json,
                'reason'     => $reason,
                'status'     => $status,
                'date_filed' => $date_filed,
                'attachmentPath' => $attachmentPath
            );

            $MyLeave->setLeaveData($leave_data);
            // Assuming SaveLeaveApplication returns true/false or an array with status/message
            $result = $MyLeave->SaveLeaveApplication();

            if ($result === true || (is_array($result) && isset($result['status']) && $result['status'] === 'success')) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Leave application submitted successfully!'
                ]);
            } else {
                $errorMsg = is_array($result) && isset($result['message']) ? $result['message'] : 'Failed to submit leave application.';
                echo json_encode([
                    'status' => 'error',
                    'message' => $errorMsg
                ]);
            }
            exit;


            // 🔹 OPTION B: Auto-split into multiple records (one per date range)
            /*
            foreach ($dates as $range) {
                $stmt = $conn->prepare("INSERT INTO leave_applications 
                    (emp_id, leave_type, date_range, reason, attachment, status, date_filed) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssss", $emp_id, $leave_type, $range, $reason, $attachmentPath, $status, $date_filed);
                $stmt->execute();
            }
            echo "<script>alert('Leave application submitted successfully!'); window.location.href='leaves.php';</script>";
            */
            break;

        case 'fetch_leave_applications':
            # code...
            break;

        default:
            # code...
            break;
    }
    
}

?>