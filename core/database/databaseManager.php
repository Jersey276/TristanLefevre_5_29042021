<?php

namespace core\database;

use \PDO;
use core\env\dotenv;

class DatabaseManager{

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
            try
            {
                $pdo = new PDO($this->db_dns, $this->db_user, $this->db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e)
            {
                return print_r('Échec lors de la connexion : ' . $e->getMessage());
            }
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    public function query($query, $classname = null)
    {
        $req = $this->getPDO()->query($query);
        return $req->fetchAll(PDO::FETCH_CLASS, $classname);
    }
    public function prepare($query, $type, $classname = null, $isOnly = false)
    {
        try
        {
            $req = $this->getPDO()->prepare($query);
            $req->execute();
            switch ($type)
            {
                case ("select") :
                    
                    $req->setFetchMode(PDO::FETCH_CLASS, $classname);
                    if ($isOnly)
                    {
                        return $req->fetch(PDO::FETCH_ASSOC);
                    }
                    return $req->fetchAll();
                    break;
                case ("insert") :
                    return $this->getPDO()->lastInsertId();
                    break;
                default :
                    return true;
            }
        } 
        catch (Exception $e)
        {
            var_dump($e->getmessage());
            return false;
        }
    }
}