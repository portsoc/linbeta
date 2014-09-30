 window.linora = window.linora || {};
 window.linora.ui = (function () {

	"use strict";

	var 

	page_sections = ['links', 'help', 'add'],

	/*
	This is a Currying closure that creates a new function whose
	id is stored and used when the function is later invoked.
	*/
	saveResponseTextTo = function (id) {
		return function(xhr) {
			document.getElementById(id).innerHTML = xhr.target.responseText;
		};
	},

	flip = function () {
		console.log("flip");
		var r, e, i;

		for (i=0;i<page_sections.length; i=i+1) {
			r = document.getElementById(page_sections[i] + ".flip");
			e = document.getElementById(page_sections[i]);
			if (r.checked) {
				e.classList.remove("stealth");
			} else {
				e.classList.add("stealth");
			}
		}
	},

	attachFlipListeners = function() {
		page_sections.map(
			function(s) {
				var h = document.getElementById(s+".flip");
				h.addEventListener("change", flip);
			}
		);
	},

	setup = function() {
		console.log("setup");
		attachFlipListeners();

	    linora.xhr.load(
	    	{
	    		"url": "help.get.html",
				"callbacks": {
					"load": saveResponseTextTo("help"),
				}
			}
		);
	    linora.xhr.load(
	    	{
	    		"url": "api/links.get.ajax.php",
				"callbacks": {
					"load": saveResponseTextTo("links"),
				}
			}
		);
	    
	    flip();
	};


	return {
		"setup": setup
	}

})();

window.addEventListener('load', linora.ui.setup);

