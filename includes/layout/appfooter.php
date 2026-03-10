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
$footer_page_label = $page_title ?? 'IntelliGov Nexus';
?>
<!-- Main Footer -->
<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>IntelliGov</b> Nexus
    </div>
    <?php echo htmlspecialchars($footer_page_label); ?>
</footer>
