<?php

namespace app\model;

use core\model\AbstractModel;

class User extends AbstractModel
{
	private int $idUser;
	private string $pseudo;
	private string $email;
	private string $password;
	private string $role;
	private bool $isEmailChecked;

	
	//getter
	public function getidUser()
	{
		return $this->idUser;
	}
	public function getpseudo()
	{
		return $this->pseudo;
	}
	public function getemail()
	{
		return $this->email;
	}
	public function getpassword()
	{
		return $this->password;
	}
	public function getrole()
	{
		return $this->role;
	}
	public function getisEmailCheck()
	{
		return $this->isEmailCheck;
	}
	//Setter
	public function setidUser($idUser)
	{
		$this->idUser = $idUser;
	}
	public function setpseudo($pseudo)
	{
		$this->pseudo = $pseudo;
	}
	public function setemail($email)
	{
		$this->email = $email;
	}
	public function setpassword($password)
	{
		$this->password = $password;
	}
	public function setrole($role)
	{
		$this->role = $role;
	}
	public function setisEmailCheck($isEmailChecked)
	{
		$this->isEmailChecked = $isEmailChecked;
	}
}

?>