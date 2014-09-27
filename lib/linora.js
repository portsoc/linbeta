
// LINORA.JS - miscellaneous javascript code for Linora's index.php
// =========
// 17 Aug 2013	v1b.iii	KL	FLIP function moved here
//
// 21 Aug 2013	v1c.i	KL	Moved the AJAX of help and review here.
//				ENCOUNTERED A BUG IN KLIB.AJAX.GET: two
//				calls, too close together, get into a
//				timing race... so ?need an XMLHttpRequest
//				object per call?
// 19 Aug 2014	v1e.ii	KL	Rename this to Linora.js: rename
//				API/links.get.php to links.get.ajax.php.
//				Remove the REVIEW option.
logging = true; // Whether we're tracking using console.log

/* ---- Setting up and doing ONLOAD ---- */

function setup() { // Find the checkboxes
    var h = document.getElementById("helpflip")
    var a = document.getElementById("addflip")
    var l = document.getElementById("linksflip")
        // Clear the checkboxes
    h.checked = a.checked = l.checked = false;
    // Set up their event handlers
    h.onchange = function(event) {
        flip('help')
    }
    a.onchange = function(event) {
        flip('add');
    }
    l.onchange = function(event) {
        flip('links')
    }

    // Set up the "submit to add an entry" event handler
    var p = document.getElementById("postage")
    p.onsubmit = function(event) {
        InsertEntry();
        return false;
    };
    // Return FALSE to prevent reload

    // Make initial calls to load the HELP and LINKS parts.
    klib.ajax.get("help.get.html", saveToHelpDiv);
    klib.ajax.get("API/links.get.ajax.php", saveToLinksDiv);
    // Calls the ajax.get function with two parameters:
    // the first is the string of the URL to get
    // from; the second is a reference-value to the
    // function-object declared as saveTo... (see later).
    // alert ("set up");
}

window.onload = setup;


/* ---- Turning display of the a part on/off ---- */

function flip(here) {
    var elem = document.getElementById(here)
        .style;
    if (elem.display == "none")
        elem.display = "block";
    else elem.display = "none";
}


/* ---- POSTing a form entry, ultimately to ENTRY.INSERT.AJAX.PHP ----*/

function InsertEntry() {
    var p = document.getElementById("postage")
    var c = p.cat.value;
    if (c == "") c = "?";
    var u = p.url.value;
    var k = p.cap.value;
    if (k == "") k = u;
    // alert("cat/url/cap = /"+c+"/"+u+"/"+k+"/")
    var x = {
        cat: c,
        url: u,
        cap: k
    }
    if (logging)
        console.log("**** Calling klib.ajax.post")
    klib.ajax.post("API/entry.insert.ajax.php", x, saveToBadInsertDiv);
    if (logging)
        console.log("**** Returned from Calling klib.ajax.post")
    p.cat.value = p.url.value = p.cap.value = ""
    klib.ajax.get("API/links.get.ajax.php", saveToLinksDiv) // refresh
}


/* ---- Callback functions for AJAX'ing the help and links, also any error
	message on an insert ----*/

function saveToHelpDiv(text) {
    document.getElementById("helpDiv")
        .innerHTML = text;
}

function saveToLinksDiv(text) {
    if (logging) console.log("preGetLinks");
    document.getElementById("linksDiv")
        .innerHTML = text;
    if (logging) console.log("postGetLinks");
}

function saveToBadInsertDiv(text) {
    if (logging) console.log("INSERT-POST callbacking");
    if (text)
        document.getElementById("badInsertDiv")
        .innerHTML = "??" + text;
    if (logging) console.log("INSERT-POST callbacked");
}