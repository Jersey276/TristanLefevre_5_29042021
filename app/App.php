<?php


namespace app;
class App {
    
    static function load()
    {
        if(session_id() == '')
        {
            session_start();
        }
        require '../vendor/autoload.php';
    }
}