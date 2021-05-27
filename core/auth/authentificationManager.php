<?php

namespace core\auth;

use App;
use core\database\InsertQuery;
use core\database\SelectQuery;
use core\database\UpdateQuery;
use core\database\DeleteQuery;
use core\database\DatabaseManager;
use core\request\RequestManager;

/**
 * class for manage authentification system
 */
class AuthentificationManager
{
    /**
     * instance of this class (Singleton)
     */
    private static $instance;
    /**
     * used database
     */
    private $database;

    private function __construct(databaseManager $database)
    {
        $this->database = $database;
    }
    
    /**
     * get instance of this class (Singleton)
     * @param databaseManager database manager for all database related operations
     * @return authentificationManager instance of this class
     */
    public static function getInstance($database = null)
    {
        if (empty(self::$instance)) {
            self::$instance = new authentificationManager($database);
        }
        return self::$instance;
    }

    /**
     * check if user have remaining try by his IP Adress
     * @return bool result
     */
    private function checkTry()
    {
        $request = new RequestManager();
        $try = $this->database->prepare(
            (new SelectQuery())
                ->select('*')
                ->from('loginlog')
                ->where('TIMEDIFF( NOW(), timestamp) < "00:01:00"')
                ->where("ipAddress = :ipAddress")
                ->toString(),
            [ ":ipAddress" => $request->getIpAddr()],
            "select",
            "loginLog"
        );
        if (count($try) < 3) {
            return true;
        }
        return false;
    }

    /**
     * report last try
     * @return bool validation of process
     */
    private function reportTry()
    {
        $request = new RequestManager();
        return $this->database->prepare(
            (new InsertQuery())
                ->insertInto('loginlog')
                ->key('ipAddress')
                ->value(":ipAddress")
                ->toString(),
            [":ipAddress" => $request->getIpAddr()],
            'insert'
        );
    }

    /**
     * get user and fill session var with
     * @param string login
     * @param string password
     * @return array result with message in case of connection fail
     */
    public function login($login, $password)
    {
        if ($this->checkTry()) {
            $query = new SelectQuery('select');
            $statement = $query
                ->select(
                    'user.pseudo',
                    'user.email',
                    'user.password',
                    'role.nameRole as role',
                    'user.isEmailChecked'
                )
                ->from('user')
                ->leftjoin('role', 'role.idRole = user.idRole')
                ->where("pseudo = :login")
                ->toString();
            $account = $this->database->prepare(
                $statement,
                [":login" => $login],
                "select",
                "\app\model\User",
                true
            );
            if ($account != false && password_verify($password, $account['password'])) {
                if ($account['isEmailChecked'] == true) {
                    $this->reportTry();
                    $request = new RequestManager();
                    $request->session('pseudo', $account['pseudo']);
                    $request->session('email', $account['email']);
                    $request->session('role', $account['role']);
                    $request->session('ipAddress', $request->getIpAddr());
                    return ['isConnected' => true];
                }
                return ["isConnected" => false,
                        "message" => "l'adresse mail n'a pas été vérifié, consulter vos mails"];
            }
            $this->reportTry();
            return ["isConnected" => false,
                    "message" => "identifiant / mot de passe incorrect, veuiller ressayer"];
        }
        return ["isConnected" => false, "message" =>
                "vous avez fais trop d' essai, veuiller attendre 1 minute avant de recommencer"];
    }

    /**
     * insert new user into database
     * @param string pseudo of new user
     * @param string verified password of new user
     * @param string email of new user
     * @return mixed result of database request
     */
    public function register($pseudo, $password, $email)
    {
        $password =  password_hash($password, PASSWORD_BCRYPT);
        $query = new InsertQuery();
        $awnser = $this->database->prepare(
            $query
            ->insertInto('user')
            ->key('pseudo', 'password', 'email')
            ->value(':pseudo', ':password', ':email')
            ->toString(),
            [
            ':pseudo' => $pseudo ,
            ':password' => $password,
            ':email' => $email],
            'insert'
        );
        return $awnser;
    }

    /**
     * Create and generate a new token
     * @param string type of token
     * @param string argument to use depending of type (email for password type, idUser for email validation)
     * @return string token on success
     * @return false fail
     */
    public function askToken($type, $arg)
    {
        switch ($type) {
            case 'password':
                $statementSearch = (new SelectQuery())
                    ->select('*')
                    ->from('user')
                    ->where("email = :email")
                    ->toString();
                $account = $this->database->prepare(
                    $statementSearch,
                    [ ":email" => $arg ],
                    "select",
                    "app\model\user",
                    true
                );
                if ($account != false) {
                    $data = [
                    ':token' => tokenGenerator::strRandom(),
                    ':idUser' => $account['idUser'],
                    ':idType' => 1
                    ];
                    break;
                }
                return false;
                break;
            case 'email':
                $data = [
                    ':token' => tokenGenerator::strRandom(),
                    ':idUser' => $arg,
                    ':idType' => 2
                ];
                break;
        }
        $statement = (new InsertQuery())
            ->insertInto('token')
            ->key('token', 'idUser', 'idTokenCategory')
            ->value(':token', ':idUser', ':idType')
            ->toString();
        if ($this->database->prepare($statement, $data, "insert")) {
            return ($data[':token']);
        }
        return false;
    }
    /**
     * Use token and change user data depending of function
     * @param token token
     * @param function type of functions
     * @param password (to be used only when ou need to change password)
     * @return bool result of operation
     */
    public function useToken($token, $function, $password = null)
    {
        switch ($function) {
            case 'password':
                $set = "password = :password";
                $data = [':password' => password_hash($password, PASSWORD_BCRYPT) ];
                break;
            case 'email':
                $set = "isEmailChecked = :isEmail";
                $data = [':isEmail' => true];
                break;
        }
        $statement = (new UpdateQuery())
            ->update("user")
            ->set($set)
            ->where("idUser = :idUser")
            ->toString();
        if ($this->database->prepare(
            $statement,
            array_merge($data, [":idUser" => $token['idUser']]),
            "update"
        )
        ) {
            $this->database->prepare(
                (new DeleteQuery())
                    ->delete('token')
                    ->where("idToken = :idToken ")
                    ->toString(),
                [':idToken' => $token['idToken']],
                "delete"
            );
            return true;
        }
        return false;
    }
}
