<?php
	include 'config.php';

	if(empty($_POST['pwd']) || $_POST['pwd'] != $rootPwd) {
		$return["statusCode"] = 400;
		$return["message"] = "password is wrong";
		echo json_encode($return);
		die();
	}
	
	session_start();
	$_SESSION["root"] = true;

	echo json_encode($return);
?>