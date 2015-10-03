<?php
/*
Initialises the DB
*/

include __DIR__.'/../../../inc/all.php';

$in = extractVars();
$results = [];

    $dsn = "mysql:" . DBHOST . ";dbname=".DBNAME.";";
    $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true
    );

    $DB = new PDO($dsn, DBUSER, DBPW, $option);

    $rows[] = $DB->query("CREATE DATABASE " . DBNAME);
    $rows[] = $DB->query("USE " . DBNAME);
    $rows[] = $DB->query(DBINIT);

    $results["rows"] = $rows;
    sendResults($results);
