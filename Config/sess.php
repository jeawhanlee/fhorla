<?php
// fhola

// altar array elements to project elements

// if header_script key is TRUE fhola calls the class of the header for the user role
// if header_script is instatiated, header class must be created.
$user_type = array(
                "default" => array(
                            // always set default "dir" to NULL unless you know what you're doing
                            "dir" => NULL,
                            "landing" => NULL,
                            "header" => "header",
                            "footer" => "footer",
                            "header_script" => FALSE
                            ),
                "1" => array(
                            "dir" => NULL,
                            "landing" => NULL,
                            "header" => NULL,
                            "footer" => NULL,
                            "header_script" => FALSE
                            ),
                "2" => array(
                            "dir" => NULL,
                            "landing" => NULL,
                            "header" => NULL,
                            "footer" => NULL,
                            "header_script" => FALSE
                            ),
                "3" => array(
                            "dir" => NULL,
                            "landing" => NULL,
                            "header" => NULL,
                            "footer" => NULL,
                            "header_script" => FALSE
                            ),
    
                "4" => array(
                            "dir" => NULL,
                            "landing" => NULL,
                            "header" => NULL,
                            "footer" => NULL,
                            "header_script" => FALSE
                            ),
                "5" => array(
                            "dir" => NULL,
                            "landing" => NULL,
                            "header" => NULL,
                            "footer" => NULL,
                            "header_script" => FALSE
                            ),
            );

$log_sess_name = "user_graph";

// change value if project requires user sessions !important
define("USER_SESSION", $log_sess_name);

// define user graphs to expect
// array can be populated
$user_graph = array(
                "role" => NULL,
                "name" => NULL,
                "email" => NULL,
                "user_id" => NULL
            );

define("USER_GRAPH", $user_graph);


if(!isset($_SESSION[USER_SESSION])){
    define("LOADER_OPT", $user_type["default"]);
}
else{
    //check user type and directory
    $sess_user_type = $_SESSION[USER_SESSION]['role'];
    $user_type_dir = $user_type[$sess_user_type]['dir'];
    
    //check url
    $url = explode("/", $_SERVER['REQUEST_URI']);
    if(!in_array($user_type_dir, $url)){
        define("LOADER_OPT", $user_type['default']);
        define("LOADER_OPT_LOGGED", $user_type[$sess_user_type]);
    }
    else{
        define("LOADER_OPT", $user_type[$sess_user_type]);
    }
}

// fhola