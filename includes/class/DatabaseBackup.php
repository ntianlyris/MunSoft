<?php

include_once("DB_conn.php");

/**
 * Class DatabaseBackup
 * Handles the comprehensive logic for extracting and restoring Database state.
 * Specifically designed with memory streaming structures for Big O space complexity efficiency
 * to avoid memory exhaustion on large payload datasets.
 */
class DatabaseBackup extends DB_conn {

    private $backupDir;

    public function __construct() {
        parent::__construct();
        // Path up to project root, then down into a dedicated backup folder
        $this->backupDir = dirname(__DIR__, 2) . '/db_backups/';
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Generates an SQL raw script file dumping schemas and data.
     * Uses MYSQLI_USE_RESULT (unbuffered queries) to keep memory footprint close to O(1)
     */
    public function generateBackup() {
        // Enforce large-scale processing environment variables
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $tables = array();
        $result = $this->conn->query('SHOW TABLES');
        if (!$result) {
            return ['status' => 'error', 'message' => 'Failed to retrieve tables: ' . $this->conn->error];
        }
        
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }

        $filename = 'backup_' . DB_DATABASE . '_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupDir . $filename;

        // Open sequential write stream
        $handle = fopen($filepath, 'w+');
        if (!$handle) {
            return ['status' => 'error', 'message' => 'Failed to initialize system backup file stream.'];
        }

        fwrite($handle, "-- ---------------------------------------------------------\n");
        fwrite($handle, "-- MunSoft Database Automation Backup\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- ---------------------------------------------------------\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        foreach ($tables as $table) {
            // Sequence 1: Generate DROP & CREATE Syntax
            $result = $this->conn->query('SHOW CREATE TABLE `' . $table . '`');
            if ($result) {
                $row2 = $result->fetch_row();
                fwrite($handle, "DROP TABLE IF EXISTS `" . $table . "`;\n");
                // The table creation string is inherently safe to write
                fwrite($handle, $row2[1] . ";\n\n");
            }

            // Sequence 2: Iteratively dump Data using Chunked Extended Inserts
            $result = $this->conn->query('SELECT * FROM `' . $table . '`', MYSQLI_USE_RESULT);
            if ($result) {
                $numRows = 0;
                $insertBatch = [];
                while ($row = $result->fetch_row()) {
                    // Escape raw SQL sequences while mapping NULL cleanly
                    $values = array_map(function($val) {
                        return is_null($val) ? "NULL" : "'" . $this->conn->real_escape_string($val) . "'";
                    }, $row);

                    $insertBatch[] = "(" . implode(",", $values) . ")";
                    $numRows++;

                    // Limit insert chunk sequence into 50 arrays for fast bulk-loading later
                    if ($numRows % 50 == 0) {
                        fwrite($handle, "INSERT INTO `$table` VALUES " . implode(",", $insertBatch) . ";\n");
                        $insertBatch = [];
                    }
                }
                
                // Flush remaining chunks explicitly remaining in the sequence array buffer
                if (!empty($insertBatch)) {
                    fwrite($handle, "INSERT INTO `$table` VALUES " . implode(",", $insertBatch) . ";\n");
                }
                fwrite($handle, "\n");
                
                // Clear the remote MySQL RAM buffer for the associated table block
                $result->free();
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);

        return [
            'status' => 'success', 
            'message' => 'Successfully captured a systemic database snapshot.',
            'filename' => $filename,
            'download_path' => '../db_backups/' . $filename // Web relative
        ];
    }

    /**
     * Restores an SQL database from a serialized backup string.
     * Uses sequential fget line parsing instead of loading full string in RAM.
     */
    public function restoreBackup($filepath) {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        if (!file_exists($filepath)) {
            return ['status' => 'error', 'message' => 'Backup file signature not found.'];
        }

        $templine = '';
        $handle = fopen($filepath, "r");
        
        if ($handle) {
            $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->conn->query("START TRANSACTION"); // Rollback protection
            
            try {
                while (($line = fgets($handle)) !== false) {
                    // Skip static comments & blank intervals instantly
                    if (substr($line, 0, 2) == '--' || trim($line) == '') continue;

                    $templine .= $line;
                    
                    // Safely extract completed syntax if line securely terminates with a structural closure
                    if (substr(trim($line), -1, 1) == ';') {
                        if (!$this->conn->query($templine)) {
                            throw new Exception("Structural error on line syntax: " . $this->conn->error . "\nPayload: " . $templine);
                        }
                        $templine = ''; // Refresh query buffer
                    }
                }
                
                // Secure operations
                $this->conn->query("COMMIT");
                $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");
                fclose($handle);
                
                return ['status' => 'success', 'message' => 'Database fully restored to historical snapshot!'];

            } catch (Exception $e) {
                $this->conn->query("ROLLBACK"); // Abort everything cleanly
                $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");
                fclose($handle);
                
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
        }
        
        return ['status' => 'error', 'message' => 'Invalid parsing handler generated.'];
    }

    /**
     * Fetches all registered system backups in descending order for Admin rendering
     */
    public function getBackupList() {
        if (!is_dir($this->backupDir)) return [];
        
        $files = scandir($this->backupDir);
        $backups = [];
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $filepath = $this->backupDir . $file;
                $backups[] = [
                    'filename' => $file,
                    'size_bytes' => filesize($filepath),
                    'created_at' => filemtime($filepath),
                    'path' => $filepath
                ];
            }
        }

        // Descending timeline sort (Newest files appear first)
        usort($backups, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return $backups;
    }
}
?>
