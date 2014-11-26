<?php
/*
A simple RESTful API router.
*/

include __DIR__.'/../../inc/all.php';
$results["meta"]["verb"] = $_SERVER['REQUEST_METHOD'];

// extract and sanitise get/post payload
switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
    case "PUT":
        $in = extractVars(INPUT_POST);
        break;
    default:
        $in = extractVars();
}

$results = [];
$results["meta"]["ok"] = true;
$verb = $_SERVER['REQUEST_METHOD'];
$path = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));

// todo use call_user_func with a whitelist to make this much shorter


switch ($path[0]) {
    case "test":
        break;
    case "links":

        switch ($verb) {

            case "GET":
                $in = extractVars();
                if (isset($path[1]) && trim($path[1]) != "") {
                    $in["id"] = $path[1];
                }
                $results = get_links ( $in );
                break;
            case "POST":
                $in = extractVars(INPUT_POST);
                if (isset($path[1]) && trim($path[1]) != "") {
                    $in["id"] = $path[1];
                    $results = updateRecord( $in );
                } else {
                    $results = insertRecord( $in );
                }
                break;
            case "PATCH":
                $in = extractVars(INPUT_POST);
                if (isset($path[1]) && trim($path[1]) != "") {
                    $in["id"] = $path[1];
                    $results = notImplementedYet( $in );
                } else {
                    $results = methodNotAllowed($in);
                }
                break;
            case "PUT":
                $in = extractVars(INPUT_POST);
                if (isset($path[1]) && trim($path[1]) != "") {
                    $in["id"] = $path[1];
                    $results = notImplementedYet( $in );
                } else {
                    $results = methodNotAllowed($in);
                }
                break;
            case "DELETE":
                $in = extractVars(INPUT_POST);
                if (isset($path[1]) && trim($path[1]) != "") {
                    $in["id"] = $path[1];
                    $results = deleteRecord( $in );
                } else {
                    $results = methodNotAllowed($in);
                }


                break;
            default:
                $results["meta"]["feedback"] = "Unrecognised verb for /links GET, POST, PUT and DELETE are supported";
                $results["meta"]["status"] = 400;
                $results["meta"]["ok"] = false;
        }

        break;
    default:
        $results["meta"]["ok"] = false;
}

$results["meta"]["request"] = $in;
$results["meta"]["verb"] = $verb;
$results["meta"]["path"] = $path;

sendResults($results);


