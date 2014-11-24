<?php
/*
A simple RESTful API router.
*/

include __DIR__.'/../../inc/all.php';

$in = extractVars();
$results = [];
$results["meta"]["request"] = $in;
$results["meta"]["ok"] = true;
$results["meta"]["verb"] = $_SERVER['REQUEST_METHOD'];
$results["meta"]["url"] = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));

sendResults($results);
