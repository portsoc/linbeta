
<?php
	/*
	Include for responders that do any logging.
	The constants LOGGING & LOGFILE should already have been DEFINEd.
	This include is best used as
	if (LOGGING) INCLUDE...
	*/

	function log_writer($obj) {
	    global $log;
	    if (LOGGING) {
	        fwrite(
	        	$log, 
	        	date("Y-m-d H:i:s  ") . json_encode( $obj ). "\r\n"
	        );
	    }
	}

	function log_($s, $type = "info") {
	    global $log;
	    if (LOGGING) {
		    $obj = debug_backtrace(false);
			$obj['type'] = $type;
			log_writer( $obj );
	    }
	}

	function log_error($s) {
	    log_($s, "error");
	}


	function log_warn($s) {
	    log_($s, "warning");
	}


	function log_close_($s) {
	    global $log;

	    if (LOGGING) {
	        return log_($s, "close");
		    if ($log) {
			    fclose($log);
		    }
		}
	}


	if (LOGGING) {
	    $log = fopen(LOGFILE, "a");
	}

?>