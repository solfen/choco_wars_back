<?php
	include 'config.php';
	include 'data.php';
	include 'roundEnd.php';

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
		$request = $connexion->prepare("SELECT `ID`, `TimeStart`, `CurrentRound` FROM `chocowars_games` WHERE 1 ORDER BY `ID` DESC LIMIT 1");
		$request->execute();
		if ($request->rowCount() > 0) {
			$result = $request->fetchAll(PDO::FETCH_ASSOC)[0];
			$timeToAdd = $gameData["roundDuration"]*$result["CurrentRound"] - (time() - strtotime($result["TimeStart"]));

			$request = $connexion->prepare("UPDATE `chocowars_games` SET `TimeStart`= DATE_ADD(`TimeStart`, INTERVAL -:time second) WHERE ID = :id");
			$request->execute(array('time' => $timeToAdd, 'id' => $result["ID"] ));

			$request = $connexion->prepare("UPDATE `chocowars_games` SET `CurrentRound`= (`CurrentRound`+1) WHERE ID = :id");
			$request->execute(array('id' => $result["ID"] ));
			roundEnd($connexion);
		}
	}
	catch (PDOExeption $e) {
		errorMsg($e->getMessage());
	}

	echo json_encode($return);
?>