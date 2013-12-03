<?php
// Initialize script
require_once "load.php";

// Initialize Loggy. Visit http://github.com/seckin92/loggy
// for more information.
include "loggy/load.php";

$loggy = new Loggy();

$engine = new Engine();

$name = "John Lunn";
$salary = 4250;
$hired = "2012-12-07";

$engine->addEmployee($name, $salary, $hired);
$result = $engine->loadBoolean();

if($result) echo "Successfully added $name"; else echo "Failed adding $name";

if($engine->hasErrors())
    $loggy->w($engine->complain(), "Engine");

?>