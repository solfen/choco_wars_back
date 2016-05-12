<?php
	include 'config.php';
	include 'data.php';
	include 'roundEnd.php';

	$connexion = new PDO($source, $user, $pwd);
	$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	try {
		$request = $connexion->prepare("SELECT `ID`, `TimeStart`, `CurrentRound` FROM `chocowars_games` WHERE 1 ORDER BY `ID` DESC LIMIT 1");
		$request->execute();
		if ($request->rowCount() > 0) {
			$result = $request->fetchAll(PDO::FETCH_ASSOC)[0];

			$timeLeft = $gameData["roundDuration"]*$result["CurrentRound"] - (time() - strtotime($result["TimeStart"]));

			//TODO: test if round number is not one too much
			if($result["CurrentRound"] <= $gameData["roundsNb"]) {
				if($timeLeft <= 0) {
					$request = $connexion->prepare("UPDATE `chocowars_games` SET `CurrentRound`= (`CurrentRound`+1) WHERE ID = :id");
					$request->execute(array('id' => $result["ID"] ));
					roundEnd($connexion);
				}

				$return["message"] = $timeLeft;
				echo json_encode($return);
			}
			else {
				errorMsg("Game end");
			}
		}
		else {
			errorMsg("Game not started");
		}
	}
	catch (PDOExeption $e) {
		errorMsg($e->getMessage()); 
	}
?>