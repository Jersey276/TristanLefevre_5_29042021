<?php

namespace core\auth;

use App;
use core\database\InsertQuery;
use core\database\SelectQuery;
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
		if ( $login == $account['pseudo'] && password_verify($password, $account['password']) || $login == $account['email'] && password_verify($password, $account['password'])) {
			$session = new requestManager();
			$session->session('pseudo', $account['pseudo']);
			$session->session('email', $account['email']);
			$session->session('role', $account['role']);
			return ['isConnected' => true];
		}
		if ($session->session('try'))
		return [ 'isConnected' => false, 'message' => 'identifiant / mot de passe incorrect, veuiller ressayer'];
    }

	public function register($pseudo, $password, $email)
	{
		$password =  password_hash($password, PASSWORD_BCRYPT);
		$query = new InsertQuery();
		$this->database->prepare($query->insertInto('user')->key('pseudo', 'password', 'email')->value($pseudo,$password,$email)->toString(), null,'insert', null);
		return true;
	}
}