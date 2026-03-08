<?php
/**
 * GAAResponseBuilder.php
 * Builds standardized JSON envelopes for every response leaving the module.
 * All bridge handlers and API endpoints use this — never raw json_encode().
 */

if (defined('GAA_RESPONSE_BUILDER_LOADED')) return;
define('GAA_RESPONSE_BUILDER_LOADED', true);

class GAAResponseBuilder {

    /**
     * Emit a success response and exit.
     *
     * @param  mixed  $data     Payload to include under 'data'
     * @param  string $message  Human-readable success message
     * @param  array  $meta     Optional extra metadata (e.g. pagination, timing)
     */
    public static function success($data, string $message = 'OK', array $meta = []): void {
        self::emit([
            'success'   => true,
            'status'    => 'OK',
            'message'   => $message,
            'data'      => $data,
            'meta'      => array_merge(['module' => GAAConfig::MODULE_NAME, 'version' => GAAConfig::MODULE_VERSION, 'ts' => time()], $meta),
        ]);
    }

    /**
     * Emit an error response and exit.
     *
     * @param  string $message    Human-readable error
     * @param  int    $code       Application error code (not HTTP)
     * @param  array  $details    Optional structured error details
     */
    public static function error(string $message, int $code = 400, array $details = []): void {
        self::emit([
            'success' => false,
            'status'  => 'ERROR',
            'message' => $message,
            'code'    => $code,
            'details' => $details,
            'meta'    => ['module' => GAAConfig::MODULE_NAME, 'version' => GAAConfig::MODULE_VERSION, 'ts' => time()],
        ]);
    }

    /**
     * Emit a validation failure response (HTTP 200 but success=false).
     *
     * @param  array  $violations  Array of violation objects
     * @param  string $message
     */
    public static function violation(array $violations, string $message = 'GAA validation failed.'): void {
        self::emit([
            'success'          => false,
            'status'           => 'VIOLATION',
            'message'          => $message,
            'violations'       => $violations,
            'violation_count'  => count($violations),
            'meta'             => ['module' => GAAConfig::MODULE_NAME, 'ts' => time()],
        ]);
    }

    /**
     * Return a raw associative array instead of emitting (useful for chaining).
     */
    public static function build(bool $success, string $status, $data, string $message = ''): array {
        return [
            'success' => $success,
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
            'meta'    => ['module' => GAAConfig::MODULE_NAME, 'ts' => time()],
        ];
    }

    // ── Private helpers ───────────────────────────────────

    private static function emit(array $payload): void {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('X-GAA-Module: ' . GAAConfig::MODULE_VERSION);
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
?>
