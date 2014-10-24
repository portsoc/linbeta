<?php
/*
Returns the current contents of the Linora database as a
JSON object, representing the arry of results, sorted on
first the category, then on the caption.
*/

include __DIR__.'/../../../inc/all.php';

$in = extractVars();

try {
    $DB = new DB();

    if (isset($in["filter"])) {
        $f = $in["filter"];
        $q = "SELECT * from entries where (cat like '%${f}%' or cap like '%${f}%') order by id desc;";
        $rows = $DB->query($q);
    } else {
        $rows = $DB->query("SELECT * from entries order by id desc");
    }
} catch (DBException $dbx) {
    echo $dbx;
    error_log ($dbx);
    exit;
}

sendResults($rows);
