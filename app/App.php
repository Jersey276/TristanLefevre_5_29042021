<?php


namespace app;
class App {
    private static  $db_data = [
        "db_host" => "localhost",
        "db_base" =>"phpblog",
        "db_user" =>"root",
        "db_pass" =>"root"];
    
    static function load()
    {
        if(session_id() == '')
        {
            session_start();
        }
        require '../vendor/autoload.php';
    }

    static function getDBConnector() {
        return self::$db_data;
    }
}