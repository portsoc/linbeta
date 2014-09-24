<?php	// API/INSERT.AJAX.PHP		// Takes cat/url/cap  from $_REQUEST,
	// ===================		// and inserts  it into the database.
	//				// If  url=caption  is null,  then no
	//    (C) C Lester 2013,2014	// entry is made.  Othwerwise, (1) if
	//				// the  category   is  null,   it  is
	// defaulted to "?";  (2) null url is OK - it's a linkless entry into
	// the database; and (3) null caption is defaulted to url.

	// Returns only error messages, which means they will later need
	// somewhere to appear in index.html.



    DEFINE ( "_debugging_",	FALSE );

    DEFINE ( "_logging_",	TRUE );
    DEFINE ( "_log_source_",	"INSERT LINK-ENTRY" );
    DEFINE ( "_log_filename_",	$_SERVER["DOCUMENT_ROOT"] . "/API/_API_.LOG" );

    INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/API/hidden/(LINORA).inc.php";	// Linora-specific defines and function
    INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/API/hidden/(DB_EASY).inc.php";// Reusable mysqli-database utilities
    if (_logging_) INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/API/hidden/LOGGING.inc.php";
				// For logging, if required



    if ( count($_POST)==0 ||				   // = a null call
	 ($url=$_POST['url']).($cap=$_POST['cap']) == "" ) // = a void entry
      { log_("No URL or caption");
	echo "No URL or caption - the entry has been ignored.<br/>"; }
    else
      { $cat = $_POST['cat'];
	log_( "**** INSERTING $cat / $url / $cap ****");
	// Now we can sensibly connect to the MySQL engine.
	$DB = new DB_easy;
	// Get any duplcates of the proposed entry
	$binds = array($cat,$url,$cap); // Binding-array for PQuery calls
	$query = "SELECT * FROM entries WHERE cat=? AND url=? AND cap=?";
	$_duplics_ = $DB->PQuery($query,$binds,__file__,__line__);
	// If there are duplicates, don't insert the new entry
	if ($_duplics_->rowCount() != 0) // [Was ->num_rows in mysqli]
	  { log_("DUPLICATE"); }
	else
	  { $query = "INSERT INTO entries VALUES (?,?,?)";
	    $DB->PQuery($query,$binds,__file__,__line__);
	    log_("INSERTed"); }
	// Now wrap up cleanly
	$DB->Close();
      }

    log_close_("");

?>
