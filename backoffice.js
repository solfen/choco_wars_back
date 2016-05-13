function startNewGame() {
	if(confirm("Are you sure ? it will stop the current game.")) {
		DBAccess('startGame', 'POST', '', startingReturn);
	}
}

function startingReturn(res) {
	res = JSON.parse(res);

	if(res.statusCode != 200) {
		alert(res.message);
	}
}

function endRound() {
	if(confirm("Are you sure ?")) {
		DBAccess('endRound', 'POST', '', endRoundReturn);
	}
}

function endRoundReturn(res) {
	res = JSON.parse(res);

	if(res.statusCode != 200) {
		alert(res.message);
	}
}

function endGame() {
	if(confirm("Are you sure ? it will end the current game.")) {
		DBAccess('endGame', 'POST', '', startingReturn);
	}
}

function addFricToAll() {
	DBAccess('addFric', 'POST', 'fric=' + fricToAddInput.value, startingReturn);
}

function timeLeft() {
	DBAccess('timeLeft', 'POST', '', timeLeftReturn);
}

function timeLeftReturn(res) {
	res = JSON.parse(res);

	document.getElementById("timeLeft").innerHTML = res.message;

	if(res.statusCode == 200 && res.message.timeLeft) {
		res.message.timeLeft = (res.message.timeLeft / 60 | 0) + "min  " + res.message.timeLeft % 60 + "sec";
		document.getElementById("timeLeft").innerHTML = "Round: " + res.message.round + "<br>Time Left: " + res.message.timeLeft;
	}

	window.setTimeout(timeLeft, 1000);
}