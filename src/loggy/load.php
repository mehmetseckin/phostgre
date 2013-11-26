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
include dirname(__FILE__)."/lib/loggy.php";

// Initialize the error and exception handlers.
$old_error_handler = set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $loggy = new Loggy('loggy/logs/error.log.gy');
    $loggy->w($errstr, "$errfile:$errline");
    
});

$old_exception_handler = set_exception_handler(function($e) {
    $loggy = new Loggy('loggy/logs/error.log.gy');
    $loggy->w($e->getMessage(), $e->getFile() .":". $e->getLine());
});

?>
