<?php
	include "config.php";

	$connexion = new PDO($source, $user, $pwd);
	$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$request = $connexion->prepare("SELECT `TeamsRepartition` FROM `chocowars_districts` WHERE 1");
	$request->execute();
	if ($request->rowCount() > 0) {
		$result = $request->fetchAll(PDO::FETCH_ASSOC);

		$request = $connexion->prepare("SELECT `Name` FROM `chocowars_teams` WHERE `ID` = :id");
		$names = [];
		$stallNb = [];

		for($i = 0; $i < count($result); $i++) {
			$names[$i] = [];
			$stallNb[$i] = [];
			$placement = explode("%", $result[$i]["TeamsRepartition"]);

			for($j = 0; $j < count($placement); $j++) {
				$teamPlacement = explode("_", $placement[$j]);
				$request->execute(array("id" => $teamPlacement[0]));
				if($request->rowCount() > 0) {
					array_push($names[$i], $request->fetchAll(PDO::FETCH_ASSOC)[0]["Name"]);
					array_push($stallNb[$i], $teamPlacement[1]);
				}
			}
		}

		$return["message"] = array("names" => $names, "stallsNb" => $stallNb);
		echo json_encode($return);
	}

?>