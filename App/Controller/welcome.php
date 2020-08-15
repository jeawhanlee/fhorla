<?php
class Welcome extends Fh_ctrl{   
    // hello world
    public function index(){
        $select = inst('select','db');
        $this->render(
            [
                'title' => 'Welcome to fhorla',
                'welcome_text' => "Hi, i'm Fhola",
                'first_small_txt' => 'V 1.0',
                'second_small_txt' => 'A simple PHP framework',
                'small_txt' => "coDe iN biTs",
                'users' => $select->table('users')->read(),
                'total' => $select->table('users')->total()
            ]
        );
    }
    
    public function hello(){
        global $data;
        
        $data['hello_page_txt'] = 'Welcome to the hello page';
    }
    
    public function new_page($arg){
        global $data;
        
        $data['new_page_txt'] = 'Welcome to the new page';
    }
}