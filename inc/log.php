<?php
/*
Include for responders that do any logging.
The constants LOGGING & LOGFILE should already have been DEFINEd.
This include is best used as
if (LOGGING) INCLUDE...
*/

function logWriter($obj)
{
    global $log;
    if (LOGGING) {
        fwrite($log, date("Y-m-d H:i:s  ").json_encode($obj)."\r\n");
    }
}

function log($s, $type = "info")
{
    global $log;
    if (LOGGING) {
        $obj = debug_backtrace(false);
        $obj['type'] = $type;
        logWriter( $obj );
    }
}

function logError($s) {
    log($s, "error");
}


function logWarn($s) {
    log($s, "warning");
}


function logClose($s) {
    global $log;

    if (LOGGING) {
        return log($s, "close");
        if ($log) {
            fclose($log);
        }
    }
}


if (LOGGING) {
    $log = fopen(LOGFILE, "a");
}