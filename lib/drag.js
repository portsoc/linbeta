window.rdfx = window.rdfx || {};

window.rdfx.drag = (function () {

	"use strict";

	var listener = {

		"over": function(e) {
			e.preventDefault();
			e.stopPropagation();
			console.log("drag over");
			document.getElementById("dropalert").classList.remove("stealth");
		},

		"drop": function(e) {
			console.log("drag drop");
			e.preventDefault();
			e.stopPropagation();
			console.log("e", e.dataTransfer.getData('Text'));
			document.getElementById("postageurl").value = e.dataTransfer.getData("Text");
			document.getElementById("dropalert").classList.add("stealth");
			document.getElementById("add.flip").checked = true;	
			window.linora.ui.flip();
			window.linora.ui.populateFromDoc();
		},

		"end": function(e) {
			console.log("drag end");
			e.preventDefault();
			e.stopPropagation();
			console.log("end", e.dataTransfer.getData('Text'));
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

window.addEventListener( "load", rdfx.drag.register );
