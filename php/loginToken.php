<?php
	include 'config.php';
	//$_GET["teamName"] = "Les semi croustillants";
	//$_GET["pwd"] = "ON_EN_A_GROS";

	$connexion = new PDO($source, $user, $pwd);
	$connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if(empty($_GET['teamName']) || empty($_GET['pwd'])) {
		$return["statusCode"] = 400;
		$return["message"] = "Team name and password are required ". "name: " . $_GET['teamName'] . " pwd: " .  $_GET['pwd'];
		echo json_encode($return);
		die();
	}

	if(!preg_match("/^[a-zA-Z0-9]*$/", $_GET['teamName'])) {
		errorMsg("Team name must contain only letters and numbers");
	}
	
	try {
		$request = $connexion->prepare("SELECT `ID`, `Password` FROM chocowars_teams WHERE `Name` = :name");
		$request->execute(array('name'=>$_GET['teamName']));
		if ($request->rowCount() == 0) {
			$request = $connexion->prepare("INSERT INTO `chocowars_teams`(`ID`, `Name`, `Password`) VALUES ('', :name, :pwd)");
			$request->execute(array('name'=>$_GET['teamName'], 'pwd' => password_hash($_GET['pwd'], PASSWORD_DEFAULT)));
			session_start();
			$_SESSION["teamID"] = $connexion->lastInsertId();
			$return["message"] = session_id();
		}
		else {
			$result = $request->fetchAll(PDO::FETCH_ASSOC)[0];
			if(!password_verify($_GET['pwd'], $result['Password'])) {
				$return["statusCode"] = 400;
				$return["message"] = "Wrong password";
				echo json_encode($return);
				die();
			}

			ini_set("session.use_cookies", 0);
			session_start();
			$_SESSION["teamID"] =  $result['ID'];
			$return["message"] = session_id();
		}
	}
	catch (PDOExeption $e) {
		errorMsg($e->getMessage());
	}

	echo json_encode($return);
?>