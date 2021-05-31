<?php

namespace app\model;

use core\model\AbstractModel;

class Token extends AbstractModel
{
	private $idToken;
	private	$idUser;
	private $token;
	private $tokenType;
	private $createdAt;

	//complementary function (empty)
	
	//getter

	public function getidToken()
	{
		return $this->idToken;
	}
	public function getidUser()
	{
		return $this->idUser;
	}
	public function gettoken()
	{
		return $this->token;
	}
	public function gettokenType()
	{
		return $this->tokenType;
	}
	public function getcreatedAt()
	{
		return $this->createdAt;
	}
	
	//setter
	
	public function setidToken($idToken)
	{
		$this->idToken = $idToken;
	}
	public function setidUser($idUser)
	{
		$this->idUser = $idUser;
	}
	public function settoken($token)
	{
		$this->token = $token;
	}
	public function settokenType($tokenType)
	{
		$this->tokenType = $tokenType;
	}
	public function setcreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;
	}
}