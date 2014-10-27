window.linora = window.linora || {};
window.linora.drag = (function () {

	"use strict";

	var listener = {

		"over": function(e) {
			e.preventDefault();
			e.stopPropagation();
			document.getElementById("dropalert").classList.remove("stealth");
		},

		"drop": function(e) {
			e.preventDefault();
			e.stopPropagation();
			document.getElementById("postageurl").value = e.dataTransfer.getData("Text");
			document.getElementById("dropalert").classList.add("stealth");
			document.getElementById("add.flip").checked = true;	
			window.linora.ui.flip();
			window.linora.ui.populateFromDoc();
		},

		"end": function(e) {
			e.preventDefault();
			e.stopPropagation();
			document.getElementById("dropalert").classList.add("stealth");
		}


	},

	registerDragListeners = function () {

		// this makes a drop possible 
		// remove it and drop events cannot occur.
		window.addEventListener( "dragover", listener.over );

		// handle what happens when a drag drop occurrs
		window.addEventListener( "drop", listener.drop );

		// handle what happens when a drag drop occurrs
		window.addEventListener( "dragend", listener.end );

		// handle what happens when a drag drop occurrs
	//	target.addEventListener( "dragleave", drag.end );
	};

	return {
		"register": registerDragListeners
	};
}());

window.addEventListener( "load", window.linora.drag.register );
