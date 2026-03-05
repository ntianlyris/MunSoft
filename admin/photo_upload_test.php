<?php
/**
 * Employee Photo Upload - Diagnostic & Test Page
 * Use this page to test the photo upload functionality and check GD library status
 */

// Check session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic security check
if (!isset($_SESSION['admin_id'])) {
    die('Access Denied. Please log in first.');
}

include_once '../includes/class/EmployeePhoto.php';
include_once '../includes/class/Employee.php';

$photoHandler = new EmployeePhoto();
$gdStatus = $photoHandler->getGDStatus();

// Get all employees for test
$employeeHandler = new Employee();
$employees = $employeeHandler->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Photo Upload - Test & Diagnostic</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        .diagnostic-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 14px;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 15px 0;
        }
        .test-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #ddd;
        }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }
        #photoPreview {
            max-width: 300px;
            max-height: 300px;
            margin-top: 15px;
            border: 2px solid #ddd;
            border-radius: 4px;
        }
        .upload-progress {
            display: none;
            margin-top: 15px;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="diagnostic-container">
        <h1>🖼️ Employee Photo Upload - Test & Diagnostic</h1>
        <p class="text-muted">Use this page to test the photo upload functionality and diagnose issues.</p>

        <!-- PHP Information Section -->
        <div class="test-section">
            <h2>System Status</h2>
            
            <h4>GD Library Status</h4>
            <div class="<?php echo strpos($gdStatus, 'available') !== false ? 'status-success' : 'status-warning'; ?> status-badge">
                📊 <?php echo $gdStatus; ?>
            </div>

            <h4>PHP Extensions Required</h4>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Extension</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>GD Library</td>
                        <td>
                            <?php 
                            if (extension_loaded('gd')) {
                                echo '<span class="badge badge-success">✓ Loaded</span>';
                            } else {
                                echo '<span class="badge badge-danger">✗ NOT Loaded</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>imagecreatefromjpeg()</td>
                        <td>
                            <?php 
                            if (function_exists('imagecreatefromjpeg')) {
                                echo '<span class="badge badge-success">✓ Available</span>';
                            } else {
                                echo '<span class="badge badge-danger">✗ NOT Available</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>imagecreatefrompng()</td>
                        <td>
                            <?php 
                            if (function_exists('imagecreatefrompng')) {
                                echo '<span class="badge badge-success">✓ Available</span>';
                            } else {
                                echo '<span class="badge badge-danger">✗ NOT Available</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>imagecreatefromgif()</td>
                        <td>
                            <?php 
                            if (function_exists('imagecreatefromgif')) {
                                echo '<span class="badge badge-success">✓ Available</span>';
                            } else {
                                echo '<span class="badge badge-danger">✗ NOT Available</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>imagecreatetruecolor()</td>
                        <td>
                            <?php 
                            if (function_exists('imagecreatetruecolor')) {
                                echo '<span class="badge badge-success">✓ Available</span>';
                            } else {
                                echo '<span class="badge badge-danger">✗ NOT Available</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="info-box">
                <strong>ℹ️ What this means:</strong><br>
                If GD functions are <strong>NOT Available</strong>, the system will still save images but without resizing them for optimization.
                To enable resizing, enable the GD extension in your php.ini file.
            </div>
        </div>

        <!-- Upload Test Section -->
        <div class="test-section">
            <h2>Test Upload</h2>
            <p>Select an employee and upload a test photo to verify the system is working.</p>

            <form id="testUploadForm">
                <div class="form-group">
                    <label for="employee_id">Select Employee:</label>
                    <select id="employee_id" class="form-control" required>
                        <option value="">-- Choose an employee --</option>
                        <?php 
                        if ($employees && is_array($employees)) {
                            foreach ($employees as $emp) {
                                echo '<option value="' . $emp['id'] . '">' . $emp['firstname'] . ' ' . $emp['lastname'] . ' (ID: ' . $emp['id'] . ')</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="test_photo">Select Photo File:</label>
                    <input type="file" id="test_photo" name="employee_photo" class="form-control-file" accept="image/*" required>
                    <small class="form-text text-muted">
                        Accepted formats: JPG, PNG, GIF (Max size: 5MB)
                    </small>
                </div>

                <div id="photoPreviewContainer"></div>

                <button type="submit" class="btn btn-primary">Upload Test Photo</button>
                <button type="reset" class="btn btn-secondary">Clear</button>
            </form>

            <!-- Upload Progress -->
            <div class="upload-progress">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                         style="width: 100%"></div>
                </div>
                <p class="text-muted mt-2">Uploading...</p>
            </div>

            <!-- Response Alert -->
            <div id="responseAlert" class="alert alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <strong id="alertTitle"></strong><br>
                <span id="alertMessage"></span>
                <div id="gdStatusInfo" style="margin-top: 10px;"></div>
            </div>
        </div>

        <!-- Information Section -->
        <div class="test-section">
            <h2>Troubleshooting</h2>
            <h4>If GD Library is NOT Available:</h4>
            <ol>
                <li>Open your XAMPP control panel</li>
                <li>Find the PHP configuration file: <code>php.ini</code> (usually in XAMPP\php folder)</li>
                <li>Search for the line: <code>;extension=gd</code> (with semicolon)</li>
                <li>Remove the semicolon to uncomment it: <code>extension=gd</code></li>
                <li>Save the file and restart Apache from XAMPP control panel</li>
                <li>Refresh this page to verify GD is now loaded</li>
            </ol>

            <h4>If the Upload Still Fails:</h4>
            <ul>
                <li>Check that the `/assets/images/employees/` directory exists and is writable</li>
                <li>Verify file permissions are 755 or similar</li>
                <li>Try uploading a smaller image file</li>
                <li>Check browser console (F12) for AJAX errors</li>
                <li>Review your web server error logs</li>
            </ul>

            <h4>Alternative Workaround:</h4>
            <p>Even without GD library, the photo upload system will:</p>
            <ul>
                <li>✓ Accept and validate image files</li>
                <li>✓ Save images to the server</li>
                <li>✓ Store file paths in the database</li>
                <li>✓ Display images in employee profiles</li>
                <li>✗ Will NOT resize images (images saved at original size)</li>
            </ul>
        </div>

        <div class="text-center mt-5">
            <a href="employees.php" class="btn btn-outline-secondary">Back to Employees</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="../assets/bootstrap/js/popper.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        // Preview image before upload
        $('#test_photo').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    let preview = $('#photoPreviewContainer').html(
                        '<div><strong>Preview:</strong><br><img id="photoPreview" src="' + event.target.result + '" alt="Photo preview"></div>'
                    );
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle form submission
        $('#testUploadForm').on('submit', function(e) {
            e.preventDefault();

            const employeeId = $('#employee_id').val();
            const photoFile = $('#test_photo')[0].files[0];

            if (!employeeId || !photoFile) {
                showAlert('error', 'Error', 'Please select both an employee and a photo file.');
                return;
            }

            // Show progress
            $('.upload-progress').show();
            $('#responseAlert').hide();

            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'upload_photo');
            formData.append('employee_id', employeeId);
            formData.append('employee_photo', photoFile);

            // Send AJAX request
            $.ajax({
                url: './upload_employee_photo.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.upload-progress').hide();
                    
                    if (response.status) {
                        showAlert('success', 'Success!', response.message);
                        if (response.gd_status) {
                            $('#gdStatusInfo').html('<small class="text-muted">GD Info: ' + response.gd_status + '</small>');
                        }
                        $('#testUploadForm')[0].reset();
                        $('#photoPreviewContainer').html('');
                    } else {
                        showAlert('danger', 'Upload Failed', response.message);
                        if (response.gd_status) {
                            $('#gdStatusInfo').html('<small class="text-muted">GD Info: ' + response.gd_status + '</small>');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('.upload-progress').hide();
                    showAlert('danger', 'Error', 'AJAX request failed: ' + error);
                }
            });
        });

        function showAlert(type, title, message) {
            $('#alertTitle').text(title);
            $('#alertMessage').text(message);
            $('#responseAlert').removeClass('alert-success alert-danger alert-warning')
                              .addClass('alert-' + type)
                              .show();
        }
    });
    </script>
</body>
</html>
