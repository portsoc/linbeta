<?php
if (file_exists($_SERVER["DOCUMENT_ROOT"] . $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    header("x-router: router was used, not htaccess");
    require "index.php";
}
?>
