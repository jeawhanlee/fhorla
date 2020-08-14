<?php
include_once 'db.php';

class Altar extends Db{
    protected $query,
               $table,
               $data,
               $where,
               $custom;

     public function table($table){
          if(!isset($table)){
               throw new Exception('Table is not defined');
          }
          
          $this->table = $table;
          return $this;
     }

     public function data($data){
          $this->data = $data;
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
        $set_param_where = $this->iniParamvalues($this->where, "and", "WHERE");

        $sql = "UPDATE $this->table SET $set_param_cols_vals $this->custom $set_param_where";

        // prepare sql statement
        $this->query = $this->conn->prepare($sql);

        // bind cols_vals param values
        $this->cusBindparam($this->data);
        
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
            $set_param_where = $this->iniParamvalues($this->where, "and", "WHERE");

            $sql = "DELETE FROM $this->table $set_param_where";

            // prepare sql statement
            $this->query = $this->conn->prepare($sql);

            // bind param where values
            $this->cusBindparam($this->where);

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

    private function iniParamvalues($data, $sql_append_clause, $sql_clause=NULL){
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
 
     private function cusBindparam($data,$cont = NULL){
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