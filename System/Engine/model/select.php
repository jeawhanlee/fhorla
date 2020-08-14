<?php
include_once('db.php');

class Select extends Db{
    protected $query,
              $table,
              $column = "*",
              $where = array(),
              $order,
              $limit,
              $custom;

    private $sql;
    
    public function table($table){
        if(!isset($table)){
            throw new Exception('Table is not defined');
        }
        
        $this->table = $table;
        return $this;
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

    private function sql($count = FALSE){
        $where = $this->iniParamvalues($this->where);

        if($count){
            $this->column = "COUNT({$this->column})";
        }

        $this->sql = "SELECT $this->column FROM $this->table $where $this->custom $this->order $this->limit";
        return $this;
    }

    private function build(){
        $this->query = $this->conn->prepare($this->sql);
        
        $this->cusBindparam($this->where);
        
        return $this->query->execute();
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
}
