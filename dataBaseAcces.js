"use strict";

function DBAccess (id, method, param, callback) { //param is a string of type application/x-www-form-urlencoded

	var url = 'php/' + id + ".php";
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.withCredentials = true;
	xmlhttp.open(method, url, true);
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlhttp.onreadystatechange = function (aEvt) {
		if(xmlhttp.readyState == 4) {
			if(xmlhttp.status  == 200) {
				if(callback) {
					callback(xmlhttp.responseText);
				}			
			}
		}
	}
	xmlhttp.send(param);
}
