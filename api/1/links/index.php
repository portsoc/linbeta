<?php
/*
Returns the current contents of the Linora database as a
JSON object, representing the arry of results, sorted on
first the category, then on the caption.
*/
include $_SERVER["DOCUMENT_ROOT"] . "/inc/all.php";

$in = extract_vars();

try {
    $DB = new DB();

    if (isset($in["filter"])) {
        $f = $in["filter"];
        $q = "SELECT * from entries where (cat like '%${f}%' or cap like '%${f}%') order by id desc;";
        $rows = $DB->query($q);
    } else {
        $rows = $DB->query("SELECT * FROM entries ORDER BY cat,cap ASC");
    }
} catch (DBException $dbx) {
    echo $dbx;
    exit;
}



send_results($rows);
?>
