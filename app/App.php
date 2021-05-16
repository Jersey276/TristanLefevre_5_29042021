<?php


namespace app;

use core\database\databaseManager;

class App {
    private static $db;
    static function load()
    {
        if(session_id() == '')
        {
            session_start();
        }
        require '../vendor/autoload.php';
    }
    static function getDB()
    {
        if (empty(self::$db))
        {
            self::$db = new DatabaseManager();
        }
        return self::$db;
    }
}