<?php
	include 'config.php';
	include 'data.php';

	$return['message'] = $gameData;
	echo json_encode($return);
?>