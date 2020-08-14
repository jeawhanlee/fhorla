<?php

// load config files
autoLoad("Config", "user_graph.php");

// load lib
autoLoad("System/core/lib", "fns.php");

// db helper
load("System/engine/helpers/db_helper");

// load loaders
load("System/engine/helpers/url_helper");

// load default engine controller
autoLoad("System/engine/controller");

require_once 'System/Engine/loaders/loader.php';

try{
    // load controllers classes
    class_loader("App/Controller");
}
catch(Exception $e){
    htmlOut("warning", $e->getMessage(), "text-center");
}
