<?php
/*
Returns the current contents of the Linora database as a
JSON object, representing the arry of results, sorted on
first the category, then on the caption.
*/

include __DIR__.'/../../../inc/all.php';

$in = extractVars();

$results = get_links ( $in );

sendResults($results);
