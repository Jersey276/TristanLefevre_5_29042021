<?php

namespace core\auth;

use App;
use core\database\InsertQuery;
use core\database\SelectQuery;
use core\database\DatabaseManager;

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
		var_dump($password);
		$query = new SelectQuery('select');
		$statement = $query->select('user.pseudo','user.email','user.password','role.nameRole as role')->from('user')->leftjoin('role','role.idRole = user.idRole')->where("pseudo = '".$login."'")->toString();
		$account = $this->database->prepare($statement, null,"select", "user", true);
		if ( $login == $account['pseudo'] && password_verify($password, $account['password']) || $login == $account['email'] && password_verify($password, $account['password'])) {
			$_SESSION['pseudo'] =  $account['pseudo'];
			$_SESSION['email'] = $account['email'];
			$_SESSION['role'] = $account['role'];
			var_dump($_SESSION);
			return true;
		}
		return false;
    }
    /**
     * 
     */
	public function register($pseudo, $password, $email)
	{
		$password =  password_hash($password, PASSWORD_BCRYPT);
		$query = new InsertQuery();
		$this->database->prepare($query->insertInto('user')->key('pseudo', 'password', 'email')->value($pseudo,$password,$email)->toString(), null,'insert', null);
		return true;
	}
}