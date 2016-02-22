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
//    exit(-1);
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

    if (isset($results["meta"]["ok"]) && $results["meta"]["ok"] !== false) {
        $status = isset($results["meta"]["status"]) ? $results["meta"]["status"] : 200;
        $msg = isset($results["meta"]["msg"]) ? $results["meta"]["msg"] : "OK";
    } else {
        $status = isset($results["meta"]["status"]) ? $results["meta"]["status"] : 599;
        $msg = isset($results["meta"]["msg"]) ? $results["meta"]["msg"] : "Oh 'eck!";
    }
    header("HTTP/1.1 $status $msg");

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

function init_db($in) {
    $DB = new DB;
    $rows[] = $DB->query("CREATE DATABASE " . DBNAME);
    $rows[] = $DB->query("USE " . DBNAME);
    $rows[] = $DB->query(DBINIT);
    $results["rows"] = $rows;
    $results["meta"]["ok"] = true;
    $results["meta"]["feedback"] = "Database created.";
    return $results;
}

function reset_db($in) {
    $DB = new DB;
    $q = "drop database " . DBNAME;
    $results["rows"] = $DB->query($q, null, $debug);
    $results["meta"]["ok"] = true;
    $results["meta"]["feedback"] = "Database reset.";
    return $results;
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
    if (count($rows) > 0) {
        $meta["ok"] = true;
        if ($meta["action"] == "insert") {
            $meta["status"] = 201;
            $meta["msg"] = "Created";
        }
    }

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



/**
 * Extract all variables necessary for processing the request.
 */
function deleteRecord($in)
{
    $meta = array();

    // open the DB
    $DB = new DB;
    $binds = null;

    $meta["action"] = "delete";
    $binds = array($in["id"]);
    $query = "DELETE from entries WHERE id=?";

    // add (or update) the record to the database
    $rows = $DB->query($query, $binds);

    // check if the update really worked and feedback to $meta properly
    $meta["ok"] = (count($rows) > 0);

    $DB->close();

    return array( "rows"=>$rows, "meta"=>$meta );
}


/**
 * Extract all variables necessary for processing the request.
 */
function updateRecord($in)
{

    $precondition = array("url", "cap", "cat", "xid");
    $required_vars_are_present = checkVarsPresent($in, $precondition);

    if ($required_vars_are_present) {
        // open the DB
        $DB = new DB;
        $binds = null;
        $meta["action"] = "update";
        $parent = isset($in["parent"]) ? $in["parent"] : null;

        $binds = array($in["url"], $in["cap"], $in["cat"], $parent, $in["xid"]);
        $query = "UPDATE entries SET url=?, cap=?, cat=?, parent=? WHERE id=?";

        // update the record in the database
        $rows = $DB->query($query, $binds);

        // check if the update really worked and feedback to $meta properly
        $meta["ok"] = (count($rows) > 0);

        $id = trim($in["xid"]);

        $query = "SELECT * FROM entries WHERE id=${id};";
        $rows = $DB->query($query);

        $DB->close();

        return array("rows" => $rows, "meta" => $meta);
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
}


function add_link($in) {

    // establish whether the required variables are all present
    $precondition = array("url", "cap", "cat");
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

    return $results;
}

function notImplementedYet ($in){
    $results = [];
    $results["meta"]["ok"] = false;
    $results["meta"]["status"] = 501;
    $results["meta"]["msg"] = "Not implemented";
    $results["meta"]["feedback"] = "This capability has not been written yet - sorry!";
    $results["meta"]["received"] = $in;
    return $results;
}

function methodNotAllowed ($in)
{
    $results = [];
    $results["meta"]["ok"] = false;
    $results["meta"]["status"] = 405;
    $results["meta"]["msg"] = "Method not allowed";
    $results["meta"]["feedback"] = "This method is not supported on the url.";
    $results["meta"]["received"] = $in;
    return $results;
}

function get_links($in) {
    $results = [];

    try {
        $DB = new DB();

        $offset = 0;
        $limit = 25;

        $clause = "";


        if (isset($in["id"])) {
            $clause = "where id = ${in['id']}";
        } else {
            if (isset($in["filter"])) {
                $f = $in["filter"];
                $clause = "where (cat like '%${f}%' or cap like '%${f}%')";
            }

            if (isset($in["offset"])) {
                $offset = abs(intval($in["offset"]));
            }

            if (isset($in["limit"])) {
                $limit = min(abs(intval($in["limit"])), 100);
            }
        }

        $href = ", concat('/api/2/links/', id) as href";


        $q = "SELECT * $href from entries $clause order by id desc limit $limit offset $offset;";

        $rows = $DB->query($q);

        $results["rows"] = $rows;
        $results["meta"]["ok"] = true;
        $results["meta"]["query"] = $q;
        $results["meta"]["offset"] = $offset;
        $results["meta"]["limit"] = $limit;
        $results["meta"]["count"] = count($rows);
    } catch (DBException $dbx) {
        error_log ($dbx);
        $results["meta"]["ok"] = false;
        $results["meta"]["exception"] = $dbx;
        $debug[0] = $dbx;
    }
    return $results;
}
