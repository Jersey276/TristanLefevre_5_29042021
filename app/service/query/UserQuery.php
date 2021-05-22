<?php

namespace app\service\query;

use core\database\SelectQuery;

class UserQuery
{
	function getUserById()
	{
		return (new SelectQuery())->select("*")->from("user")->where("user.idUser = :id")->toString();
	}

	function getUserByPseudo()
	{
		return (new SelectQuery())->select("*")->from("user")->where("user.pseudo = :pseudo")->toString();
	}
}