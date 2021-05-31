<?php

namespace core\database;

use \PDO;
use PDOException;

class DatabaseManager
{
    /**
     * @var PDO instance of PDO
     */
    private static $pdo;
    /**
     * @var string domaine name
     */
    private $db_dns;
    /**
     * @var string database user
     */
    private $db_user;
    /**
     * @var string database password
     */
    private $db_pass;

    public function __construct()
    {
        $this->db_dns = getenv('DATABASE_DNS');
        $this->db_user = getenv('DATABASE_USER');
        $this->db_pass = getenv('DATABASE_PASSWORD');
    }

    /**
     * Generate PDO statement
     * @return PDO pdo instance
     */
    private function getPDO()
    {
        if (self::$pdo == null) {
            try {
                $pdo = new PDO($this->db_dns, $this->db_user, $this->db_pass);
            } catch (PDOException $e) {
                return print_r('Échec lors de la connexion : ' . $e->getMessage());
            }
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }
    /**
     * use query to database and return result
     * @param string prepared query
     * @param array data used by query
     * @param string type type of query (select, insert, update, delete)
     * @param string path to class (work only for select multiple object)
     * @param bool needed only if you ask for one data
     * @return array results
     */
    public function prepare(string $query, array $data, 
    string $type, string $classname = null, bool $isOnly = false)
    {
        try {
            $req = $this->getPDO()->prepare($query);
            $req->execute($data);
            switch ($type) {
                case ("select"):
                    if ($isOnly) {
                        return $req->fetch(PDO::FETCH_ASSOC);
                    }
                    $req->setFetchMode(PDO::FETCH_CLASS, $classname);
                    return $req->fetchAll();
                    break;
                case ("insert"):
                    return $this->getPDO()->lastInsertId();
                    break;
                default:
                    return true;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
}
