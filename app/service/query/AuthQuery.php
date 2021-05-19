<?php

namespace app\service\query;

use core\database\SelectQuery;

class AuthQuery
{
	function getTokenQuery($token)
	{
		return (new SelectQuery())->select('*')
		->from('token')
		->where("token = '" . $token . "'")
		->toString();
	}
}