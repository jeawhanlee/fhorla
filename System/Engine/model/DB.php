<?php
require_once 'DB_Con.php';

class DB extends DB_Con{
     protected $query,
               $column = "*",
               $where = array(),
               $order,
               $limit,
               $custom,
               $data;

     protected static $table;

    private $sql;
    
    public static function table($table){
        if(!isset($table)){
            throw new Exception('Table is not defined');
        }
        
        self::$table = $table;
        return new static;
    }
    
    public function column($columns){
        $this->column = $columns;
        return $this;
    }
    
    public function where($where){
        $this->where = $where;
        return $this;
    }
    
    public function custom($custom){
        $this->custom = $custom;
        return $this;
    }
    
    public function order($order = ''){
        if(!empty($order)){
            $this->order = "ORDER BY $order";
        }
        
        return $this;
    }
    
    public function limit($limit = ''){
        if(!empty($limit)){
            $this->limit = "ORDER BY $limit";
        }
        
        return $this;
    }
    
    public function total(){
        $this->sql(TRUE);
        $this->build();

        return $this->query->fetchColumn();
    }
    
    public function read(){
        $this->sql();
        $this->build();
    
        return $this->query->fetchAll(PDO::FETCH_OBJ);
    }

    public function data($data){
          $this->data = $data;
          return $this;
     }

    private function sql($count = FALSE){
        $where = $this->iniParamvalues($this->where);

        if($count){
            $this->column = "COUNT({$this->column})";
        }

        $this->sql = "SELECT $this->column FROM ".self::$table." $where $this->custom $this->order $this->limit";
        return $this;
    }

    private function build(){
        $this->query = $this->conn->prepare($this->sql);
        
        $this->cusBindparam($this->where);
        
        return $this->query->execute();
    }
    
     public function create(){
        $bind_param_data = $this->create_iniparamvalues($this->data);
        
        $sql = "INSERT INTO ".self::$table." $bind_param_data";
        
        $this->query = $this->conn->prepare($sql);
        
        // bind param
        $this->create_cusBindparam($this->data);
        
        // execute
        return $this->query->execute();
    }
    
    public function lastid(){
        return $this->conn->lastInsertId();
    }

    
    public function update(){
        // set cols and vals param values
        $set_param_cols_vals = $this->iniParamvalues($this->data, ",");
        // check for extra set_column
        $extra_set = ($this->custom !== NULL) ? $this->custom : NULL ;
        if($this->custom !== NULL){
            if($this->data !== NULL){
                if(!empty($this->where)){
                    $this->custom = ",$this->custom";     
                }
                else{
                    $this->custom = 'WHERE '.$this->custom; 
                }
            }
            else{
                if(empty($this->where)){
                    $this->custom = 'WHERE '.$this->custom;     
                }
                else{
                    $this->custom = $this->custom;     
                }
            }  
        }
        // set where cols param values
        $set_param_where = $this->update_iniParamvalues($this->where, "and", "WHERE");

        $sql = "UPDATE ".self::$table." SET $set_param_cols_vals $this->custom $set_param_where";

        // prepare sql statement
        $this->query = $this->conn->prepare($sql);

        // bind cols_vals param values
        $this->update_cusBindparam($this->data);
        
        // bind where param values
        if(!empty($this->data)){
            $this->cusBindparam($this->where,count($this->data)+1);   
        }
        else if(!empty($this->where)){
            $this->cusBindparam($this->where);   
        }
        // execute query
        $execute = $this->query->execute();

        if(!$execute){
            return false;
        }
        else{
            return true;
        }
    }
    
    
    public function delete(){
        if(!empty($this->where)){
            // set where cols param values
            $set_param_where = $this->update_iniParamvalues($this->where, "and", "WHERE");

            $sql = "DELETE FROM ".self::$table." $set_param_where";

            // prepare sql statement
            $this->query = $this->conn->prepare($sql);

            // bind param where values
            $this->update_cusBindparam($this->where);

            // execute query
            $execute = $this->query->execute();

            if(!$execute){
                return false;
            }
            else{
                return true;
            }   
        }
    }





    private function iniParamvalues($where){
          if(empty($where)){
               $return = NULL;
          }
          else{
               $i = 1;
               $array_value = count($where);
               $not_equal = NULL;

               // init bind param where
               $bind_param_where = '';
               // allowed clauses
               $clauses = array("LIKE");
               foreach($where as $key => $value){
                    // check for last array element and append sql "and" keyword
                    // check where clause extension
                    $clause = explode(" ", $value);
                    $not_equal = (strpos($value, "!") !== FALSE) ? "!" : NULL;
                    
                    if(strpos($key, "or=") !== FALSE){
                         $joiner = " OR ";
                         $key = str_replace("or= ", NULL, $key);
                    }
                    else{
                         $joiner = " AND ";
                    }
                    
                    
                    if(count($clause) > 1){
                         $clause = $clause[0];
                         $clause = (in_array($clause, $clauses)) ? $clause : "=";
                         $query_append = ($i == $array_value) ? '' : $joiner;
                         $bind_param_where .= $key. " $clause ? ".$query_append;  
                    }
                    else{
                         $query_append = ($i == $array_value) ? '' : $joiner;
                         $bind_param_where .= $key. " $not_equal= ? ".$query_append;   
                    }
                    $i++;
               }
               
               
               if(strpos($bind_param_where," OR ") !== FALSE){
                    // convert where parameter to array
                    $bind_param_where = explode(" AND ",$bind_param_where);
                    // get last element index;
                    $last_el_or = count($bind_param_where)-1;
                    
                    // loop through each array element and check before the
                    for($i=0; $i<=$last_el_or;$i++){
                         // check for string with AND clause
                         if(strpos($bind_param_where[$i]," OR ") !== FALSE){
                              // convert string to array
                              $string_with_or = explode(" OR ",$bind_param_where[$i]);
                              // get the last 'and' element
                              $last_el = count($string_with_or)-1;
                              $string_with_or[0] = "(".$string_with_or[0];
                              $string_with_or[$last_el] = $string_with_or[$last_el].")";
                              
                              // push back to bind_param_where array after construction
                              $bind_param_where[$i] = implode(" OR ", $string_with_or);
                         }
                    }
                    
                    $bind_param_where = implode(" AND ",$bind_param_where);
               }

               $return = "WHERE $bind_param_where";
          }
          return $return;
     }
     
     private function cusBindparam($where){
          if(is_array($where)){
               $i = 1;
               foreach($where as $key => &$value){
                    if(strpos($value, "LIKE") !== FALSE){
                         $value = "%".str_replace("LIKE ", NULL, $value)."%";
                    }
                    else if(strpos($value, "!") !== FALSE){
                         $value = str_replace("!",NULL,$value);
                    }
                    $this->query->bindParam($i, $value);
                    $i++;
               }
          }
     }

    private function create_iniparamvalues($data){
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

     private function create_cusBindparam($data){
          $i = 1;
          foreach($data as $key => &$value){
               $this->query->bindParam($i, $value);
               $i++;
          }
     }

     private function update_iniParamvalues($data, $sql_append_clause, $sql_clause=NULL){
          if($data == NULL){
          $return = NULL;
          }
          else{
          $i = 1;
          $array_value = count($data);

          // init bind param where
          $bind_param_data = '';
          foreach($data as $key => $value){
               // check for last array element and append sql "and" keyword
               $query_append = ($i == $array_value) ? NULL : " $sql_append_clause ";
               $bind_param_data .= $key.' = ?'.$query_append;
               $i++;
          }

          $return = "$sql_clause $bind_param_data";
          }

          return $return;
     }
 
     private function update_cusBindparam($data,$cont = NULL){
          if($data != NULL){
            $i = !empty($cont) ? $cont : 1;
            foreach($data as $key => &$value){
                $this->query->bindParam($i, $value);
                $i++;
            }
          }
          else{
            return false;
          }
     }
}