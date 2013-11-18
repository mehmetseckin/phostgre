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

// Initialize the Engine.
include "lib/engine.php";
?>
