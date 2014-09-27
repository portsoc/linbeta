<?php
    INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/inc/config.php";

	// Linora-specific defines and function
    INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/API/hidden/(LINORA).inc.php";	

	// Reusable mysqli-database utilities
    INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/API/hidden/(DB_EASY).inc.php";

	// For logging, if required
    if (LOGGING) {
    	INCLUDE $_SERVER["DOCUMENT_ROOT"] . "/API/hidden/LOGGING.inc.php";
	}
?>