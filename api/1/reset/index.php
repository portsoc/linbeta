<?php

include __DIR__.'/../../../inc/all.php';

$DB = new DB;
$q = "drop database " . DBNAME;
echo "<p>$q</p>";

$rows = $DB->query_to_array($q);

send_results($rows);

echo "<p>ok DB wiped.</p>";
?>
