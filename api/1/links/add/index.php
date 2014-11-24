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
} else {
    $results = [];
    $results["meta"]["ok"] = false;
    $results["meta"]["status"] = 400;
    $results["meta"]["msg"] = "Bad Request";
    $results["meta"]["feedback"] = "That request doesn't appear to have all the necessary fields for it to work.";
    $results["meta"]["developer"] = "See the required and received fields in json metadata to evaluate what was omitted.";
    $results["meta"]["received"] = $in;
    $results["meta"]["required"] = $precondition;

}

sendResults($results);

