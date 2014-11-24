<?php
/*
Returns the current contents of the Linora database as a
JSON object, representing the arry of results, sorted on
first the category, then on the caption.
*/

include __DIR__.'/../../../inc/all.php';

$in = extractVars();
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

sendResults($results);
