// (AJAX.POST).JS	(C) Kit Lester 2008. Minor tweaks 2013, 2014.
// ==============
			/* Utility in the klib library for simple AJAX calls
			 * which POST content to an active web entity (e.g.a
			 * php-written API element) over http. */

$asynch = true;  // else synch

logging = true;	// whether messaging console.log


// ***************************************************
// ******** Depends on (STRING.URIENCODES).JS ********
// ***************************************************


// The  following 2 lines make sure that the klib library  object  exists, and
// then that that the klib.ajax sublibrary object exists
		
if (!window.klib)	window.klib	  = {}	    // NB: {} is equivalent
if (!window.klib.ajax)	window.klib.ajax  = {}	    // to "new Object()"



// KLIB.AJAX.CONSTRUCTOBJ() creates  a new Ajax  object by  calling  whatever
// XMLHttpRequest constructor is supported by the browser being used.

// An identical klib.ajax.constructObj function appears in each
//	klib/(ajax.whatever).js
// file, so in  practice  if more than one are  loaded  they  share the single
// last-loaded instance of it.

klib.ajax.constructObj = function()
  { if (window.XMLHttpRequest)
	return new XMLHttpRequest();
    if (window.ActiveXObject)
	return new ActiveXObject("Microsoft.XMLHTTP");
    if (logging) console.log("This browser does not support AJAX.");
    return false;};



// KLIB.AJAX.POST()  makes an Ajax call to the  specified  URL to convey  POST
// queries given by the second parameter to the specified URL.

// When the responder at that URL has completed, the call-back  function given
// by the third parameter will be called with any response to come back to the
// browser as it's only parameter.

// Since this is a POST, the url should normally  specify  something active on
// the server  (e.g. a PHP script or a servlet) which does  something with the
// HTTP queries. So the second parameter of klib.ajax.post()is  expected to be
// of one of the forms
//		"name1=value1&name2=value2..."		(i.e. a string)
//		{"name1":value1,"&name2":value2...}	(i.e. an object)
//		null					(or 0 or "" or ...)
//     * If it's a string, it will be used  directly as the POST queries to be
//       sent in the POST's HTTP header: in particular, it should already have
//       had any URI-encoding that is needed.
//     * If it's an  object, the  "value"  parts of  non-null/false  name/value
//       pairs will be URI-encoded, and the resulting  queries will be sent as
//       the POST queries.
//     * If it's  NULL  (orequivalent),  the POST  will  usually  be to merely
//       notify the API that  something  has  happened  (e.g. some  change  of
//       status).

// POSTs do not normally request  information from the URL, (and so won't need
// URL to end with a query string of parameters - i.e. NOT a sequence of form
//     ?name1=value1&name2=value2...

// However, if error  messages or other  unusual  responses are expected, then
// the third parameter should be a a callback(...)  function to be called with
// the error  message (etc) or other  non-XML text  response text as its' only
// parameter. If the application has no need for such callback, then the third
// parameter can be null.

klib.ajax.post = function(URL, HTTPqueries, callback)
  { // Make HTTPqueries safe for use as a POSTable query-set
    HTTPqueries = ( !HTTPqueries
		  ? null// in case there should be no POSTs
		  : typeof HTTPqueries == "string"
		  ? HTTPqueries // better not need URIencoding!
		  : klib.string.URIQueriesOf(HTTPqueries) ) //must be obj
    // Now set up the ajaxObj.
    var ajaxObj = klib.ajax.constructObj(); // One day: handle FALSE return
    ajaxObj.open("POST", URL, $asynch);
    ajaxObj.setRequestHeader("Content-type",
			     "application/x-www-form-urlencoded");
    ajaxObj.onreadystatechange =
		function(){ if ( callback )	// I.e. if call-back is needed.
			      { if (logging) console.log("state "+ajaxObj.readyState+"/"+
				    		     ajaxObj.status, ajaxObj);
				if (ajaxObj.readyState == 4    // One day, handle bad
				    && ajaxObj.status == 200)  // state/status pair.
				      { if (logging) console.log("calling back from ajax.post");
					callback(ajaxObj.responseText);
					if (logging) console.log("called back from ajax.post"); }}}
    // Now do the SEND.
    if (logging) console.log("************ about to send to "+URL+" from ajax.post");
    ajaxObj.send(HTTPqueries); 
    if (logging) console.log("************ send from ajax.post is complete");
  };
