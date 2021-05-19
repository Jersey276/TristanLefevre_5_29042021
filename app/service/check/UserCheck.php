<?php

namespace app\service\check;

use App\app;
use core\database\SelectQuery;

/**
 *  class for verification of user data 
 * 	@author Tristan
 * 	@version 1
 */
class UserCheck
{
	/**
	 * Check data for register system. This check do:
	 * - check if user already use email
	 * @param string email to check
	 * @return bool result 
	 */
	function isEmailUsed($info)
	{
		$query = new SelectQuery;
		$statement = $query->select("*")->from('user')->where("email ='" . $info ."'")->toString();
		$check = (APP::getDB())->prepare($statement,"select","app\model\User");
		if ($check == [])
		{
			return false;
		}
		return true;
	}
	/**
	 * Check data for register system. This check do:
	 * - check if a user already use this pseudo
	 * @param string pseudo to check
	 * @return bool result 
	 */
	function isPseudoUsed($info)
	{
		$query = new SelectQuery;
		$statement = $query->select("*")->from('user')->where("pseudo ='" . $info . "'")->toString();
		$check = (APP::getDB())->prepare($statement,"select", 'app\model\User');
		if ($check == [])
		{
			return false;
		}
		return true;
	}
}
