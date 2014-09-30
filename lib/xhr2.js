window.linora = window.linora || {};
window.linora.xhr = (function () {

	"use strict";

	var

	/*
	Given a payload that is an object (containing name/value pairs), this function 
	converts that array into a URLEncoded string.
	*/
	encodePayload = function (x) {
		var i, payload = "";
		for (i in x) {
			if (x.hasOwnProperty(i)) {
				payload += i + "=" + encodeURIComponent(x[i]) + "&";
				payload = payload.replace('%20', '+');
				payload = payload.replace('%3D', '=');
			}
		}
		payload = payload.slice(0, -1);
		//console.log("Payload Type", typeof(x));
		//console.log("Pay", payload);
		return payload;
	},

	/*
	Take an object with multiple parameters (in any order)
	Callback: keys are the events that are listened to and values are the functions that are called back.
	{
		"url": "url to be loaded",
		"callbacks": {
			"load": function() { console.log("foo"); },
			"error": function() { console.log("bar"); }
		},
		"data": "the data the needs to be encoded (an object)",
		"method": "HTTP method"
	}
	*/
	load = function (p) {

		var i, payload, xhr = new XMLHttpRequest();

		// default to async = true if not defined
		p.async = p.async || true;

		if (!p.method) {
			p.method = "GET"; // default if undefined
		}

		xhr.open(p.method, p.url, p.async);

		if (p.accept) {
			xhr.setRequestHeader("Accept", p.accept);
		} else {
			// by default, if nothing else is specified, request JSON
			xhr.setRequestHeader("Accept", "application/json");
		}

		if (p.method === "POST") {
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			if (p.query) {
				//console.log('prepping payload')
				payload = encodePayload(p.query);
			}
		}

		for (i in p.callbacks) {
			if (p.callbacks.hasOwnProperty(i)) {
				//console.log("Adding EventListener to xhr ", i, p.callbacks[i]);
				xhr.addEventListener(i, p.callbacks[i]);
			}
		}

		xhr.send(payload);
	};


	return {
		"load": load
	};

}());