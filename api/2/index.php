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

switch ($results["meta"]["url"][0]) {
    case "test":
        break;
    case "links":

        if ($results["meta"]["verb"] == "GET") {
            $results = get_links ( $in );
        }

        /*
         * list
         * add
         * update
         * delete
         */
        break;
    default:
        $results["meta"]["ok"] = false;
}

sendResults($results);


