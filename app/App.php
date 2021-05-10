<?php

use app\util\database\DatabaseManager;

class App {

    static function load() {
        session_start();
        require '../vendor/autoload.php';
    }
}