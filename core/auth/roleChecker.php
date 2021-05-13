<?php

namespace core\auth;

use core\request\requestManager;

class roleChecker
{
	private const RoleRule = [ 
		'User' =>  ['User','Writer','Admin'],
		'Writer' => ['Writer', 'Admin'],
		'Admin' => ['Admin']
	];

	static function role($role)
	{
		$request = new requestManager();
		if ($request->isSetSession('role')) {
			if (in_array($request->session('role'),self::RoleRule[$role]))
			{
				return true;
			}
		}
		return false;
	}
	static function guest()
	{
		$request = new requestManager();
		if ($request->isSetSession('role')) {
			return false;
		}
		return true;
	}
}

?>