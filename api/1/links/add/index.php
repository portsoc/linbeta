<?php
/*
Takes id/cat/url/cap from $_REQUEST, and inserts it into the database.
*/

include __DIR__.'/../../../../inc/all.php';

// establish whether the required variables are all present
$precondition = array("url", "cap", "cat");
$in = extractVars(INPUT_POST);
$required_vars_are_present = checkVarsPresent($in, $precondition);

if ($required_vars_are_present) {
	$results = insertRecord($in);
	sendResults(
		$results['rows'],
		$results['meta']
	); // TODO internationalise?
} else {
	header("HTTP/1.0 400 Bad Request");
    header("x-failure-detail: Not all expected fields were present.");
    echo '{
    	"error_message": "not all required fields were provided.",
    	"error_required_fields": '.json_encode($precondition).',
    	"error_available_fields": '.json_encode($in).',
    	"ok": false
    }';
}