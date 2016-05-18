<?php
	function roundEnd($connexion) {
		include 'data.php';
		try {
			$request = $connexion->prepare("SELECT * FROM `chocowars_teamresults` WHERE 1 GROUP BY `TeamID` DESC");
			$request->execute();
			if ($request->rowCount() > 0) {
				$teamDecisions = $request->fetchAll(PDO::FETCH_ASSOC);

				$request = $connexion->prepare("SELECT * FROM `chocowars_districts` WHERE 1");
				$request->execute();
				if ($request->rowCount() > 0) {
					$districts = $request->fetchAll(PDO::FETCH_ASSOC);
				}

				$request = $connexion->prepare("UPDATE `chocowars_teamresults` SET `Turnover`= :turnOver, `Earnings`= :earnings, `Capital` = `Capital` + :turnOver2 WHERE `ID` = :id");
				$altRequest = $connexion->prepare("INSERT INTO `chocowars_teamresults`(`ID`, `TeamID`, `Round`, `Price`, `QualityBudget`, `MarketingBudget`, `Placement`, `Turnover`, `Earnings`, `Capital`) VALUES ('', :teamID, (SELECT `CurrentRound` FROM `chocowars_games` WHERE 1 ORDER BY `ID` DESC LIMIT 1)-1, :price, :qualityBudget, :marketingBudget, :placement, :turnOver, :earnings, :capital)");
				//$debugArr = array();
				for($i = 0; $i < count($teamDecisions); $i++) {
					$turnOver = 0;
					$costs = $teamDecisions[$i]["QualityBudget"] + $teamDecisions[$i]["MarketingBudget"];

					// $debugArr[$teamDecisions[$i]['TeamID']] = array();
					// $debugArr[$teamDecisions[$i]['TeamID']]['disctricts'] = [];

					for($j = 0; $j < count($districts); $j++) {
						$teamStallNb = getTeamStallNb($teamDecisions[$i]["TeamID"], $districts[$j]["TeamsRepartition"]);
						if($teamStallNb == 0) {
							continue;
						}

						$stallsNb = getStallNb($districts[$j]["TeamsRepartition"]);
						$districtMarketShare = $teamStallNb/$stallsNb;

						$district = $gameData["mapDistricts"][$districts[$j]["Index"]];
						$costs += $teamStallNb * $district["stallPrice"];

						// $debugArr[$teamDecisions[$i]['TeamID']]['disctricts'][$j] = array();
						// $debugArr[$teamDecisions[$i]['TeamID']]['disctricts'][$j]["stallNb"] = $stallsNb;
						// $debugArr[$teamDecisions[$i]['TeamID']]['disctricts'][$j]["districtMarketShare"] = $districtMarketShare;
						// $debugArr[$teamDecisions[$i]['TeamID']]['disctricts'][$j]["consumersAtracted"] = array();

						foreach($district["population"] as $name => $popType) {
							$customer = $gameData["customers"][$popType["typeName"]];

							$consumersAtractedPerecent = ($customer["priceSensitivity"] * min($districts[$j]["MinPrice"], $customer["maxPrice"]) / $teamDecisions[$i]["Price"]
							+ $customer["qualitySensitivity"] * $teamDecisions[$i]["QualityBudget"] / max($districts[$j]["MaxQualityBudget"], $customer["minQuality"])
							+ $customer["marketingSensitivity"] * $teamDecisions[$i]["MarketingBudget"] / max($districts[$j]["MaxMarketingBudget"], $customer["minMaketing"]))
							/ ($customer["priceSensitivity"] + $customer["qualitySensitivity"] + $customer["marketingSensitivity"]);

							$consumersAtractedPerecent = min($consumersAtractedPerecent, 1);

							//$debugArr[$teamDecisions[$i]['TeamID']]['disctricts'][$j]["consumersAtracted"][$popType["typeName"]]["atracted"] = $consumersAtractedPerecent;

							$turnOver += $consumersAtractedPerecent * $districtMarketShare * $popType["quantity"] * $gameData["mapDistricts"][$districts[$j]["Index"]]["totalPopulation"] * $teamDecisions[$i]["Price"];
							
							//$debugArr[$teamDecisions[$i]['TeamID']]['disctricts'][$j]["consumersAtracted"][$popType["typeName"]]["pop quantity"] = $popType["quantity"] * $gameData["mapDistricts"][$districts[$j]["Index"]]["totalPopulation"];
						}	

						//$debugArr[$teamDecisions[$i]['TeamID']]['turnOver'] = $turnOver;
						//echo json_encode($debugArr);
					}
					if($teamDecisions[$i]["Turnover"] != 0 || $teamDecisions[$i]["Earnings"] != 0) {
						$altRequest->execute(array(
							"teamID" => $teamDecisions[$i]["TeamID"], 
							"price" => $teamDecisions[$i]["Price"], 
							"qualityBudget" => $teamDecisions[$i]["QualityBudget"], 
							"marketingBudget" => $teamDecisions[$i]["MarketingBudget"], 
							"placement" => $teamDecisions[$i]["Placement"],
							"turnOver" => $turnOver, 
							"earnings" => $turnOver-$costs,
							"capital" => $teamDecisions[$i]["Capital"]+$turnOver
						));
					}
					else {
						$request->execute(array("turnOver" => $turnOver, "turnOver2" => $turnOver, "earnings" => $turnOver-$costs, "id" => $teamDecisions[$i]["ID"]));
					}
				}
			}
		}
		catch (PDOExeption $e) {
			errorMsg($e->getMessage()); 
		}
	}

	function getStallNb($subject) {
		$stalls = [];
		preg_match_all("/\\d_(\\d+)%?/", $subject, $stalls);

		$total = 0;
		for($i = 0; $i < count($subject); $i++) {
			$total += $subject[$i];
		}
		return $total;
	}

	function getTeamStallNb($teamId, $subject) {
		$matches = [];
		preg_match("/%?". $teamId ."_(\\d+)%?/", $subject, $matches);
		return isset($matches[1]) ? $matches[1] : 0;
	}
?>