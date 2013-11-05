<?php

require_once 'src/loggy.php';
if (isset($_GET["file"]))
    $loggy = new Loggy($_GET["file"]);
else
    $loggy = new Loggy();
if (isset($_GET["type"])) {
    $type = $_GET["type"];
    switch ($type) {
        case "JSON":
            header('Content-type: application/json');
            break;
        case "XML":
            header('Content-type: text/xml');
        default:
            header('Content-type: text/html');
            break;
    }
} else {
    $type = "";
}
if(isset($_GET["truncate"])) {
    $loggy->truncate();
    exit;
}
print $loggy->export($type);
exit;
?>
