<?php

$url = urldecode ( trim($_REQUEST['url']) );
$tags = get_meta_tags($url);

// title isn't covered by get_meta_tags to pull it from the page content
$str = file_get_contents($url);
preg_match("/<title>(.*)<\/title>/", $str, $matches);
$tags["title"] = $matches[1];

echo json_encode($tags);
?>
