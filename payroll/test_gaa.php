<?php
session_start();
$_SESSION['uid'] = 1;

// Simulate request
$_POST['action'] = 'fetch_department_status';
$_POST['department_id'] = 1;
$_POST['employment_type'] = 'Regular';

// Include the handler
require_once 'D:/xampp/htdocs/MunSoft/payroll/gaa_netpay_handler.php';
