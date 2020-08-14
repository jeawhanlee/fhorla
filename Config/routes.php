<?php
// * for numeric data values
// $ for alpha data values
// *$ for alphanumeric data values
// $$ for any data value

// passing args to urls
/*
for url having arguments tied directly to the index method of their controllers
    e.g
    'welcome' => 'welcome/1/*'
    
    where welcome is the controller
    1 is the no of arguments to be passed to the method
    * is the data type to be passed...
    
for url having arguments tied directly to a custom method of their controllers
    e.g
    'new_page' => 'welcome/{new_page}/1/*'
    
    where welcome is the controller
    {new_page} is custom method of the class. NB methods are wrapped around curly braces
    1 is the no of arguments to be passed to the method
    * is the data type to be passed...
*/

$assign = [
    'welcome' => 'welcome/'
];



define('ASG',$assign);