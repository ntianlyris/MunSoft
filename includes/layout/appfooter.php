<?php
/**
 * Shared Main Footer - includes/layout/appfooter.php
 *
 * Usage: Set $page_title before including this file, e.g.:
 *   $page_title = 'Employees';
 *   include_once '../includes/layout/appfooter.php';
 *
 * If $page_title is not set, falls back to a default.
 */
$footer_page_label = $page_title ?? 'IntelliGov';
?>
<!-- Main Footer -->
<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <small>Powered by IntelliGov & AdminLTE v3.1.0 | Proprietary for MGLU Polanco</small>
    </div>
    <strong><small>Copyright &copy; <?php echo date("Y"); ?> <a href="#">Tagz Software Solutions</a>.</small></strong>
    All rights reserved.
</footer>
