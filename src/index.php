<?php

require_once "init.php";

$loggy = new Loggy();

$engine = new Engine();

$engine->setQuery("
        SELECT  proname, proargnames
        FROM    pg_catalog.pg_namespace n
        JOIN    pg_catalog.pg_proc p
        ON      pronamespace = n.oid
        WHERE   nspname = 'public' AND proowner <> 1;
        ");

$results = $engine->loadMultiple();

if($engine->hasErrors())
    $loggy->w($engine->complain(), "Engine");
?>
