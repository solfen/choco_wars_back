

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
		$request = $connexion->prepare("SELECT * FROM `chocowars_teamresults` WHERE `Turnover` != 0 || `Earnings` != 0 GROUP BY `TeamID` DESC");
		$request->execute();
		if ($request->rowCount() > 0) {
			$teamDecisions = $request->fetchAll(PDO::FETCH_ASSOC);
			$request = $connexion->prepare("UPDATE `chocowars_teamresults` SET `Earnings`= (`Earnings` + :earnings) WHERE `ID` = :id");
			for($i = 0; $i < count($teamDecisions); $i++) {
				$request->execute(array("earnings" => $_POST["fric"], "id" => $teamDecisions[$i]["ID"]));
			}
		}
	}
	catch (PDOExeption $e) {
		errorMsg($e->getMessage());
	}

	echo json_encode($return);
?>