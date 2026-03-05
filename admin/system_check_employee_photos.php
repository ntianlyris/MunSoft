<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Photo Upload - System Check</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <style>
        body { padding: 30px; background-color: #f5f5f5; }
        .status-card { margin-bottom: 20px; border-radius: 8px; }
        .status-success { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .status-warning { background-color: #fff3cd; border: 1px solid #ffeeba; }
        .status-error { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .check-item { padding: 15px; }
        .check-title { font-weight: bold; font-size: 16px; }
        .check-details { font-size: 14px; margin-top: 5px; }
        code { background-color: #f8f9fa; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">Employee Photo Upload Feature - System Validation</h1>
    
    <div class="row">
        <div class="col-md-8">

            <!-- 1. PHP Version Check -->
            <div class="card status-card status-<?php echo phpversion() >= 7.0 ? 'success' : 'error'; ?>">
                <div class="card-body check-item">
                    <div class="check-title">✓ PHP Version</div>
                    <div class="check-details">
                        Current: <code><?php echo phpversion(); ?></code>
                        <br>Required: PHP 7.0 or higher
                        <?php 
                        $status = phpversion() >= 7.0 ? '✓ Compatible' : '✗ Incompatible';
                        echo "<br><strong>Status: $status</strong>";
                        ?>
                    </div>
                </div>
            </div>

            <!-- 2. GD Library Check -->
            <div class="card status-card status-<?php echo extension_loaded('gd') ? 'success' : 'error'; ?>">
                <div class="card-body check-item">
                    <div class="check-title">✓ GD Library (Image Processing)</div>
                    <div class="check-details">
                        <?php 
                        if (extension_loaded('gd')) {
                            echo "Status: <strong style='color: green;'>✓ Enabled</strong>";
                            $gdInfo = gd_info();
                            echo "<br>JPEG Support: " . ($gdInfo['JPEG Support'] ? 'Yes' : 'No');
                            echo "<br>PNG Support: " . ($gdInfo['PNG Support'] ? 'Yes' : 'No');
                            echo "<br>GIF Support: " . ($gdInfo['GIF Support'] ? 'Yes' : 'No');
                        } else {
                            echo "Status: <strong style='color: red;'>✗ NOT ENABLED</strong>";
                            echo "<br><small>This library is required for image resizing!</small>";
                            echo "<br><small>Enable in php.ini: find <code>extension=gd</code> and uncomment it</small>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- 3. Upload Directory Check -->
            <?php
            $uploadDir = dirname(__FILE__) . '/assets/images/employees/';
            $dirExists = is_dir($uploadDir);
            $isWritable = $dirExists && is_writable($uploadDir);
            ?>
            <div class="card status-card status-<?php echo $isWritable ? 'success' : 'error'; ?>">
                <div class="card-body check-item">
                    <div class="check-title">✓ Upload Directory (<code>assets/images/employees/</code>)</div>
                    <div class="check-details">
                        Exists: <strong><?php echo $dirExists ? '✓ Yes' : '✗ No'; ?></strong>
                        <br>Writable: <strong><?php echo $isWritable ? '✓ Yes' : '✗ No'; ?></strong>
                        <?php if (!$isWritable): ?>
                            <br><small style="color: red;">Create directory and set permissions: <code>chmod 755 assets/images/employees/</code></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 4. Classes Check -->
            <?php
            $classDir = dirname(__FILE__) . '/includes/class/';
            $employeePhotoExists = file_exists($classDir . 'EmployeePhoto.php');
            $employeeExists = file_exists($classDir . 'Employee.php');
            ?>
            <div class="card status-card status-<?php echo ($employeePhotoExists && $employeeExists) ? 'success' : 'error'; ?>">
                <div class="card-body check-item">
                    <div class="check-title">✓ Class Files</div>
                    <div class="check-details">
                        EmployeePhoto.php: <strong><?php echo $employeePhotoExists ? '✓ Found' : '✗ Missing'; ?></strong>
                        <br>Employee.php: <strong><?php echo $employeeExists ? '✓ Found' : '✗ Missing'; ?></strong>
                        <?php if (!$employeePhotoExists): ?>
                            <br><small style="color: red;">Create <code>includes/class/EmployeePhoto.php</code></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 5. Upload Handler Check -->
            <?php
            $handlerExists = file_exists(dirname(__FILE__) . '/admin/upload_employee_photo.php');
            ?>
            <div class="card status-card status-<?php echo $handlerExists ? 'success' : 'error'; ?>">
                <div class="card-body check-item">
                    <div class="check-title">✓ Upload Handler (<code>admin/upload_employee_photo.php</code>)</div>
                    <div class="check-details">
                        Status: <strong><?php echo $handlerExists ? '✓ Found' : '✗ Missing'; ?></strong>
                        <?php if (!$handlerExists): ?>
                            <br><small style="color: red;">Create handler file in admin directory</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 6. Database Connection Check -->
            <div class="card status-card">
                <div class="card-body check-item">
                    <div class="check-title">✓ Database Connection</div>
                    <div class="check-details">
                        <?php
                        try {
                            include_once 'includes/class/DB_conn.php';
                            $db = new DB_conn();
                            
                            // Check if employee_photos table exists
                            $result = $db->query("SHOW TABLES LIKE 'employee_photos'");
                            $tableExists = $db->num_rows($result) > 0;
                            
                            echo "Database Connection: <strong style='color: green;'>✓ Connected</strong>";
                            echo "<br>employee_photos Table: <strong>" . ($tableExists ? '✓ Exists' : '✗ Missing') . "</strong>";
                            
                            if (!$tableExists) {
                                echo "<br><small style='color: red;'>You need to run the migration SQL to create the table</small>";
                                echo "<br><small>Check <code>db/migration_employee_photos.sql</code></small>";
                            } else {
                                // Get table stats
                                $statsResult = $db->query("
                                    SELECT COUNT(*) as count, 
                                           SUM(file_size) as total_size 
                                    FROM employee_photos
                                ");
                                $stats = $db->fetch_array($statsResult);
                                echo "<br>Total Photos: " . $stats['count'];
                                echo "<br>Total Storage: " . (isset($stats['total_size']) && $stats['total_size'] ? 
                                        round($stats['total_size'] / 1024 / 1024, 2) . " MB" : "0 MB");
                            }
                        } catch (Exception $e) {
                            echo "<strong style='color: red;'>✗ Error: " . $e->getMessage() . "</strong>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- 7. .htaccess Protection Check -->
            <?php
            $htaccessFile = dirname(__FILE__) . '/assets/images/employees/.htaccess';
            $htaccessExists = file_exists($htaccessFile);
            ?>
            <div class="card status-card status-<?php echo $htaccessExists ? 'success' : 'warning'; ?>">
                <div class="card-body check-item">
                    <div class="check-title">✓ Security (.htaccess Protection)</div>
                    <div class="check-details">
                        .htaccess File: <strong><?php echo $htaccessExists ? '✓ Found' : '⚠ Missing'; ?></strong>
                        <?php if (!$htaccessExists): ?>
                            <br><small style="color: orange;">Create <code>.htaccess</code> in <code>assets/images/employees/</code> for security</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="alert alert-info mt-4">
                <h5>System Status Summary</h5>
                <?php
                $checks = [
                    'PHP Version' => phpversion() >= 7.0,
                    'GD Library' => extension_loaded('gd'),
                    'Upload Directory' => $isWritable,
                    'EmployeePhoto Class' => $employeePhotoExists,
                    'Upload Handler' => $handlerExists,
                    'Database Connected' => isset($db) && $tableExists,
                ];
                
                $passed = count(array_filter($checks));
                $total = count($checks);
                echo "<p><strong>$passed/$total checks passed</strong></p>";
                
                if ($passed === $total) {
                    echo "<div class='alert alert-success'>✓ All systems ready! You can now use the employee photo feature.</div>";
                } else {
                    echo "<div class='alert alert-warning'>⚠ Some checks failed. Please fix the issues above before using the feature.</div>";
                }
                ?>
            </div>

        </div>

        <!-- Right Column: Instructions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Setup Instructions</h5>
                </div>
                <div class="card-body">
                    <h6>1. Database</h6>
                    <p><small>Run migration SQL from <code>db/migration_employee_photos.sql</code></small></p>
                    
                    <h6>2. GD Library</h6>
                    <p><small>Enable in php.ini if not already enabled</small></p>
                    
                    <h6>3. Permissions</h6>
                    <p><small>Set <code>assets/images/employees/</code> to 755</small></p>
                    
                    <h6>4. Files</h6>
                    <p><small>Verify all class files are in place</small></p>
                    
                    <h6>5. Integration</h6>
                    <p><small>Reference <code>EMPLOYEE_PHOTO_INTEGRATION_EXAMPLE.php</code></small></p>
                    
                    <hr>
                    
                    <h6>Documentation</h6>
                    <ul>
                        <li><a href="QUICK_START_EMPLOYEE_PHOTOS.md">Quick Start Guide</a></li>
                        <li><a href="EMPLOYEE_PHOTO_FEATURE_GUIDE.md">Full Documentation</a></li>
                        <li><a href="EMPLOYEE_PHOTO_INTEGRATION_EXAMPLE.php">Integration Example</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
