window.linora = window.linora || {};
window.linora.keys = (function () {

	var
		keys = { enter: 13, esc: 27, up: 38, down: 40 },


		loadpage = function () {
			var first = document.querySelector(".links li.selected a");
			window.location = first.href;
			return false;
		},

		doselect = function(e) {
			// if up or down is pressed, don't do a search;
			switch(e.keyCode) {
				case (keys.up): window.linora.ui.selectElem(false); return false;
				case (keys.down): window.linora.ui.selectElem(true); return false;
				case (keys.enter): loadpage(); return false;
				case (keys.esc): window.linora.ui.clearSearch(""); return false;
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
