<?php
// Check lib directory.
if(!is_dir(dirname(__FILE__)."/lib"))
    die("Directory \"lib\" does not exist!");

// Check logs directory
if(!is_dir(dirname(__FILE__)."/logs"))
    die("Directory \"logs\" does not exist!");

// Check file permissions on logs directory
if(!is_writable(dirname(__FILE__)."/logs"))
    die("Directory \"logs\" is not writable!");

// Include the class definition.
include dirname(__FILE__)."/lib/loggy.php"
?>
