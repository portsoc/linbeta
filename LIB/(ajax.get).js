// (AJAX.GET).JS	(C) Kit Lester 2008. Minor tweaks 2013, 2014
// =============
/* Utility in the klib library for simple AJAX calls
 * which GET a file content (or php output, or...)
 * over http. */
asynch = true; // else synch

logging = true; // whether messaging console.log


// The  following 2 lines make sure that the klib library  object  exists, and
// then that that the klib.ajax sublibrary object exists

if (!window.klib) window.klib = {}; // NB: {} is equivalent
if (!window.klib.ajax) window.klib.ajax = {}; // to "new Object()"


// KLIB.AJAX.CONSTRUCTOBJ()  creates  a new Ajax  object by  calling  whatever
// XMLHttpRequest constructor is supported by the browser being used.

// An identical klib.ajax.constructObj function appears in each
//	klib/(ajax.whatever).js
// file, so in  practice  if more than one are  loaded  they  share the single
// last-loaded instance of it.

klib.ajax.constructObj = function() {
    if (window.XMLHttpRequest)
        return new XMLHttpRequest();
    if (window.ActiveXObject)
        return new ActiveXObject("Microsoft.XMLHTTP");
    if (logging) console.log("This browser does not support AJAX.");
    return false;
};


// KLIB.AJAX.GET() makes an Ajax call to the specified URL to obtain a non-XML
// text: it then calls the  callback(...)  function  with the obtained text as
// the only parameter.

// Since this is a GET, if the url  specifies  something  active on the server
// (e.g. a PHP  script or a servlet) then the url may end with a query  string
// of parameters to the php/servlet/whatever - i.e. a sequence of form
//     ?name1=value1&name2=value2...

// POSTs normally  requests  information  from the url: so the third parameter
// must be a  callback(...)  function  to be  called  with  the  non-XML  text
// response text as its' only parameter.

klib.ajax.get = function(URL, callback) {
    var ajaxObj = klib.ajax.constructObj(); // One day: handle FALSE return
    if (logging) console.log("ajax.get got an obj");
    ajaxObj.open("GET", URL, asynch);
    ajaxObj.onreadystatechange = function() {
        if (logging)
            console.log("state " + ajaxObj.readyState + "/" + ajaxObj.status, ajaxObj);
        if (ajaxObj.readyState == 4) // One day: handle bad paies of values
            if (ajaxObj.status == 200) {
            if (logging)
                console.log("calling back from ajax.get");
            callback(ajaxObj.responseText);
            if (logging)
                console.log("called back from ajax.get");
        }
    };
    if (logging)
        console.log("************ about to send to " + URL + " from ajax.get");
    ajaxObj.send(null);
    if (logging)
        console.log("************ ajax.get send is complete");
};