<?php
// fhola
class DB_Con {
    private $db_host,
            $db_name,
            $db_username,
            $db_password;
    
    public $conn;
    
    public function __construct(){
        $this->db_host = DB['db_host'];
        $this->db_name = DB['db_name'];
        $this->db_username = DB['db_username'];
        $this->db_password = DB['db_password'];
        // connect to db using pdo 
        try{
            $this->conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_username, $this->db_password);
            // error mode exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        }
        catch(PDOException $e){
            print 'Error: '.$e->getMessage();
        }
    }
}
// fhola