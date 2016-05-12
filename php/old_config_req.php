<?php
session_start();
include 'config.php';

$connexion = new PDO($source, $user, $pwd);
$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(!isset($_SESSION['teamID'])) {
	$return["statusCode"] = 403;
	$return["message"] = "You are not logged in!";
	echo json_encode($return);
	die();
}

//not so elegant but at least it works
try {
	$request = $connexion->prepare("SELECT * FROM chocowars_config WHERE 1");
	$request->execute();
	if ($request->rowCount() > 0) {
		$resultConfig = $request->fetchAll(PDO::FETCH_ASSOC)[0];
		$request = $connexion->prepare("SELECT * FROM chocowars_customerstypes WHERE 1");
		$request->execute();
		if($request->rowCount() > 0) {
			$resultCustomers = $request->fetchAll(PDO::FETCH_ASSOC);
			$request = $connexion->prepare("SELECT * FROM chocowars_customerstypes WHERE 1");
			$request->execute();
			if($request->rowCount() > 0) {
				$resultMap = $request->fetchAll(PDO::FETCH_ASSOC);
				dbNotEmpty();
			}
			else {
				dbNotEmpty();
			}
		}
		else {
			dbNotEmpty();
		}

	}
	else {
		dbEmpty()
	}
}
catch (PDOExeption $e) {
	$return["statusCode"] = 500;
	$return["message"] = $e->getMessage();
	echo json_encode($return);
	die();
}

function dbEmpty() {
	$return["statusCode"] = 500;
	$return["message"] = "Data base is empty!";
	echo json_encode($return);
	die();
}

function dbNotEmpty() {
	$returnArr = [];
	$returnArr["initialFinances"] = $resultConfig["StartFric"];
	$returnArr["maxOverdraft"] = $resultConfig["maxOverdraft"];
	$returnArr["maximunAmounts"] = [
		"productionInvestment" => $resultConfig["MaxQualityBudget"];
		"promotionInvestment" => $resultConfig["MaxMarketingBudget"];
		"unitPrice" => $resultConfig["MaxProductPrice"];
	]

	$returnArr["customers"] = [];
	for($i = 0; $i < count($resultCustomers); $i++) {
		$returnArr["customers"][$resultCustomers[$i]['Name']] = [
			"name" => $resultCustomers[$i]['Name'],
			"priceSensitivity" => $resultCustomers[$i]['PriceImportance'],
			"qualitySensitivity" => $resultCustomers[$i]['QualityImportance'],
			"promotionSensitivity" => $resultCustomers[$i]['MarketingImportance']
		]
	}

	$returnArr["mapDistricts"] = [];
	for($i = 0; $i < count($resultMap); $i++) {
		$returnArr["mapDistricts"][$i] = [
			"name" => $resultMap[$i]['Name'],
			"stallPrice" => $resultMap[$i]['StallPrice'],
			"population" => [],
		];			

		$customers = explode("%", $resultMap[$i]['Population'])
		for($j = 0; $j < count($customers); $j++) {
			$data = explode("_", $customers[$j]);
			$returnArr["mapDistricts"][$i]["population"] = [
				"quantity" => $data[0],
				"typologyName" => $resultCustomers[$data[1]-1]['Name'] // $data[1] is the ID of customerType so if the DB is indexed on base 1, it works
			]
		}
	}
}
?>
