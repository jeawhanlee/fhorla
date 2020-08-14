<?php
include_once 'db.php';

class Insert extends Db{
    protected $query,
               $table,
               $data;

     public function table($table){
          $this->table = $table;
          return $this;
     }

     public function data($data){
          $this->data = $data;
          return $this;
     }
    
     public function create(){
        $bind_param_data = $this->iniparamvalues($this->data);
        
        $sql = "INSERT INTO $this->table $bind_param_data";
        
        $this->query = $this->conn->prepare($sql);
        
        // bind param
        $this->cusBindparam($this->data);
        
        // execute
        return $this->query->execute();
    }
    
    public function lastid(){
        return $this->conn->lastInsertId();
    }

    private function iniparamvalues($data){
     $i = 1;
     $array_value = count($data);

     // init bind param where
     $bind_param_data_key = NULL;
     $bind_param_data_value = NULL;
     foreach($data as $key => $value){
         // check for last array element and append sql "and" keyword
         $query_append = ($i < $array_value) ? ', ' : '';
         $bind_param_data_key .= $key.$query_append;
         $bind_param_data_value .= '?'.$query_append;
         $i++;
     }
     
     $cols_vals = "($bind_param_data_key) values($bind_param_data_value)";
     return $cols_vals;
 }
 
  private function cusBindparam($data){
     $i = 1;
     foreach($data as $key => &$value){
         $this->query->bindParam($i, $value);
         $i++;
     }
 }
}