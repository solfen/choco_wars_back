<?php
	include "config.php";

	$connexion = new PDO($source, $user, $pwd);
	$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$request = $connexion->prepare("SELECT `Round`,`Turnover`, `Earnings`, `chocowars_teams`.`Name` FROM `chocowars_teamresults` JOIN `chocowars_teams` ON `TeamID` = `chocowars_teams`.`ID` WHERE 1 ");
	$request->execute();
	if ($request->rowCount() > 0) {
		$result = $request->fetchAll(PDO::FETCH_ASSOC);

		$formatedData = [];
		$curentTeam = "";

		for($i = 0; $i < count($result); $i++) {
			if($result[$i]["Name"] != $curentTeam) {
				$curentTeam = $result[$i]["Name"];
				array_push($formatedData, array("teamName" => $curentTeam, "results" => []));
			}

			array_push($formatedData[count($formatedData)-1]["results"], array("year" => $result[$i]["Round"], "turnOver" => $result[$i]["Turnover"], "earnings" => $result[$i]["Earnings"]));
		}

		$return["message"] = $formatedData;
		echo json_encode($return);
	}

?>