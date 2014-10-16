<?php
	$folder = $_SERVER["DOCUMENT_ROOT"] . "/inc";

    INCLUDE "$folder/config.php";
    INCLUDE "$folder/db.php";
    INCLUDE "$folder/io.php";

    if (LOGGING) {
		// For logging, if required
    	INCLUDE "$folder/log.php";
	}
?>
