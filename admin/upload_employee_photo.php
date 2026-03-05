<?php
/**
 * Employee Photo Upload Handler
 * This script handles the file upload and processing for employee photos
 * Processes AJAX requests for uploading and managing employee photos
 */

header('Content-Type: application/json');

// Check if this is an AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        include_once '../includes/class/EmployeePhoto.php';
        
        switch ($action) {
            
            /**
             * Upload employee photo
             * Required POST: employee_id, image file in $_FILES['employee_photo']
             */
            case 'upload_photo':
                $response = array('status' => false, 'message' => 'Unknown error occurred');
                
                try {
                    // Validate employee_id
                    if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
                        $response = array('status' => false, 'message' => 'Employee ID is required');
                        break;
                    }
                    
                    // Validate file upload
                    if (!isset($_FILES['employee_photo'])) {
                        $response = array('status' => false, 'message' => 'No file was uploaded');
                        break;
                    }
                    
                    $employee_id = $_POST['employee_id'];
                    $file = $_FILES['employee_photo'];
                    
                    // Create EmployeePhoto instance and upload
                    $photoHandler = new EmployeePhoto();
                    
                    // Check GD availability and inform user
                    $gdStatus = $photoHandler->getGDStatus();
                    
                    $uploadResult = $photoHandler->uploadPhoto($employee_id, $file);
                    
                    if ($uploadResult['status']) {
                        // Add GD status info to success response
                        $uploadResult['gd_status'] = $gdStatus;
                    } else {
                        // Add helpful info if upload failed
                        if (strpos($uploadResult['message'], 'Failed') !== false) {
                            $uploadResult['message'] .= ' - ' . $gdStatus;
                        }
                    }
                    
                    $response = $uploadResult;
                    
                } catch (Exception $e) {
                    $response = array('status' => false, 'message' => 'Error: ' . $e->getMessage());
                }
                
                echo json_encode($response);
                break;
            
            /**
             * Get employee photo
             * Required POST: employee_id
             */
            case 'get_photo':
                $response = array('status' => false, 'message' => 'No photo found');
                
                try {
                    if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
                        $response = array('status' => false, 'message' => 'Employee ID is required');
                        break;
                    }
                    
                    $employee_id = $_POST['employee_id'];
                    
                    $photoHandler = new EmployeePhoto();
                    $photo = $photoHandler->getPhotoByEmployeeID($employee_id);
                    
                    if ($photo !== false) {
                        $response = array(
                            'status' => true,
                            'message' => 'Photo found',
                            'photo_id' => $photo['photo_id'],
                            'photo_path' => $photo['photo_path'],
                            'upload_date' => $photo['upload_date'],
                            'file_size' => $photo['file_size']
                        );
                    }
                    
                } catch (Exception $e) {
                    $response = array('status' => false, 'message' => 'Error: ' . $e->getMessage());
                }
                
                echo json_encode($response);
                break;
            
            /**
             * Delete employee photo
             * Required POST: photo_id
             */
            case 'delete_photo':
                $response = array('status' => false, 'message' => 'Failed to delete photo');
                
                try {
                    if (!isset($_POST['photo_id']) || empty($_POST['photo_id'])) {
                        $response = array('status' => false, 'message' => 'Photo ID is required');
                        break;
                    }
                    
                    $photo_id = $_POST['photo_id'];
                    
                    $photoHandler = new EmployeePhoto();
                    if ($photoHandler->deletePhoto($photo_id)) {
                        $response = array('status' => true, 'message' => 'Photo deleted successfully');
                    }
                    
                } catch (Exception $e) {
                    $response = array('status' => false, 'message' => 'Error: ' . $e->getMessage());
                }
                
                echo json_encode($response);
                break;
            
            /**
             * Check if employee has photo
             * Required POST: employee_id
             */
            case 'check_photo':
                $response = array('status' => false, 'has_photo' => false);
                
                try {
                    if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
                        $response = array('status' => false, 'message' => 'Employee ID is required');
                        break;
                    }
                    
                    $employee_id = $_POST['employee_id'];
                    
                    $photoHandler = new EmployeePhoto();
                    $hasPhoto = $photoHandler->hasPhoto($employee_id);
                    
                    $response = array(
                        'status' => true,
                        'has_photo' => $hasPhoto
                    );
                    
                    if ($hasPhoto) {
                        $photo = $photoHandler->getPhotoByEmployeeID($employee_id);
                        $response['photo_path'] = $photo['photo_path'];
                    }
                    
                } catch (Exception $e) {
                    $response = array('status' => false, 'message' => 'Error: ' . $e->getMessage());
                }
                
                echo json_encode($response);
                break;
            
            /**
             * Get all photos of an employee
             * Required POST: employee_id
             */
            case 'get_all_photos':
                $response = array('status' => false, 'photos' => array());
                
                try {
                    if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
                        $response = array('status' => false, 'message' => 'Employee ID is required');
                        break;
                    }
                    
                    $employee_id = $_POST['employee_id'];
                    
                    $photoHandler = new EmployeePhoto();
                    $photos = $photoHandler->getAllPhotosByEmployeeID($employee_id);
                    
                    if ($photos !== false) {
                        $response = array(
                            'status' => true,
                            'photos' => $photos,
                            'count' => count($photos)
                        );
                    } else {
                        $response = array(
                            'status' => true,
                            'photos' => array(),
                            'count' => 0
                        );
                    }
                    
                } catch (Exception $e) {
                    $response = array('status' => false, 'message' => 'Error: ' . $e->getMessage());
                }
                
                echo json_encode($response);
                break;
            
            default:
                $response = array('status' => false, 'message' => 'Invalid action');
                echo json_encode($response);
                break;
        }
    } else {
        $response = array('status' => false, 'message' => 'Action is required');
        echo json_encode($response);
    }
} else {
    $response = array('status' => false, 'message' => 'Invalid request method');
    echo json_encode($response);
}
?>
