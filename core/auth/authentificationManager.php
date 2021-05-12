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
		$statement = $query->select('user.pseudo','user.email','user.password','role.nameRole as role')->from('user')->leftjoin('role','role.idRole = user.idRole')->where("pseudo = '".$login."'")->toString();
		$account = $this->database->prepare($statement, null,"select", "user", true);
		if ( $login == $account['pseudo'] && password_verify($password, $account['password']))
		{
			$session = new requestManager();
			$session->session('pseudo', $account['pseudo']);
			$session->session('email', $account['email']);
			$session->session('role', $account['role']);
			return ['isConnected' => true];
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
				$querySearch = new SelectQuery();
				$statementSearch = $querySearch->select('*')->from('user')->where("email = '". $arg."'")->toString();
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
		$queryInsert = new InsertQuery();
		$statement = $queryInsert->insertInto('token')->key('idUser','token','idTokenType')->value($idUser,$token,2)->toString();
		if($this->database->prepare($statement,null,"insert",null,true))
		{
			return ($token);
		}
		return false;
	}

	public function resetPassword($token, $password)
	{
		$newPassword = password_hash($password, PASSWORD_BCRYPT);
		$statement = (new UpdateQuery())->update("user")->set("password = '" . $newPassword . "'")->where("idUser ='" . $token['idUser'] . "'")->toString();
		if($this->database->prepare($statement, null, "update", null)) {
			$deleteQuery = (new DeleteQuery())->delete('token')->where("idToken = ". $token['idToken'])->toString();
			$this->database->prepare($deleteQuery, null, "delete", null, true);
			return true;
		}
		return false;
	}
}