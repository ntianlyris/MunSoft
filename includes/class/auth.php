<?php
//Start session
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if ($_SESSION['login'] == false) {
	session_regenerate_id();
	session_destroy();
	header("Location: ../");
	exit();
}

?>