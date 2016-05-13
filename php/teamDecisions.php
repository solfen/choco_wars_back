<?php
	include "config.php";
	include "data.php";
	/*$_POST["token"] = "j3cc0he9euslthl7os5v7kj1u2";
	$_POST["decisions"] = '
	{
		"price": 10,
		"qualityBudget": 1500,
		"marketingBudget": 150,
		"place" : [
			{
				"mapDistrictIndex": 0,
				"stallQuantity": 2
			},
			{
				"mapDistrictIndex": 1,
				"stallQuantity": 2
			}
		]
	}';*/
	if(empty($_POST["token"]) || empty($_POST["decisions"])) {
		errorMsg("Token and Decisions are requiered");
	}

	session_id($_POST["token"]);
	session_start();

	if(!isset($_SESSION["teamID"])) {
		errorMsg("You're not connected");
	}

	$decisions = json_decode($_POST["decisions"], true);
	if(empty($decisions["price"]) || empty($decisions["qualityBudget"]) || empty($decisions["marketingBudget"]) || !isset($decisions["place"]) ) {
		errorMsg("Invalid json format. Json needs the keys: 'price', 'qualityBudget', 'marketingBudget' and 'place'");
	}

	if($decisions["price"] <= 0 || $decisions["price"] > $gameData["maximunAmounts"]["productPrice"]) {
		errorMsg("Price is not in an acceptable range");
	}
	if($decisions["qualityBudget"] <= 0 || $decisions["qualityBudget"] > $gameData["maximunAmounts"]["qualityBudget"]) {
		errorMsg("Quality budget is not in an acceptable range");
	}
	if($decisions["marketingBudget"] <= 0 || $decisions["marketingBudget"] > $gameData["maximunAmounts"]["marketingBudget"]) {
		errorMsg("Marketing budget is not in an acceptable range");
	}
	if(count($decisions["place"]) == 0) {
		errorMsg("You need to have at least one stall. Use the map to rent some.");
	}

	$connexion = new PDO($source, $user, $pwd);
	$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if(!canAfford($connexion, $decisions, $gameData)) {
		errorMsg("You don't have enough money!");
	}

	$placement = "";

	try {
		$request = $connexion->prepare("SELECT * FROM `chocowars_districts` WHERE `Index` = :id");
		$request2 = $connexion->prepare("UPDATE `chocowars_districts` SET `MinPrice`=:minPrice,`MaxMarketingBudget`=:maxMarketing,`MaxQualityBudget`=:maxQuality,`TeamsRepartition`=:teamsRepartition WHERE `Index` = :id");

		for($i = 0; $i < count($decisions["place"]); $i++) {
			$request->execute(array("id" => $decisions["place"][$i]["mapDistrictIndex"]));
			if ($request->rowCount() == 0) {
				errorMsg("Wrong Map District: " . $decisions["place"][$i]["mapDistrictIndex"]);
			}

			$result = $request->fetchAll(PDO::FETCH_ASSOC)[0];

			$minPrice = $decisions["price"] < $result["MinPrice"] ? $decisions["price"] : $result["MinPrice"];
			$maxQuality = $decisions["qualityBudget"] > $result["MaxQualityBudget"] ? $decisions["qualityBudget"] : $result["MaxQualityBudget"];
			$maxMarketingBudget = $decisions["marketingBudget"] > $result["MaxMarketingBudget"] ?  $decisions["marketingBudget"] : $result["MaxMarketingBudget"];
			
			if(preg_match("/". $_SESSION["teamID"] . "_[0-9]/", $result["TeamsRepartition"])) 
				$teamsRepartition = preg_replace("/". $_SESSION["teamID"] . "_([0-9]*)/", $_SESSION["teamID"] . "_" . $decisions["place"][$i]["stallQuantity"], $result["TeamsRepartition"]);
			else {
				$teamsRepartition = empty($result["TeamsRepartition"]) ? "" : $result["TeamsRepartition"] . "%";
				$teamsRepartition .= $_SESSION["teamID"] . "_" . $decisions["place"][$i]["stallQuantity"];
			}
			
			$request2->execute(array(
				"id" => $decisions["place"][$i]["mapDistrictIndex"],
				"minPrice" => $minPrice,
				"maxMarketing" => $maxQuality,
				"maxQuality" => $maxMarketingBudget,
				"teamsRepartition" => $teamsRepartition
			));

			$placement .= $decisions["place"][$i]["mapDistrictIndex"] . "_" . $decisions["place"][$i]["stallQuantity"] . "%";
		}
		$placement = substr($placement, 0, -1); // remove last "%"
	}
	catch (PDOExeption $e) {
		errorMsg($e->getMessage());
	}

	try {
		$request = $connexion->prepare("INSERT INTO `chocowars_teamresults`(`ID`, `TeamID`, `Round`, `Price`, `QualityBudget`, `MarketingBudget`, `Placement`, `Turnover`, `Earnings`) VALUES ('', :teamID, (SELECT `CurrentRound` FROM `chocowars_games` WHERE 1 ORDER BY `ID` DESC LIMIT 1), :price, :qualityBudget, :marketingBudget, :placement, '', '')");
		$request->execute(array(
			"teamID" => $_SESSION["teamID"], 
			"price" => $decisions["price"], 
			"qualityBudget" => $decisions["qualityBudget"], 
			"marketingBudget" => $decisions["marketingBudget"], 
			"placement" => $placement
		));

		echo json_encode($return);
	}
	catch (PDOExeption $e) {
		errorMsg($e->getMessage());
	}

	function canAfford($connexion, $decisions, $gameData) {
		$request = $connexion->prepare("SELECT `Earnings` FROM `chocowars_teamresults` WHERE `TeamID` = :id ORDER BY `ID` DESC LIMIT 1");
		$request->execute(array("id" => $_SESSION["teamID"]));
		$fric = $request->rowCount() > 0 ? $request->fetchAll(PDO::FETCH_ASSOC)[0]["Earnings"] : $gameData["initialFinances"];

		$cost = $decisions["qualityBudget"] + $decisions["marketingBudget"];
		for($i = 0; $i < count($decisions["place"]); $i++) {
			$cost += $decisions["place"][$i]["stallQuantity"] * $gameData["mapDistricts"][$decisions["place"][$i]["mapDistrictIndex"]]["stallPrice"];
		}

		return $fric - $cost > -$gameData["maximunAmounts"]["overdraft"];
	}
?>