<?php

require_once "src/init.php";

$loggy = new Loggy("debug.log.gy");
$loggy->w("Dummy debug message with \"QUOTES\"!!", "Tag!");

$engine = new Engine();
$engine->setQuery("select * from employees;");
$results = $engine->loadMultiple();
$engine->complain();

var_dump($results);
?>
