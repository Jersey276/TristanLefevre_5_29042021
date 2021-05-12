<?php

namespace core\database;

use \PDO;
use core\env\dotenv;

class databaseManager{

    static private $pdo;
    private $db_dns;
    private $db_user;
    private $db_pass;

    public function __construct()
    {
        (new DotEnv(dirname(__DIR__,2) . '/.env'))->load();
        $this->db_dns = getenv('DATABASE_DNS'); 
        $this->db_user = getenv('DATABASE_USER');
        $this->db_pass = getenv('DATABASE_PASSWORD');
    }

    private function getPDO()
    {
        if(self::$pdo == null)
        {
            try {
                $pdo = new PDO($this->db_dns, $this->db_user, $this->db_pass);
                //$pdo = new PDO('mysql:dbname=phpblog;host=localhost', 'root', 'root');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                return print_r('Échec lors de la connexion : ' . $e->getMessage());
            }
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    public function query($query, $classname)
    {
        $req = $this->getPDO()->query($query);
        return $req->fetchAll(PDO::FETCH_CLASS, $classname);
    }

    public function prepare($query, $attribute, $type, $classname, $isOnly = false, $askReturn = false)
    {
        try {
        $req = $this->getPDO()->prepare($query);
        $req->execute($attribute);
        if ($type == "select" || $askReturn) {
            $req->setFetchMode(PDO::FETCH_CLASS, $classname);
            if ($isOnly)
            {
                return $req->fetch(PDO::FETCH_ASSOC);
            }
            return $req->fetchAll();
        }
        return true;
        } catch (Exception $e) {
            return false;
        }
    }
}