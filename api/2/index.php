<?php
/*
A simple RESTful API router.
*/

include __DIR__.'/../../inc/all.php';

$verb = $_SERVER['REQUEST_METHOD'];
$path = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));

// extract and sanitise get/post payload
switch ($verb) {
    case "POST":
    case "PUT":
        $in = extractVars(INPUT_POST);
        break;
    default:
        $in = extractVars();
}

if (isset($path[1]) && trim($path[1]) != "") {
    $in["id"] = trim(sanitize($path[1]));
}

$results = [];
$results["meta"]["ok"] = true;
$results["meta"]["verb"] = $verb;


// todo use call_user_func with a whitelist to make this much shorter

switch ($path[0]) {
    case "test":
        break;
    case "init":
        if ($verb == "GET") {
            // TODO GET isn't the right verb, but it means we can kick
            // it off from a browser query, for now...
            $results = init_db ( $in );
        }
        break;
    case "reset":
        if ($verb == "GET") {
            // TODO GET isn't the right verb, but it means we can kick
            // it off from a browser query, for now...
            $results = reset_db ( $in );
        }
        break;
    case "proxy" :
        if ($verb == "GET") {
            $url = $in["url"];
            try {
            	$tags = get_meta_tags($url);
            } catch (Exception $failure) {
            	fail( $failure );
            }

            // title isn't covered by get_meta_tags to pull it from the page content
            $str = file_get_contents($url);
            preg_match("/<title>(.*)<\/title>/", $str, $matches);

            // if there's a title, use it, if not, the domain will do
            $tags["title"] = array_key_exists(1, $matches) ? $matches[1] :  parse_url($url)["host"];

            $results["tags"] = $tags;
        }
        break;
    case "links":

        switch ($verb) {

            case "GET":
                $results = get_links ( $in );
                break;
            case "POST":
                $results = insertRecord( $in );
                break;
            case "PATCH":
            case "PUT":
                if (isset($in["id"])) {
                    $results = notImplementedYet( $in );
                } else {
                    $results = methodNotAllowed($in);
                }
                break;
            case "DELETE":
                if (isset($in["id"])) {
                    $results = deleteRecord( $in );
                } else {
                    $results = methodNotAllowed($in);
                }
                break;
            default:
                $results["meta"]["feedback"] = "For ".$path[0]." GET, POST, PUT and DELETE are supported";
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
