<?php
/**
 * @module /inc/io
 * A library for general operations 
 *
 * @copyright University of Portsmouth 2014
 * @author Rich Boakes
 */

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

function fail($msg) {
	$meta["ok"] = false;
	$debug[] = $msg;
	sendResults(array(), $meta, $debug);
    exit(-1);
}

/**
 * @param $results
 */
function sendResults($results)
{
    global $_REQUEST;

    $jsonFormatParameter = isset($_REQUEST['format']) && ($_REQUEST['format'] == 'json');
    $jsonHeaderRequest = !(stripos($_SERVER['HTTP_ACCEPT'], 'application/json') === false);
    $json = $jsonHeaderRequest || $jsonFormatParameter;

    if (isset($results["meta"]["ok"]) && $results["meta"]["ok"] === false) {
        $status = isset($results["meta"]["status"]) ? $results["meta"]["status"] : 599;
        $msg = isset($results["meta"]["msg"]) ? $results["meta"]["msg"] : "Oh 'eck!";
		header("HTTP/1.1 $status $msg");
    }

    if ($json) {
        header("Content-Type: application/json");
        echo json_encode($results);
    } else {
        header("Content-Type: text/plain");
        echo("results: ");
        var_dump($results);
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
	if (isset($in["xid"]) && strlen(trim($in["xid"])) > 0) {
        $meta["action"] = "update";
        $binds = array($in["url"],$in["cap"],$in["cat"],$in["parent"],$in["xid"]);
        $query = "UPDATE entries SET url=?, cap=?, cat=?, parent=? WHERE id=?";
	} else {
        $meta["action"] = "insert";
        if (!isset($in["parent"])) {
            $in["parent"] = 0;
        }
        $binds = array($in["url"],$in["cap"],$in["cat"],$in["parent"]);
		$query = "INSERT INTO entries (url, cap, cat, parent) VALUES (?,?,?,?);";
	}


    // add (or update) the record to the database
    $rows = $DB->query($query, $binds);

	// check if the update really worked and feedback to $meta properly
	$meta["ok"] = (count($rows) > 0);

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


function get_links($in) {
    $results = [];

    try {
        $DB = new DB();

        if (isset($in["filter"])) {
            $f = $in["filter"];
            $q = "SELECT * from entries where (cat like '%${f}%' or cap like '%${f}%') order by id desc;";
        } else {
            $q = "SELECT * from entries order by id desc;";
        }

        $rows = $DB->query($q);

        $results["rows"] = $rows;
        $results["meta"]["ok"] = true;
        $results["meta"]["query"] = $q;
        $results["meta"]["count"] = count($rows);
    } catch (DBException $dbx) {
        error_log ($dbx);
        $results["meta"]["ok"] = false;
        $results["meta"]["exception"] = $dbx;
        $debug[0] = $dbx;
    }
    return $results;
}