<?php
/**
 * Ajax Endpoint for Database Backup and Restore
 */

require_once '../includes/class/DatabaseBackup.php';

$BackupManager = new DatabaseBackup();
$response = ['status' => 'error', 'message' => 'Invalid Request'];

if (isset($_POST['action'])) {
    
    // Check if generating a backup
    if ($_POST['action'] === 'generate_backup') {
        $result = $BackupManager->generateBackup();
        echo json_encode($result);
        exit;
    }

    // Check if listing existing backups
    if ($_POST['action'] === 'list_backups') {
        $files = $BackupManager->getBackupList();
        
        $html = '';
        if (empty($files)) {
            $html = '<tr><td colspan="4" class="text-center">No backups found.</td></tr>';
        } else {
            foreach ($files as $file) {
                // Formatting timestamp and sizes
                $date = date('F d, Y h:i A', $file['created_at']);
                $size = number_format($file['size_bytes'] / 1048576, 2) . ' MB';
                $downloadUrl = '../db_backups/' . $file['filename'];

                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($file['filename']) . '</td>';
                $html .= '<td>' . $date . '</td>';
                $html .= '<td>' . $size . '</td>';
                $html .= '<td class="text-center">
                            <div class="btn-group">
                                <a href="' . $downloadUrl . '" class="btn btn-sm btn-success" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                          </td>';
                $html .= '</tr>';
            }
        }
        echo $html;
        exit;
    }
}

// Check if uploading and restoring a backup file
if (isset($_FILES['backup_file'])) {
    $file = $_FILES['backup_file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed. Error Code: ' . $file['error']]);
        exit;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($extension) !== 'sql') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file format. Only .sql files are allowed.']);
        exit;
    }

    // Move to a temporary secure path to process it
    $targetPath = sys_get_temp_dir() . '/' . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Execute Restore
        $result = $BackupManager->restoreBackup($targetPath);
        
        // Clean up temp file
        @unlink($targetPath);
        
        echo json_encode($result);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not stage the uploaded file on the server.']);
        exit;
    }
}

echo json_encode($response);
?>
