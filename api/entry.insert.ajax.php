<?php
	/*
    Takes cat/url/cap  from $_REQUEST, and inserts  it into the database.
    If url=caption  is null,  then no entry is made.  Othwerwise, (1) if the  category   is  null,   it  is
	defaulted to "?";  (2) null url is OK - it's a linkless entry into
	the database; and (3) null caption is defaulted to url.
	Returns only error messages, which means they will later need
	somewhere to appear in index.html.
	 */

    INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/inc/all.php";

    if (
    	count($_POST)==0 ||   // = a null call
    	($url=$_POST['url']).($cap=$_POST['cap']) == ""     	 // = a void entry
	) {
        log_("No URL or caption");
        echo "No URL or caption - the entry has been ignored.<br/>";
    } else {
        $cat = $_POST['cat'];
        log_( "**** INSERTING $cat / $url / $cap ****");
        // Now we can sensibly connect to the MySQL engine.
        $DB = new DB_easy;
        // Get any duplcates of the proposed entry
        $binds = array($cat,$url,$cap);
        // Binding-array for PQuery calls
        $query = "SELECT * FROM entries WHERE cat=? AND url=? AND cap=?";
        $_duplics_ = $DB->PQuery($query,$binds,__file__,__line__);
        // If there are duplicates, don't insert the new entry

        if ($_duplics_->rowCount() != 0) {
            log_("DUPLICATE");
        } else  {
            $query = "INSERT INTO entries VALUES (?,?,?)";
            $DB->PQuery($query,$binds,__file__,__line__);
            log_("INSERTed");
        }

        // Now wrap up cleanly
        $DB->Close();
    }


    if (LOGGING) {
        log_close_("");
    }

?>
