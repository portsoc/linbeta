window.linora = window.linora || {};
window.linora.keys = (function () {

	var
		keys = { enter: 13, esc: 27, left: 37, up: 38, right: 39, down: 40 },


		loadpage = function () {
			var first = document.querySelector(".links li.selected a");
			window.location = first.href;
			return false;
		},

		doselect = function(e) {

			var search;

			// keys that work anytime
			switch(e.keyCode) {
				case (keys.esc): window.linora.ui.clearSearch(""); return false;	
				case (keys.left): window.linora.ui.panelAdvance(-1); return false;
				case (keys.right): window.linora.ui.panelAdvance(1); return false;
			}

			// only respond to keys if in the links page
			if (document.getElementById("links.flip").checked) {

				// if up or down is pressed, don't do a search;
				switch(e.keyCode) {
					case (keys.up): window.linora.ui.selectElem(false); return false;
					case (keys.down): window.linora.ui.selectElem(true); return false;
					case (keys.enter): loadpage(); return false;
					default:
						search = document.getElementById("search");
						if ( search != document.activeElement) {
							// a kay has been pressed in the links page
							// and the search box has no focus
							// so give it focus
							search.focus();
						}
						return false;
				}
			}
		},

		setup = function()	{
			document.addEventListener( "keydown", doselect );
		}

	return {
		"setup": setup
	};
}());

window.addEventListener("load", window.linora.keys.setup);      
