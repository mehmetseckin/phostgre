<?php
/**
 * A little example script to demonstrate Loggy's work.
 * 
 * - Check out the logs by calling 
 *     yourscript/logs.php
 * 
 * - Look at different Loggy files by passing their name on
 *   the url : yourscript/logs.php?file=debug.loggy
 * 
 * - Switch exporting type
 *      yourscript/logs.php?type=JSON&file=foo.log.gy
 * 
 * - Truncate
 *      yourscript/logs.php?truncate
 *      yourscript/logs.php?file=debug.log.gy&truncate
 */

require_once 'loggy/load.php';
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
            break;
        default:
            $type = "HTML";
            break;
    }
} else {
    $type = "HTML";
}

if(isset($_GET["truncate"])) {
    print $loggy->truncate();
    exit;
}

if($type!="HTML") {
    print $loggy->export($type);   
    exit;
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $loggy->getFileName() . " | Powered by Loggy"; ?></title>
    </head>
    <body>
        <?php
            print $loggy->export($type);
        ?>
        <hr>
        <p>Click <a href="?file=<?php echo $loggy->getFileName(); ?>&truncate=yes">here</a> to truncate the log file.</p>
        <p style="cursor:pointer" onclick="javascript:window.history.back();">Click here to go back.</p>
    </body>
</html>
