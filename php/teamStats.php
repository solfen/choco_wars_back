<?php
	include "config.php";
	//$_GET["token"] = "41j44vicu3d9uk3qvff8170ss4";

	if(empty($_GET["token"])) {
		errorMsg("Token is requiered");
	}

	session_id($_GET["token"]);
	session_start();

	if(!isset($_SESSION["teamID"])) {
		errorMsg("You're not connected");
	}


	$connexion = new PDO($source, $user, $pwd);
	$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$request = $connexion->prepare("SELECT `Round`, `Price`,`QualityBudget`,`MarketingBudget`,`Placement`, `Turnover`, `Earnings`, `chocowars_teams`.`Name` FROM `chocowars_teamresults` JOIN `chocowars_teams` ON `TeamID` = `chocowars_teams`.`ID` WHERE `TeamID` = :id && `Turnover` != 0 && `Earnings` != 0");
	$request->execute(array("id" => $_SESSION["teamID"]));
	if ($request->rowCount() > 0) {
		$result = $request->fetchAll(PDO::FETCH_ASSOC);

		$formatedData = array("teamName" => $result[0]["Name"], "statistics" => []);

		for($i = 0; $i < count($result); $i++) {
			$decision = array("year" => $result[$i]["Round"], "decisions" => array(
				"price" => $result[$i]["Price"],
				"qualityBudget" => $result[$i]["QualityBudget"],
				"marketingBudget" => $result[$i]["MarketingBudget"],
				"turnOver" => $result[$i]["Turnover"],
				"earnings" => $result[$i]["Earnings"],
				"place" => []
			));

			$placement = explode("%", $result[$i]["Placement"]);

			for($j = 0; $j < count($placement); $j++) {
				$district = explode("_", $placement[$j]);
				array_push($decision["decisions"]["place"], array("mapDistrictIndex" => $district[0], "stallQuantity" => $district[1]));
			}

			array_push($formatedData["statistics"], $decision);
			$return["message"] = $formatedData;
		}
	}

	echo json_encode($return);
?>