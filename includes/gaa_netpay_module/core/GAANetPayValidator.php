<?php
/**
 * GAANetPayValidator.php  —  Module Wrapper
 * ─────────────────────────────────────────────────────────────
 *  Location : Munsoft/includes/gaa_netpay_module/core/
 *
 *  PURPOSE:
 *  This file is the module's internal bridge to the project's
 *  original GAANetPayValidator class located at:
 *    Munsoft/includes/class/GAANetPayValidator.php
 *
 *  PROBLEM SOLVED:
 *  The original validator uses relative include_once("DB_conn.php")
 *  which only works when PHP's working directory is the class/
 *  folder. When included from the module context, the CWD is
 *  different, breaking the include chain.
 *
 *  SOLUTION:
 *  1. Resolve the class/ directory via GAA_MODULE_ROOT (set by
 *     GAAModule.php using __DIR__, always reliable).
 *  2. Temporarily add class/ to PHP's include_path so all
 *     relative includes inside the original files resolve correctly.
 *  3. Restore include_path after loading.
 * ─────────────────────────────────────────────────────────────
 */

if (defined('GAA_VALIDATOR_LOADED')) return;
define('GAA_VALIDATOR_LOADED', true);

// ── Resolve absolute path to Munsoft/includes/class/ ─────────
// GAA_MODULE_ROOT = .../Munsoft/includes/gaa_netpay_module
// So ../class = .../Munsoft/includes/class
$_gaa_class_dir = realpath(GAA_MODULE_ROOT . '/../class');

if (!$_gaa_class_dir || !is_dir($_gaa_class_dir)) {
    error_log('[GAAModule] CRITICAL: Cannot resolve class directory from GAA_MODULE_ROOT=' . GAA_MODULE_ROOT);
    return;
}

// ── Temporarily extend include_path so relative includes work ─
// The original GAANetPayValidator does include_once("DB_conn.php")
// DB_conn does include_once("DB_interface.php")
// Both resolve correctly once class/ is on the path.
$_gaa_prev_path = get_include_path();
set_include_path($_gaa_prev_path . PATH_SEPARATOR . $_gaa_class_dir);

// ── Load the dependency chain with absolute paths ─────────────
// Using require_once + absolute path ensures no duplicate loading
// even if the original files were already loaded by other parts
// of the application.
require_once $_gaa_class_dir . '/DB_conn.php';
require_once $_gaa_class_dir . '/GAANetPayValidator.php';

// ── Restore include_path ──────────────────────────────────────
set_include_path($_gaa_prev_path);

// ── Cleanup temporary vars from global scope ──────────────────
unset($_gaa_class_dir, $_gaa_prev_path);
?>
