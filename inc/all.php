<?php
	$folder = $_SERVER["DOCUMENT_ROOT"] . "/inc";

    INCLUDE "$folder/io.php";

    // io include needs to have happened for debug fn to exist.
    if(!file_exists("$folder/config.php")) {
        debug("<b>Hold on Sparky!</b> You need to copy the
            contents of the <code>config_sample.php</code>
            file into a new <code>config.php</code> file and fill in the database
            details, or Linora can't store and retrieve your links.");
        send_results();
        exit(-1);
    }

    INCLUDE "$folder/config.php";
    INCLUDE "$folder/db.php";

    if (LOGGING) {
		// For logging, if required
    	INCLUDE "$folder/log.php";
	}
?>
