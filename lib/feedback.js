window.linora = window.linora || {};
window.linora.feedback = (function () {

	createEntry = function (msg, cls) {
		var e = document.createElement("p");
		if (cls) {
			e.classList.add(cls);
		}
		e.innerHTML = "[x] " + msg;

		e.addEventListener(
			"click",
			function (e) {
				// var fbz = document.getElementById("feedbackzone");
				// fbz.classList.add("fedback");
				// fbz.innerHTML = "";
				// e.target.parentNode.removeChild(e.target);
				e.currentTarget.classList.add("fedback");
				setTimeout(
					function() {
						e.target.parentNode.removeChild(e.target);
					},
					1000
				);
			}
		);
		return e;
	},

	remove = function( elem ) {
	}

	log = function (msg, cls) {
		var fb = document.getElementById("feedback");
		console.log("LOG", msg);
		fb.appendChild(
			createEntry(msg, cls)
		);
	},

	logMeta = function (meta) {
		if (meta.display === undefined || meta.display === true) {
			feedback(
				meta.msg,
				"cls" + meta.ok
			);
		}
		console.log(meta);
	},

	welcome = function() {
		log("Welcome to Linora!  Acknowledge messages like this with a click or tap.");
	};

	return {
		"log": log,
		"logMeta": logMeta,
		"welcome": welcome
	}

})();

window.addEventListener('load', linora.feedback.welcome);

