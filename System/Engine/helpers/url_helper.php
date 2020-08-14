<?php
class url_helper{
    public $page,
            $suf_pages = array(),
            $count_array,
            $basename_key,
            $trimmed_uri = array();
    
    public function __construct(){
        $root_app = '/'.ROOT_DIR;
        $uri = (strpos($_SERVER['REQUEST_URI'], $root_app) !== FALSE) ? $_SERVER['REQUEST_URI'] : $root_app.$_SERVER['REQUEST_URI'];
        
        $this->trimmed_uri = array_filter(explode('/', $uri));
        // get index no
        $this->basename_key = array_keys($this->trimmed_uri, ROOT_DIR)[0];
        
        $this->count_array = count($this->trimmed_uri);
        
        if($this->count_array > $this->basename_key){
            // check for query url
            if($_GET){
                if($this->count_array > $this->basename_key){
                    if(isset($_SESSION[USER_SESSION])){
                        // check if there is a user directory
                        if(LOADER_OPT['dir'] != NULL){
                            $this->page = $this->trimmed_uri[$this->basename_key+2];   
                        }
                        else{
                            $this->page = $this->trimmed_uri[$this->basename_key+1];
                        }
                    }
                    else{
                        $this->page = $this->trimmed_uri[$this->basename_key+1];
                    }
                }
                else{
                    $basename = basename($uri);
                    $basename = explode("?", $basename);

                    $this->page = $basename[0];   
                }
            }
            else{
                if(isset($_SESSION[USER_SESSION])){
                        // check if there is a user directory
                        if(LOADER_OPT['dir'] != NULL){
                            if(!isset($this->trimmed_uri[$this->basename_key+2])){
                                $this->page = LOADER_OPT['landing'];
                            }
                            else{
                                $this->page = $this->trimmed_uri[$this->basename_key+2];
                            }
                        }
                        else{
                            $this->page = $this->trimmed_uri[$this->basename_key+1];
                        }
                    }
                    else{
                        $this->page = $this->trimmed_uri[$this->basename_key+1];
                    }
            }

            $this->suf_pages = array_slice($this->trimmed_uri, $this->basename_key+1, $this->count_array-$this->basename_key);
        }
    }
}