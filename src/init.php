<?php
// For debugging purposes ...
error_reporting(E_ALL);

// Database settings ...
define("PgSQL_HOSTNAME", "localhost");
define("PgSQL_USERNAME", "phostgre");
define("PgSQL_PASSWORD", "phostgre");
define("PgSQL_DATABASE", "phostgre_test");
define("PgSQL_PORT"    , "5432");
// Class definitions ...

// Initialize Loggy. Visit http://github.com/seckin92/loggy
// for more information.
include "loggy/load.php";

$old_error_handler = set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $loggy = new Loggy('loggy/logs/error.log.gy');
    $loggy->w($errstr, "$errfile:$errline");
    
});

$old_exception_handler = set_exception_handler(function($e) {
    $loggy = new Loggy('loggy/logs/error.log.gy');
    $loggy->w($e->getMessage(), $e->getFile() .":". $e->getLine());
});
// Initialize the Engine.
include "lib/engine.php";
?>
