<?php

namespace core\database;

use \PDO;

class databaseManager{

    static private $pdo;
    private $db_name;
    private $db_host;
    private $db_user;
    private $db_pass;

    public function __construct($db_name, $db_user = 'root', $db_pass = 'root', $db_host='localhost')
    {
        $this->db_name = $db_name; 
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_host = $db_host;
    }

    private function getPDO()
    {
        if(self::$pdo == null)
        {
            //$pdo = new PDO('mysql:dbname='. $this->db_name .';host='. $this->db_host. '', $this->db_user, $this->db_pass);
            $pdo = new PDO('mysql:dbname=phpblog;host=localhost', 'root', 'root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    public function query($query, $classname)
    {
        $req = $this->getPDO()->query($query);
        return $req->fetchAll(PDO::FETCH_CLASS, $classname);
    }

    public function prepare($query, $attribute, $type, $classname, $isOnly = false)
    {
        $req = $this->getPDO()->prepare($query);
        $req->execute($attribute);
        if ($type == "select") {
            $req->setFetchMode(PDO::FETCH_CLASS, $classname);
            if ($isOnly)
            {
                return $req->fetch();
            }
            return $req->fetchAll();
        }
        return true;
    }
}