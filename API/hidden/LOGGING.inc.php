<?php
	// LOGGING.INC.PHP	Include for responders that do any logging.
	// ===============	The constants _logging_ _LOG_SOURCE_ 
	//			_log_filename_ should already have been
	//  (C) C Lester 2013	DEFINEd. This include is best done as
	//			    if (_logging_) INCLUDE...


    function log_($s)
      { global $LOG;
	if (_logging_)
	    fwrite($LOG,date("Y-m-d H:i:s  ")._log_source_."  $s\r\n"); }

    function log_error($s) { log_("ERROR: $s"); }
    function log_warn($s)  { log_("WARNING: $s"); }

    function log_close_($s)
      { global $LOG;
	if (!_logging_) return
	log_("******** $s");
        fclose($LOG); }


    if (_logging_) $LOG = fopen(_log_filename_,"a");
    
?>
