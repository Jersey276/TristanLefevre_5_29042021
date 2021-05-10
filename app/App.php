<?php

use app\util\database\DatabaseManager;

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