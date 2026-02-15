<?php include_once('../includes/class/auth.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MunSoft</title>

  <link rel="icon" href="../includes/images/polanco_logo.png" type="image/png" />
	<link rel="shortcut icon" href="../includes/images/polanco_logo.png" type="image/png" />
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"> 
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="../plugins/toastr/toastr.min.css">
  <!-- Theme style -->
  <!--<link rel="stylesheet" href="../dist/css/adminlte.min.css">-->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- Select2 -->
  <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

  <!-- My style -->
  <link rel="stylesheet" href="../css/my_style.css">
  
  <!-- Employee Mobile-First Styles -->
  <link rel="stylesheet" href="../css/employee-mobile.css">
  
  <!-- Sweetalert -->
  <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">
  <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="../dist/js/urlcheck.js"></script>
  
  <!-- Role-Based Access Control -->
  <style>
    /* Hide action button groups and columns for employee users */
    body[data-user-role="Employee"] .action-buttons-group {
      display: none !important;
    }
    body[data-user-role="Employee"] .action-column {
      display: none !important;
    }
  </style>
  
  <script>
    // Global variable to store user role for runtime checks
    window.currentUserRole = 'Guest';
    
    // Function to hide action buttons/columns based on user role
    function HideActionButtonsForEmployee() {
      if (window.currentUserRole === 'Employee') {
        // Hide all action button groups
        document.querySelectorAll('.action-buttons-group').forEach(function(el) {
          el.style.display = 'none';
        });
        // Hide all action columns
        document.querySelectorAll('.action-column').forEach(function(el) {
          el.style.display = 'none';
        });
      }
    }
    
    // Setup role-based hiding when DOM is ready and role is available
    function SetupRoleBasedSecurity() {
      // Only set up if role has been provided by view.php
      if (window.currentUserRole && window.currentUserRole !== 'Guest') {
        // Set data attribute on body for CSS selectors
        document.body.setAttribute('data-user-role', window.currentUserRole);
        // Apply hiding rules
        HideActionButtonsForEmployee();
      }
    }
    
    // Run when DOM is fully loaded
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', SetupRoleBasedSecurity);
    } else {
      SetupRoleBasedSecurity();
    }
  </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<!--Preloader
<div class="preloader flex-column justify-content-center align-items-center">
  <img class="animation__shake" src="../dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
</div>-->