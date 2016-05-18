<?php
	include 'config.php';
	include 'data.php';

	session_start();
	$_SESSION["root"] = true;
	if(empty($_SESSION["root"])) {
		$return["statusCode"] = 400;
		$return["message"] = "Not logged in!";
		echo json_encode($return);
		die();
	}

	$connexion = new PDO($source, $user, $pwd);
	$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	try {
		$request = $connexion->prepare("INSERT INTO `chocowars_games`(`ID`, `CurrentRound`) VALUES ('',1)");
		$request->execute();
		$request = $connexion->prepare("TRUNCATE `chocowars_teamresults`");
		$request->execute();
		$request = $connexion->prepare("UPDATE `chocowars_districts` SET `MinPrice`=:maxPrice,`MaxMarketingBudget`=0,`MaxQualityBudget`=0,`TeamsRepartition`='' WHERE 1");
		$request->execute(array("maxPrice" => $gameData["maximunAmounts"]["productPrice"]));
	}
	catch (PDOExeption $e) {
		errorMsg($e->getMessage());
	}

	echo json_encode($return);
?>