<?php

require_once "init.php";

$loggy = new Loggy();

$engine = new Engine();

$engine->setQuery("select * from employees;");

$results = $engine->loadMultiple();

$loggy->w($engine->complain(), "Engine");

?>
