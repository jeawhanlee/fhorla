<?php
// fhola
function initCart(){
    if(USE_SHOPPING_CART == TRUE){
        // check if cart session is set
        if(!isset($_SESSION[FHOLA_CART])){
            // create cart session array
            $_SESSION[FHOLA_CART] = array();
            $_SESSION[CART_ATTR] = array();
        }   
    }
    else{
        if(isset($_SESSION[FHOLA_CART])){
            unset($_SESSION[FHOLA_CART]);
            unset($_SESSION[CART_ATTR]);
        }
    }
}
initCart();
// fhola