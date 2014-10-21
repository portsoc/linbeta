<?php
/**
 * @module /inc/io
 * A library for general operations 
 *
 * @copyright University of Portsmouth 2014
 * @author Rich Boakes
 */

$debug = array();

function debug($value) {
	global $debug;
	$debug[] = $value;
}

function sanitize($what, $how, $method = INPUT_GET) {
  $filtered = filter_input($method,$what, $how, FILTER_FLAG_STRIP_LOW || FILTER_FLAG_STRIP_HIGH);
  return $filtered;
}

/*
 Extract all variables necessary for processing the request.
 */
function extract_vars($method = INPUT_GET) {

	$out = array();
	foreach ($_REQUEST as $key => $value) {
    	$out[$key] = stripslashes(strip_tags(urldecode(filter_input($method,$key, FILTER_SANITIZE_STRING))));
    }
	
	return $out; // $_REQUEST;
}


function send_results($rows = array(), $meta = array(true)) {
	global $debug, $_REQUEST;

 	// Check for presence of "application/json" in the accept header
	$json = !(stripos($_SERVER['HTTP_ACCEPT'], 'application/json') === FALSE);
	if ($json) {
		header("Content-Type: application/json");
		$rows = json_encode($rows);
		$meta = json_encode($meta);
		$debug = json_encode($debug);
		$req = json_encode($_REQUEST);
		echo '{"meta": '.$meta.', "req": '.$req.', "data": '.$rows.', "debug": '.$debug.'}';
	} else {
		header("Content-Type: text/plain");
		print_r($rows);
	}
}


function check_vars_present($in,$keys) {
	foreach( $keys as $k) {
		if ( !isset($in[$k]) ) {
			return false;
		}
	}
	return true;
}
?>