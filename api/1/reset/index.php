<?php

include __DIR__.'/../../../inc/all.php';

$DB = new DB;
$q = "drop database " . DBNAME;

$results["rows"] = $DB->query($q, null, $debug);
$results["meta"]["ok"] = true;
$results["meta"]["feedback"] = "Database reset.";

sendResults($results);
