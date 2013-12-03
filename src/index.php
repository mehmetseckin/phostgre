<?php

require_once "load.php";

$loggy = new Loggy();

$engine = new Engine();

$name = "Mehmet Seckin";
$salary = 5622;
$hired = "2013-12-20";

$engine->addEmployee($name, $salary, $hired);
$result = $engine->loadBoolean();

if($result) echo "Successfully added $name"; else echo "Failed adding $name";

if($engine->hasErrors())
    $loggy->w($engine->complain(), "Engine");

?>