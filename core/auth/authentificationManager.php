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
	 * 
	 * @param 
	 */
	public static function getInstance($database = null)
	{
		if(empty(self::$instance))
		{
			self::$instance = new authentificationManager($database);
		}
		return self::$instance;
	}

	/**
	 * get user and fill session var with 
	 * @param string login
	 * @param string password
	 */
	public function login($login, $password) 
	{
		$query = new SelectQuery('select');
		$statement = $query
			->select('user.pseudo','user.email','user.password','role.nameRole as role','user.isEmailChecked')
			->from('user')
			->leftjoin('role','role.idRole = user.idRole')
			->where("pseudo = '".$login."'")
			->toString();
		$account = $this->database->prepare($statement,"select", "\app\model\User", true);
		if ($account != false && password_verify($password, $account['password']))
		{
			if($account['isEmailChecked'] == true) 
			{
				$session = new requestManager();
				$session->session('pseudo', $account['pseudo']);
				$session->session('email', $account['email']);
				$session->session('role', $account['role']);
				return ['isConnected' => true];
			}
			return ['isConnected' => false, 'message' => "l'adresse mail n'a pas été vérifié, consulter vos mails"];
		}
		return [ 'isConnected' => false, 'message' => 'identifiant / mot de passe incorrect, veuiller ressayer'];
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
		$awnser = $this->database->prepare($query->insertInto('user')->key('pseudo', 'password', 'email')->value($pseudo,$password,$email)->toString(),'insert');
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
		switch($type)
		{
			case 'password' :
				$statementSearch = (new SelectQuery())->select('*')->from('user')->where("email = '". $arg."'")->toString();
				$account = $this->database->prepare($statementSearch,"select", "app\model\user", true);
				if ($account != false) {
					$token = tokenGenerator::strRandom();
					$idUser = $account['idUser'];
					$type = 1;
					break;
				}
				return false;
				break;
			case 'email' :
				$token = tokenGenerator::strRandom();
				$idUser = $arg;
				$type = 2;
				break;
		}
		$statement = (new InsertQuery())->insertInto('token')->key('idUser','token','idTokenCategory')->value($idUser,$token,2)->toString();
		if($this->database->prepare($statement,null,"insert",null,true))
		{
			return ($token);
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
		switch($function)
		{
			case 'password':
				$set = "password = '" . password_hash($password, PASSWORD_BCRYPT) . "'";
				break;
			case 'email' :
				$set = "isEmailChecked = true";
				break;
		}
		$statement = (new UpdateQuery())->update("user")->set($set)->where("idUser =" . $token['idUser'])->toString();
		if ($this->database->prepare($statement, "update")) {
			$this->database->prepare((new DeleteQuery())->delete('token')->where("idToken = ". $token['idToken'])->toString(),"delete");
			return true;
		}
		return false;
	}
}