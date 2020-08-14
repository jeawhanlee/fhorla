<?php

class Fh_ctrl{
    protected $data = array();

    protected function render($data = NULL){
        // view loader
        $loader = new loader;
        $loader->loader($data);
    }
    
    protected function createUser_graph($string){
        global $user_graph;
        $i = 1;
        $count = count(USER_GRAPH);
        $keys_col = NULL;

        foreach(USER_GRAPH as $key => $value){
            // get keys
            $append = ($i < $count) ? ',' : NULL;
            $keys_col .= $key.$append;
            $i++;
        }

        $keys = createStr_array($keys_col);
        $new_keys = createStr_array($string);

        $user_graph = array_combine($keys, $new_keys);
        
        load("config/user_graph");

        if(!$user_graph){
            throw new Exception("Could not create user graph");
        }

        return $user_graph;
    }
    
    protected function newCookie($name,$value,$days,$array = FALSE){
        setcookie("fh_test_cookie", "Hello World", time() + 3600, '/');
        if(count($_COOKIE) > 0){
            // delete test cookie
            setcookie("fh_test_cookie", NULL, time() - 3600);
            
            // check if cookie is set
            if(!isset($_COOKIE[$name])){
                if($array == TRUE){
                    $value = json_encode($value);
                }
                // create new cookie
                setcookie($name, $value, time() + (86400 * $days), "/");   
            }   
        }
        else{
            return FALSE;
        }
    }
    
    protected function getCookie($cookie_name, $array = FALSE){
        if(isset($_COOKIE[$cookie_name])){
            if($array == TRUE){
                $cookie = json_decode($_COOKIE[$cookie_name], TRUE);
            }
            else{
                $cookie = $_COOKIE[$cookie_name];
            }
            
            return $cookie;
        }
        else{
            return FALSE;
        }
    }
    
    protected function deleteCookie($cookie_name){
        if(isset($_COOKIE[$cookie_name])){
            setcookie($cookie_name, NULL, time() - 3600, "/");   
        }
    }
    
    protected function sess($sess_name,$sess_value){
        $_SESSION[$sess_name] = $sess_value;
    }
    
    protected function remove_sess($sess_name,$sess_value=NULL){
        if($sess_value !== NULL){
            unset($_SESSION[$sess_name][$sess_value]);   
        }
        else{
            unset($_SESSION[$sess_name]);
        }
    }
    
    protected function update_sess($sess_name,$value,$key=NULL){
        if($key !== NULL){
            $_SESSION[$sess_name][$key] = $value;
        }
        else{
            $_SESSION[$sess_name] = $value;  
        }
    }
    
    protected function user_session($sess_value){
        $_SESSION[USER_SESSION] = $sess_value;      
    }
    
    protected function session_value($value){
        return $_SESSION[USER_SESSION]["$value"];
    }
    
    protected function unset_user_session(){
        unset($_SESSION[USER_SESSION]);
    }
    
    // form fields controllers
    protected function check_fields($data,$msgs = array()){
        $err_msg = NULL;
        // check for empty field
        $errors = isempty($data, $msgs);
        if(count($errors) != 0){
            if($msgs == NULL){
                $err_msg = "Empty fields detected";
            }
            else{
                // loop through array fields and display errors
                for($i=0;$i<=count($errors)-1;$i++){
                    $append = $i < count($errors)-1 ? '<br />' : NULL;
                    $err_msg .= $errors[$i].$append;
                }
            }
            throw new Exception($err_msg);
        }
    }
    
    protected function post($name){
        if(isset($_POST["$name"])){
            return $_POST["$name"];
        }
        else{
            return FALSE;
        }
    }
    
    protected function postVal($post_val,$function){
        if(isset($_POST[$post_val])){
            if (is_callable($function)) {
                $function();
            } 
            else{
                return FALSE;
            }
        }
        else{
            return FALSE;
        }
    }
    
    protected function apiGet(){
        return json_decode(file_get_contents("php://input"), true);
    }
    
    protected function apiVal($fields,$val,$function){
        if(isset($fields[$val])){
            if (is_callable($function)) {
                $function();
            } 
            else{
                return FALSE;
            }
        }
        else{
            return FALSE;
        }
    }
    
    protected function get($name){
        if(isset($_GET["$name"])){
            return $_GET["$name"];
        }
        else{
            return FALSE;
        }
    }
    
    protected function getVal($get_val,$function){
        if(isset($_GET[$get_val])){
            if (is_callable($function)) {
                $function();
            } 
            else{
                return FALSE;
            }
        }
        else{
            return FALSE;
        }
    }
    
    protected function check_email($email,$err_msg = NULL){
        // check email
        if(!check_email($email)){
            if($err_msg == NULL){
                $err_msg = "your e-mail is invalid";
            }
            throw new Exception($err_msg);
        }
    }
    
    protected function isint($field, $err_msg){
        // check if value is integer
        if(!is_numeric($field)){
            throw new Exception($err_msg);
        }
    }
    
    protected function confirm_pass($pass1, $pass2){
        // check if password match
        if($pass1 != $pass2){
            throw new Exception("We detected a password mismatch");
        }
    }
    // end form controllers
    
    // cart controllers
    protected function addTocart($product_id, $duplicate = TRUE){

        // check if product has been added to cart
        if(USE_SHOPPING_CART == TRUE){
            // if product is not in array
            if(!isset($_SESSION[FHOLA_CART][$product_id])){
                $_SESSION[FHOLA_CART][$product_id] = 1;
                return true;
            }
            
            // check if cart product is duplicate
            if($duplicate == TRUE){
                // if product is in array
                if(isset($_SESSION[FHOLA_CART][$product_id])){
                    $_SESSION[FHOLA_CART][$product_id]++;
                    return true;
                }
            }
            else{
                throw new Exception("Item is already in cart");
            }
        }
        else{
            throw new Exception("Fhola shopping cart is disabled");
        }
    }
    
    public static function cart_counter(){
        if(USE_SHOPPING_CART == TRUE){
            $total_items = 0;
            if(!empty($_SESSION[FHOLA_CART])){
                foreach($_SESSION[FHOLA_CART] as $key => $value){
                    $total_items += $value;
                }      
            }
            return $total_items;  
        }
        else{
            throw new Exception("Fhola shopping cart is disabled");
        }
    }
    
    public function itemQty($item_id){
        if(USE_SHOPPING_CART == TRUE){
            if(isset($_SESSION[FHOLA_CART][$item_id])){
                return $_SESSION[FHOLA_CART][$item_id];    
            }
        }
        else{
            throw new Exception("Fhola shopping cart is disabled");
        }
    }
    
    public function cart_array(){
        if(USE_SHOPPING_CART == TRUE){
            return $_SESSION[FHOLA_CART];
        }
        else{
            throw new Exception("Fhola shopping cart is disabled");
        }
    }
    
    protected function remove_cart_item($item){
        if(USE_SHOPPING_CART == TRUE){
            // check if cart item
            if(isset($_SESSION[FHOLA_CART][$item])){
                unset($_SESSION[FHOLA_CART][$item]);
                return true;
            }
            else{
                return false;
            }
        }
        else{
            throw new Exception("Fhola shopping cart is disabled");
        }
    }
    
    protected function addQty($product_id,$qty){
        if(USE_SHOPPING_CART == TRUE){
            if($qty <= 0){
                unset($_SESSION[FHOLA_CART][$product_id]);
            }
            else{
                $_SESSION[FHOLA_CART][$product_id] = $qty;   
            }
        }
        else{
            throw new Exception("Fhola shopping cart is disabled");
        }
    }
    
    protected function emptycart(){
        $_SESSION[FHOLA_CART] = array();
    }
    
    // end cart controllers
    
    // force download file
    protected function getfile($file, $file_path){
        //Parse information
        $pathinfo = pathinfo($file);
        $extension = strtolower($pathinfo['extension']);
        
        $mimetype = null;
        
        $path_to_file = $file_path.'/'.$file;
        $path_to_file = str_replace('//','/',$path_to_file);
        
        //Check if file exists
          if (!file_exists($path_to_file)) {
            throw new Exception("The file you are looking for does not exist or has been moved.");
          }

          //Check if file is readable
          if (!is_readable($path_to_file)) {
            throw new Exception("An error occurred while trying to get your file.");
          }
        
        //Get mimetype for extension
        //This list can be extended as you need it.
        //A good start to find mimetypes is the apache mime.types list
        // http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
        switch ($extension) {
            case 'avi':     $mimetype = "video/x-msvideo"; break;
            case 'doc':     $mimetype = "application/msword"; break;
            case 'exe':     $mimetype = "application/octet-stream"; break;
            case 'flac':    $mimetype = "audio/flac"; break;
            case 'gif':     $mimetype = "image/gif"; break;
            case 'jpeg':
            case 'jpg':     $mimetype = "image/jpg"; break;
            case 'json':    $mimetype = "application/json"; break;
            case 'mp3':     $mimetype = "audio/mpeg"; break;
            case 'mp4':     $mimetype = "application/mp4"; break;
            case 'ogg':     $mimetype = "audio/ogg"; break;
            case 'pdf':     $mimetype = "application/pdf"; break;
            case 'png':     $mimetype = "image/png"; break;
            case 'ppt':     $mimetype = "application/vnd.ms-powerpoint"; break;
            case 'rtf':     $mimetype = "application/rtf"; break;
            case 'sql':     $mimetype = "application/sql"; break;
            case 'xls':     $mimetype = "application/vnd.ms-excel"; break;
            case 'xlsx':    $mimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
            case 'xml':     $mimetype = "application/xml"; break;
            case 'zip':     $mimetype = "application/zip"; break;
            default:        $mimetype = "application/force-download";
        }
    
        
        //Set header
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for some browsers
        header('Content-Type: '.$mimetype);
        header('Content-Disposition: attachment; filename="'.basename($path_to_file).'";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($path_to_file));
        ob_clean();
        flush();
        readfile($path_to_file);
    }
    
    protected function ajax(){
        exit;
    }
    
    protected function remote_request($param = array()){
        if(isset($param['url']) && isset($param['fields'])){
            if(empty($param['url']) || empty($param['fields'])){
                throw new Exception("remote_request contains empty parameter values");
            }   
        }
        else{
            throw new Exception("Incomplete parameter count for remove_request");
        }

        // build the urlencoded data
        $postvars = http_build_query($param['fields']);

        // open connection
        $ch = curl_init();

        // set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $param['url']);
        curl_setopt($ch, CURLOPT_POST, count($param['fields']));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);

        // execute post
        $result = curl_exec($ch);

        // close connection
        curl_close($ch);
    }
}