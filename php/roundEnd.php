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

				$request = $connexion->prepare("UPDATE `chocowars_teamresults` SET `Turnover`= :turnOver, `Earnings`= :earnings WHERE `ID` = :id");

				for($i = 0; $i < count($teamDecisions); $i++) {
					$turnOver = 0;
					$costs = $teamDecisions[$i]["QualityBudget"] + $teamDecisions[$i]["MarketingBudget"];
					for($j = 0; $j < count($districts); $j++) {
						$teamStallNb = getTeamStallNb($teamDecisions[$i]["TeamID"], $districts[$j]["TeamsRepartition"]);
						if($teamStallNb == 0) {
							continue;
						}

						$stallsNb = getStallNb($districts[$j]["TeamsRepartition"]);
						$districtMarketShare = $teamStallNb/$stallsNb;

						$district = $gameData["mapDistricts"][$districts[$j]["Index"]];
						$costs += $teamStallNb * $district["stallPrice"];

						foreach($district["population"] as $name => $popType) {
							$customer = $gameData["customers"][$popType["typeName"]];

							$consumersAtractedPerecent = ($customer["priceSensitivity"] * min($districts[$j]["MinPrice"], $customer["maxPrice"]) / $teamDecisions[$i]["Price"]
							+ $customer["qualitySensitivity"] * $teamDecisions[$i]["QualityBudget"] / max($districts[$j]["MaxQualityBudget"], $customer["minQuality"])
							+ $customer["marketingSensitivity"] * $teamDecisions[$i]["MarketingBudget"] / max($districts[$j]["MaxMarketingBudget"], $customer["minMaketing"]))
							/ ($customer["priceSensitivity"] + $customer["qualitySensitivity"] + $customer["marketingSensitivity"]);

							$turnOver += $consumersAtractedPerecent * $districtMarketShare * $popType["quantity"] * $gameData["mapDistricts"][$districts[$j]["Index"]]["totalPopulation"] * $teamDecisions[$i]["Price"];
						}	
					}
					
					$request->execute(array("turnOver" => $turnOver, "earnings" => $turnOver-$costs, "id" => $teamDecisions[$i]["ID"]));
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