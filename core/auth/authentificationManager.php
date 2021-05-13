<?php

namespace core\auth;

use App;
use core\database\InsertQuery;
use core\database\SelectQuery;
use core\database\UpdateQuery;
use core\database\DeleteQuery;
use core\database\DatabaseManager;
use core\request\requestManager;
use \SessionHandler;

class authentificationManager
{
	private static $instance;
	private $database;
	
	private function __construct(databaseManager $database)
	{
		$this->database = $database;
	}
	
	public static function getInstance($database = null)
	{
		if(empty(self::$instance))
		{
			self::$instance = new authentificationManager($database);
		}
		return self::$instance;
	}

	public function login($login, $password) 
	{
		$query = new SelectQuery('select');
		$statement = $query->select('user.pseudo','user.email','user.password','role.nameRole as role','user.isEmailChecked')->from('user')->leftjoin('role','role.idRole = user.idRole')->where("pseudo = '".$login."'")->toString();
		$account = $this->database->prepare($statement, null,"select", "user", true);
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

	public function register($pseudo, $password, $email)
	{
		$password =  password_hash($password, PASSWORD_BCRYPT);
		$query = new InsertQuery();
		$awnser = $this->database->prepare($query->insertInto('user')->key('pseudo', 'password', 'email')->value($pseudo,$password,$email)->toString(), null,'insert', true, true);
		return $awnser;
	}

	public function askToken($type, $arg)
	{
		switch($type)
		{
			case 'password' :
				// Search account concerned with this email
				$statementSearch = (new SelectQuery())->select('*')->from('user')->where("email = '". $arg."'")->toString();
				$account = $this->database->prepare($statementSearch, null,"select", "user", true);
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
		$statement = (new InsertQuery())->insertInto('token')->key('idUser','token','idTokenType')->value($idUser,$token,2)->toString();
		if($this->database->prepare($statement,null,"insert",null,true))
		{
			return ($token);
		}
		return false;
	}

	public function useToken($token, $function, $password = null)
	{
		switch($function)
		{
			case 'changePassword':
				$set = "password = '" . password_hash($password, PASSWORD_BCRYPT) . "'";
				break;
			case 'validEmail' :
				$set = "isEmailChecked = true";
				break;
		}
		$statement = (new UpdateQuery())->update("user")->set($set)->where("idUser =" . $token['idUser'])->toString();
		if ($this->database->prepare($statement, null, "update", null)) {
			$this->database->prepare((new DeleteQuery())->delete('token')->where("idToken = ". $token['idToken'])->toString(),null,"delete",null,true);
			return true;
		}

	}
}