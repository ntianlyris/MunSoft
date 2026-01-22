<?php
	//Start session
	session_start();

	if($_SESSION['login'] == false){
		session_regenerate_id();
		session_destroy();
		header("Location: ../");
		exit();
	}

?>