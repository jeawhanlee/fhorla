<?php
// fhola
class db_helper{
    protected $res_array;
    
    public function dbResult_array(){
        $result = array();

        while($row = $this->res_array->fetchResult()){
            $result[] = $row;
        }
        return $result;
    }
    
/*
    LIGHT PAGINATION REUSABLE FUNCTIONS(paginationHead(MariaDB tcolumn, MariaDB tname, WHERE['MIXED_STRING'], ORDER['MIXED_STRING'], INT) & paginationFooter(URL['MIXED_STRING]))
    HEAD VALIDATIONS
    *@paginationHead() requires 3 param
    *@param 5 - optional
    *@param 5 ! defined : function takes default value of 30
    *column param : column to source result from (unique || *)
    *tablename param,
    *order param,
    *where param,
    "*" param are dependent on MariaDB
    
    *@paginationFooter() is dependent @paginationCore()
    variables are global for external refrence in case of custom @paginationFooter() scripting
*/    
    
    public function paginationCore($column, $tablename, $where, $order, $inner_column_append = NULL, $items_per_page = 20){
        // define variables as GLOBAL
        global $total_rows,
                $append_link,
                $total_pages,
                $page_num,
                $start_row,
                $limit,
                $countpaged;

            $select = inst("select","db");
            $total_rows = $select->countRows($tablename, $where, $inner_column_append,$column);
            if($total_rows > 0){
                $append_link = (isset($_GET['page'])) ? '&page='.$_GET['page'] : '';
                $total_pages = ceil($total_rows/$items_per_page);

                if(isset($_GET['page'])){
                    $page_num = preg_replace('#[^0-9^]#', '', $_GET['page']);
                }
                else{
                    $page_num = 1;
                }

                if($page_num < 1){
                    $page_num = 1;
                }
                elseif($page_num > $total_pages){
                    $page_num = $total_pages;
                }
                $start_row = ($items_per_page * ($page_num - 1));
                $limit = "LIMIT ".$start_row.", ".$items_per_page;
                $query = $select->selectData($column, $tablename, $where, $order, $limit, $inner_column_append);
                $countpaged = $select->rowCount();
                
                $result = array();
                while($row = $select->fetchResult()){
                    $result[] = $row;
                }
                return $result;
            }
            else{
                return false;
            }
    }
}
// fhola