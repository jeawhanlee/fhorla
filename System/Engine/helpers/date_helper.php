<?php
// fhola
class date_helper{
    public function dateTostr($then, $now){
        $then_new = date_create($then);
        $now = date_create($now);
        
        $date = date_diff($then_new, $now);
        $return = str_replace("+", NULL, $date->format("%R%a"));
        $return = (int) str_replace("-", NULL, $return);
        
        if($return == 0){
            $string = 'today';
        }
        else if($return == 1){
            $string = 'a day ago';
        }
        else if($return == 2){
            $string = '2 days ago';
        }
        else if($return == 3){
            $string = '3 days ago';
        }
        else if($return == 4){
            $string = '4 days ago';
        }
        else if($return == 5){
            $string = '5 days ago';
        }
        else if($return == 6){
            $string = '6 days ago';
        }
        else if($return == 7 || $return > 7 && $return < 14){
            $string = 'a week ago';
        }
        else if($return == 14 || $return > 14 && $return < 21){
            $string = '2 weeks ago';
        }
        else if($return == 21 || $return > 21 && $return < 28){
            $string = '3 weeks ago';
        }
        else if($return == 28 || $return == 29 || $return == 30){
            $string = '4 weeks ago';
        }
        else if($return > 30 || $return > 31 && $return  < 60){
            $string = 'a month ago';
        }
        else if($return > 60){
            $string = '2 months ago';
        }
        else{
            $string = formatDate($then, "date");
        }
        
        return $string;
    }
}
// fhola