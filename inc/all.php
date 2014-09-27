<?php
	$folder = $_SERVER["DOCUMENT_ROOT"] . "/inc";

    INCLUDE "$folder/config.php";
    INCLUDE "$folder/linora.php";
    INCLUDE "$folder/db.php";

    if (LOGGING) {
		// For logging, if required
    	INCLUDE "$folder/log.php";
	}
?>
