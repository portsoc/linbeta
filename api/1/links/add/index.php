<?php
/*
Takes id/cat/url/cap from $_REQUEST, and inserts it into the database.
*/

include __DIR__.'/../../../../inc/all.php';
$in = extractVars(INPUT_POST);
$results = add_link($in);
sendResults($results);
