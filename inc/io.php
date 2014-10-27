<?php
/**
 * @module /inc/io
 * A library for general operations 
 *
 * @copyright University of Portsmouth 2014
 * @author Rich Boakes
 */

$debug = array();

function debug($value)
{
    global $debug;
    $debug[] = $value;
}

function sanitize($what, $how, $method = INPUT_GET)
{
    $filtered = filter_input($method,$what, $how, FILTER_FLAG_STRIP_LOW || FILTER_FLAG_STRIP_HIGH);
    return $filtered;
}

/*
 Extract all variables necessary for processing the request.
 */
function extractVars($method = INPUT_GET)
{
    $out = array();
    foreach ($_REQUEST as $key => $value) {
        $out[$key] = stripslashes(strip_tags(urldecode(filter_input($method,$key, FILTER_SANITIZE_STRING))));
    }
    
    return $out; // $_REQUEST;
}


function sendResults($rows = array(), $meta = array(true))
{
    global $debug, $_REQUEST;

     // Check for presence of "application/json" in the accept header
    $json = !(stripos($_SERVER['HTTP_ACCEPT'], 'application/json') === false);
    if ($json) {
        header("Content-Type: application/json");
        $rows = json_encode($rows);
        $meta = json_encode($meta);
        $debug = json_encode($debug);
        $req = json_encode($_REQUEST);
        echo '{"meta": '.$meta.', "req": '.$req.', "data": '.$rows.', "debug": '.$debug.'}';
    } else {
        header("Content-Type: text/plain");
        print_r($rows);
    }
}


function checkVarsPresent($in, $keys)
{
    foreach ($keys as $k) {
        if (!isset($in[$k])) {
            return false;
        }
    }
    return true;
}

/**
 * Extract all variables necessary for processing the request.
 */
function insertRecord($in)
{
	$meta = array();

    // open the DB
    $DB = new DB;
    $binds = null;

	// is this an add or an update?
	if ($in["xid"] && strlen(trim($in["xid"])) > 0) {
        $meta["action"] = "update";
        $binds = array($in["url"],$in["cap"],$in["cat"],$in["xid"]);
        $query = "UPDATE entries SET url=?, cap=?, cat=? WHERE id=?";
	} else {
        $meta["action"] = "insert";
        $binds = array($in["url"],$in["cap"],$in["cat"]);
		$query = "INSERT INTO entries (url, cap, cat) VALUES (?,?,?);";
	}

    // add (or update) the record to the database
    $rows = $DB->query($query, $binds);

	// check if the update really worked and feedback to $meta properly
	$meta["ok"] = ($rows > 0);

	// read the record back from the database
	$rows = array();

	if ($meta["action"] == "insert") {
		$id = $DB->lastInsertId();
	} else {
		$id = trim($in["xid"]);
	}

	$query = "SELECT * FROM entries WHERE id=${id};";
    $rows = $DB->query($query);

    $DB->close();

	return array( "rows"=>$rows, "meta"=>$meta );
}