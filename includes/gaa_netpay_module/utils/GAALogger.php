<?php
/**
 * GAALogger.php
 * Module-scoped audit and request logger.
 * Writes to the gaa_validation_audit table and optionally to error_log.
 */

if (defined('GAA_LOGGER_LOADED')) return;
define('GAA_LOGGER_LOADED', true);

class GAALogger {

    private $db;

    public function __construct() {
        // DB_conn is loaded by GAANetPayValidator's include chain
        if (class_exists('DB_conn')) {
            $this->db = new DB_conn();
        }
    }

    /**
     * Log an API request (non-blocking — errors are swallowed).
     */
    public function logApiRequest(string $action, ?int $user_id, array $params): void {
        if (!GAAConfig::LOG_ENABLED || !$this->db) return;

        $safe_action   = preg_replace('/[^a-zA-Z0-9_]/', '', $action);
        $safe_user_id  = intval($user_id ?? 0);
        $safe_params   = $this->db->escape_string(json_encode($params));

        $query = "INSERT INTO gaa_api_log 
                    (action, user_id, request_params, requested_at)
                  VALUES
                    ('{$safe_action}', '{$safe_user_id}', '{$safe_params}', NOW())";

        @$this->db->query($query); // Suppress errors — logging should never crash a request
    }

    /**
     * Log a module-level event to PHP error_log.
     */
    public static function log(string $message, string $level = 'INFO'): void {
        if (!GAAConfig::LOG_ENABLED) return;
        error_log("[" . GAAConfig::MODULE_NAME . "] [{$level}] {$message}");
    }

    /**
     * Log an exception cleanly.
     */
    public static function exception(\Throwable $e, string $context = ''): void {
        $msg = sprintf(
            "[%s] [ERROR] %s in %s:%d | Context: %s",
            GAAConfig::MODULE_NAME,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $context
        );
        error_log($msg);
    }
}
?>
