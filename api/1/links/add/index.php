<?php
/*
Takes id/cat/url/cap from $_REQUEST, and inserts it into the database.
*/

include $_SERVER["DOCUMENT_ROOT"] . "/linbeta/inc/all.php";

/**
 * Extract all variables necessary for processing the request.
 */
function insert_record($in) {
	debug("inserting", $in);

	$meta = array( "ok" => true );

    // open the DB
    $DB = new DB;
    $binds = null;

	// is this an add or an update?
	if ($in["xid"] && trim($in["xid"]).length > 0) {
        $meta["msg"] = "Update " . $in["xid"].".";
        $binds = array($in["url"],$in["cap"],$in["cat"],$in["xid"]);
        $query = "UPDATE entries SET url=?, cap=?, cat=? WHERE id=?";
	} else {
        $meta["msg"] = "Insert.";
        $binds = array($in["url"],$in["cap"],$in["cat"]);
		$query = "INSERT INTO entries (url, cap, cat) VALUES (?,?,?);";
	}

    // add (or update) the record to the database
    $rows = $DB->query($query, $binds);

	// TODO check if the update really worked and feedback to $meta properly

	// read the record back from the database
	$rows = array();
	$query = "SELECT * FROM entries WHERE id=" .$DB->lastInsertId().";";
    $rows = $DB->query_to_array($query);

    $DB->close();

	return array( "rows"=>$rows, "meta"=>$meta );
}

// establish whether the required variables are all present
$precondition = array("url", "cap", "cat");
$in = extract_vars(INPUT_POST);
$required_vars_are_present = check_vars_present($in, $precondition);

if ($required_vars_are_present) {
	$results = insert_record($in);
	send_results(
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
    	"debug": '.json_encode($debug).',
    	"ok": false
    }';
}


