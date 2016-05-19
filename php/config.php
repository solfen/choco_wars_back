<?php

	$source = 'mysql:host=127.0.0.1;dbname=sg_chocowars';
	$user = 'root';
	$pwd = '';

	$rootPwd = "pifpif";
	$return = [ "statusCode" => 200, "message" => ""];

	function errorMsg($msg) {
		$return["statusCode"] = 500;
		$return["message"] = $msg;
		echo json_encode($return);
		die();
	}
?>
