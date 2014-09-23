<?php	// This file is .PHP only to hide this comment from view>source, and
	// to provide the following variable.

	$vsn =	"v1e (PDO)";

// LINORA - LINK ORGANIZER AND ARCHIVER -- version **** v1e = v1d+PDO ****
// ====================================  (c) Kit Lester 2013

// 14 Aug 2013	v0a KL	Very sketchy first draft
// 14 Aug 2013	v0b KL	Introduced PHP to read and (in a separate fileURL.php
//			page) write the links.txt file, Also (1) CSS and (2)
//			fieldset round the form.
// 14 Aug 2013	v0c KL	Merged the other page into this one to remove the
//			need to go BACK. Output a CR-LF to LINKS.TXT after
//			writing an URL.  Output a sensible message if the
//			LINKS,TXT is not there to read. CSS to soften the
//			fieldset. Define the name of the file as a constant,
//			to avoid the risks of having multiple string copies
//			of the name. Start writing comments!
// 14 Aug 2013	v0d KL	Dealt with the bug that at first load there isn't a
//			URL to write. Introduce categories and captions.
// 16 Aug 2013	v1a KL	Hide/show the four parts: also many cosmetic
//			improvements.
// 17 Aug 2013	v1b KL	Moved the JS to be LIB/MISC.JS.
// 23 Aug 2013	v1c.ii	(Done ahead of plan) AJAX GETs of the help and review.
// 23 Aug 2012	v1c.i	Hide the REVIEW checkbox if there's no file to
//			display.. Was trivial DHTML.
// 25 Aug 2013	aside	Tools to faciliate the next stage - to upload from
//			text-file of links to database, to download, to view.
//			Also a file of templates of frewuent code in HTML,
//			PHP-calling-MYSQL.
// 25 Aug 2012	v1ciii	AJAX load of the links from the Database to Linora: 
//			new API/LOAD.AJAX.PHP; consequent changes to
//			LIB/MISC.JS. Rippled background for the display of
//			links took the overall CSS to a point where it was
//			worth moving to a new file LIB/LINORA.CSS. Debugged
//			the API element by direct execution from the browser,
//			THEN did the index.html & js to AJAX-call it. ALL
//			ACTUALLY VERY EASY with the tools & templates.
// 26 Aug 2012	aside	Tool to remove duplicates - a matching mod to the
//			upload tool gave understanding of several possible
//			techniques for "the real thing" to avoid inserting
//			duplicates, given that SQL lacks an INSERT IF NOT
//			EXISTS statement.
// 26 Aug 2012	v1c.iv	Insert to the database: done in three steps.First was
//			API/INSERT.AJAX.PHP to take cat/url/cap from
//			$_REQUEST, tested direct from the browser - so no
//			change yet here in the index.html (except this
//			comment) or in the javascript.
// 27 Aug 2013	part 2	Second step was making all the the changes here.
// 28 Aug 2013	part 3	Third part was moving the JS & event-handler to MISC
// 15 Sep 2013	v1d KL	Update the display of links when an insertion is done
//			(crude!) & assorted clean-up.
// 06 Aug 2014	v1e KL	PDO in DB_Easy, in place of mysqli_: simple query
// 17 Aug 2014  v1eii	Prepared statements in entry.insert, links,get, and
//			DB_Easy. Rename LIB/misc.js to linota.js, and
//			API/links.get.php to links.get.ajax.php.
//			Drop the REVIEW option.


?>
<!-- (c) C Lester 2013, 2014 -->

<!DOCTYPE HTML5>
<html>

    <head>
	<title>LinOrA <?php echo $vsn;?> - Link Organizer and Archiver</title>
	<meta name="description" content="LinOrA - an Organizer and Archiver for Web-links"/>
	<meta name="author" content="Kit Lester"/>
	<meta name="keywords" content="Web-link, Organizer, Archiver"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="content-script-type" content="text/javascript"/>

	<link rel="shortcut icon" href="favicon.ico"/>


	<link type="text/css" rel="stylesheet" href="LIB/linora.css" media="all">

	<script src="LIB/(string.uriencodes).js"></script>
	<script src="LIB/(ajax.get).js"></script>
		<!-- defines klib.ajax.get(URL,callback) -->
	<script src="LIB/(ajax.post).js"></script>
		<!-- defines klib.ajax.put(URL,HTTPqueries,callback:
			depends on (string.uriencodes) -->
	<script src="LIB/linora.js"></script>
		<!-- Linora's misc JS: all the setup, all the event-handlers,
		     all the AJAXing. -->

    </head>

    <body lang="EN-GB">

	<div class="header">
	    <div class="title"><big>&nbsp;<big><big><b>Linora</b>&nbsp;&nbsp;&nbsp;&nbsp;
				</big></big></big></div>
	    <div class="shows">Show&hellip;&nbsp;
		    <input type="checkbox" id="helpflip"/>help&nbsp;
		    <input type="checkbox" id="addflip"/>add&nbsp;
		    <input type="checkbox" id="linksflip"/>links
	    </div>
	</div>


	<div class="hack"><p>&nbsp;<br>&nbsp;</p><div>

	<fieldset id="add" style="display:none">
	    <legend><b>Link to be added</b></legend>

	    <div id="badInsertDiv" style="color:red"></div>

	    <form id="postage">
		<table cellspacing="2">
		<tr><td>Category:&nbsp;
		    <td><input name="cat" type="text"
					  size="40" maxlength="100" />
			</td></tr>
	    	<tr><td>URL:&nbsp;
		    <td><input name="url" type="text"
					  size="40" maxlength="200" />
			</td></tr>
	    	<tr><td>Caption:&nbsp;
		    <td><input name="cap" type="text"
					  size="40" maxlength="300" />
			</td></tr>
		<tr><td><input type="submit" value="Add"></td></tr>

	    	</table>
	    </form>
	</fieldset>


	<fieldset id="links" style="display:none">
	    <legend><b>Archived Links</b></legend>
	    <div id="linksDiv"></div>
	</fieldset>

	<fieldset id="help" style="display:none">
	    <legend><b>Help etc.</b></legend>
		<div id="helpDiv"></div>
	    </fieldset>


    </body>

</html>
