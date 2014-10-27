<?php

$url = urldecode ( trim($_REQUEST['url']) );
try {
	$tags = get_meta_tags($url);
} catch (Exception $failure) {
	/// TODO throw a 500, *maybe*... 
}

// title isn't covered by get_meta_tags to pull it from the page content
$str = file_get_contents($url);
preg_match("/<title>(.*)<\/title>/", $str, $matches);

// if there's a title, use it, if not, the domain will do
$tags["title"] = array_key_exists(1, $matches) ? $matches[1] :  parse_url($url)["host"];

echo json_encode($tags);
?>
