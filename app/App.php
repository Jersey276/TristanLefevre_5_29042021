<?php


namespace app;

use core\database\DatabaseManager;
use core\env\DotEnv;

class App
{
    /**
     * @var DatabaseManagerinstance of database manager
     */
    private static $db;

    /**
     * load all base function, like autoloader, local time and env var
     */
    public static function load()
    {
        if (session_id() == '') {
            session_start();
        }
        require '../vendor/autoload.php';
        (new DotEnv(dirname(__DIR__, 1) . '/.env'))->load();
        setlocale(LC_ALL, 'French_France.1252', 'fr_FR', 'fr_FR@euro', 'fr_FR.utf8');
        date_default_timezone_set('Europe/Paris');
    }

    /**
     * get databaseManager instance
     * @return DatabaseManager instance of database manager
     */
    public static function getDB() : DatabaseManager
    {
        if (empty(self::$db)) {
            self::$db = new DatabaseManager();
        }
        return self::$db;
    }
}
