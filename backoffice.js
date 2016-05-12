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

function timeLeft() {
	DBAccess('timeLeft', 'POST', '', timeLeftReturn);
}

function timeLeftReturn(res) {
	res = JSON.parse(res);

	if(typeof(res.message) == "number") {
		res.message = (res.message / 60 | 0) + "min  " + res.message % 60 + "sec";
	}
	document.getElementById("timeLeft").innerHTML = "Time Left to next round: " + res.message;
	window.setTimeout(timeLeft, 1000);
}