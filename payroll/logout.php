<?php 
session_start();
include_once('../includes/class/User.php'); 
$LoggingOutUser = new User();
$LoggingOutUser->logout();
?>