<?php

function class_loader($path){
    global $data;
    
    // get dir files
    $filedir = scandir($path);
    
    // load methods from URI upon REQUESTS   
    $url = inst('url_helper',"helper");
    
    $index = empty(INDEX_PAGE) ? 'welcome' : INDEX_PAGE;

    // define class name
    if(LOADER_OPT['dir'] != NULL){
        // check for session
        if(isset($url->trimmed_uri[$url->basename_key+2])){
            $page = $url->trimmed_uri[$url->basename_key+2];
        }
        else{
            $page = LOADER_OPT['landing'];   
        }
    }
    else{
        if(isset($url->trimmed_uri[$url->basename_key+1])){
            $page = $url->trimmed_uri[$url->basename_key+1];   
        }
        else{
            $page = $index;
        }
    }
    
    // check if there is a get request
    if(strpos($page,'?') !== FALSE){
        $page = explode('?',$page)[0];
    }
    
    // check in routes
    if(!array_key_exists($page,ASG)){
        if(DEBUG == TRUE){
            throw new Exception('page is not configured for routing or there is a restricted access for the requested page');   
        }
        else{
            $data['error404'] = TRUE;
        }
    }
    else{
        // get page controller
        if(strpos(ASG[$page],'/') !== FALSE){
            $ctrl_ent = explode('/',ASG[$page]);
            $class_name = $ctrl_ent[0];
        }
        else{
            $class_name = ASG[$page];
        }
    
        // load header script
        if(!isset($error404)){
            // load header script
            if(LOADER_OPT['header_script']){
                $header = LOADER_OPT['header'];
                // check if file exists
                if(!file_exists("$path/$header.php")){
                    throw new Exception("header script class not found");
                }

                include_once "App/Controller/".$header.".php";  

                $header_script = new $header;
                $header_script->index();
            }   
        }

        // check if class exists
        if(file_exists("$path/$class_name.php")){
            // load requested class
            load("$path/$class_name.php");
            // instantiate class
            $obj = new $class_name;

            if(LOADER_OPT['dir'] != NULL){
                if(isset($url->trimmed_uri[$url->basename_key+3])){
                    $url_data = $url->trimmed_uri[$url->basename_key+3]; 
                }
            }
            else{
                if(isset($url->trimmed_uri[$url->basename_key+2])){
                    $url_data = $url->trimmed_uri[$url->basename_key+2];
                }
            }

            // check for method
            if(!empty($ctrl_ent[1])){

                // check for number of args for routing with params
                if(LOADER_OPT['dir'] != NULL){
                    $slice_start = $url->basename_key+2; 
                }
                else{
                    $slice_start = $url->basename_key+1;
                }
                $params_array = array_slice($url->trimmed_uri,$slice_start);
                $params_array_count = count($params_array);

                // check if $ctrl_ent[1] is a method
                if(strpos($ctrl_ent[1],'{') !== FALSE && strpos($ctrl_ent[1],'}') !== FALSE){
                    $san_method_name = str_replace('{',NULL,$ctrl_ent[1]);
                    $san_method_name = str_replace('}',NULL,$san_method_name);

                    // check if method exists in class
                    if(!method_exists($obj, $san_method_name)){
                        if(DEBUG == TRUE){
                            throw new Exception("Invalid method, check your routing config or controller handler");
                        }
                        else{

                        }
                    }

                    $method_name = $san_method_name;

                    // check if method accepts args
                    if(!empty($ctrl_ent[2])){
                        if($params_array_count != $ctrl_ent[2]){
                            if(DEBUG == TRUE){
                                throw new Exception('Parameters count mismatch');
                            }
                            else{

                            }
                        }

                       if($ctrl_ent[3] != '$$'){
                           for($i=0;$i<=$params_array_count-1;$i++){
                                if($ctrl_ent[3] == '*'){
                                    if(!ctype_digit($params_array[$i])){
                                        if(DEBUG == TRUE){
                                            throw new Exception('url data value is not numeric');
                                        }
                                        else{

                                        }
                                    }
                                }
                                else if($ctrl_ent[3] == '$'){
                                    if(!ctype_alpha($params_array[$i])){
                                        if(DEBUG == TRUE){
                                            throw new Exception('url data value is not alphabetic');
                                        }
                                        else{

                                        }
                                    }
                                }
                                else if($ctrl_ent[3] == '*$'){
                                    if(!ctype_alnum($params_array[$i])){
                                        if(DEBUG == TRUE){
                                            throw new Exception('url data value is not alphanumeric');
                                        }
                                        else{

                                        }
                                    }
                                }    
                           }  
                        } 
                    }
                }
                else{
                    // then $ctrl_ent[1] is a url data
                    if($params_array_count != $ctrl_ent[1]){
                        if(DEBUG == TRUE){
                            throw new Exception('Parameters count mismatch');
                        }
                        else{

                        }
                    }

                   if($ctrl_ent[2] != '$$'){
                       for($i=0;$i<=$params_array_count-1;$i++){
                            if($ctrl_ent[2] == '*'){
                                if(!ctype_digit($params_array[$i])){
                                    if(DEBUG == TRUE){
                                        throw new Exception('url data value is not numeric');
                                    }
                                    else{

                                    }
                                }
                            }
                            else if($ctrl_ent[2] == '$'){
                                if(!ctype_alpha($params_array[$i])){
                                    if(DEBUG == TRUE){
                                        throw new Exception('url data value is not alphabetic');
                                    }
                                    else{

                                    }
                                }
                            }
                            else if($ctrl_ent[2] == '*$'){
                                if(!ctype_alnum($params_array[$i])){
                                    if(DEBUG == TRUE){
                                        throw new Exception('url data value is not alphanumeric');
                                    }
                                    else{

                                    }
                                }
                            }    
                       }  
                    }

                    $method_name = 'index';
                }
            }
            else{
                $method_name = 'index';
                $params_array = NULL;
            }

            if($method_name != 'index'){
                $obj->index($arg = $params_array);
                $obj->$method_name($arg = $params_array);
            }
            else{
                $obj->index($arg = $params_array);
            }

        }
        else{
            if(DEBUG == TRUE){
                throw new Exception('Controller handler not found, check your routing config');
            }
            else{

            }
        }
    }
}

/*
get url values after actual page and filters out query text.
*returns result in array
e.g category/heroes?page=1 "returns" category/heroes (without the '?page=1');
*/
function getval(){
    $url = inst("url_helper","helper");
    
    $tmp = $url->suf_pages;
    $str = NULL;
    $fin = array();
    
    for($i=0; $i<= count($tmp)-1; $i++){
        $append = ($i < count($tmp)-1) ? ',' : NULL;
        if(strpos($tmp[$i], "?") !== FALSE){
            $perma = explode("?", $tmp[$i]); 
            $str .= $perma[0].$append;
        }
        else{
            $str .= $tmp[$i].$append;
        }
    }
    
    $fin = explode(",", $str);
    
    return $fin;
}

function move_files($old_path, $new_path){
    if(!move_uploaded_file($old_path, $new_path)){
        throw new Exception("Unable to move file");
    }
    else{
        return TRUE;
    }
}