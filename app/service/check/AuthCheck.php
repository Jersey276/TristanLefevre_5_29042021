<?php

namespace app\service\check;

use core\request\RequestManager;
use app\service\check\UserCheck;

/**
 *  class for post data verification about authentification system
 * 	@author Tristan
 * 	@version 1
 */
class AuthCheck
{
	/**
	 * Check data for register system. This check do:
	 * - check conformity of post data
	 * - check if password is the same as his conformation
	 * - verify if pseudo and email is used by other user
	 * @return array post data if success
	 * @return array error data if fail
	 */
	function registerCheck()
	{
		$post = (new RequestManager())->getPost([
			'pseudo' => 'string',
			'email' => 'string|email',
			'password' => 'string',
			'confirm-password' => 'string',
			'CSRFtoken' => 'token'
		]);
		if ($post != false)
		{
			if ($post['password'] == $post['confirm-password'])
			{
				$check = new UserCheck();
				if ($check->isPseudoUsed($post['pseudo']))
				{
					return [
						"err" => true,
						"data" => [
							"email" => $post['email'],
						],
						"errMessage" => "Ce pseudonyme à déja été utilisé"
					];
				}
				if ($check->isEmailUsed($post['email']))
				{
					return [
						"err" => true,
						"data" => [
							"pseudo"=> $post['pseudo'],
						],
						"errMessage" => "Cet adresse mail à déja été utilisé"
					];
				}
				return $post;


			}
			return [
				"err" => true,
				"data" => [
					"pseudo" => $post['pseudo'],
					"email" => $post['email']
				],
				"errMessage" => "Le mot de passe et sa confirmation ne sont pas identiques"
			];
		}
		return [
			"err" => true,
			"errMessage" => "un problème a eu lieu avec vos données, veuiller ressayer"
		];
		
	}

	/**
	 * Check post data for login system. This do:
	 * - check conformity of post data
	 * @return array post data if success
	 * @return array error data if fail
	 */
	function loginCheck()
	{
		$post = (new RequestManager())->getPost([
			"pseudo" => "string",
			"password" => "string",
			"CSRFtoken" => "token"
		]);
		if ($post != false)
		{
			return $post;
		}
		return [
			"err" => true,
			"errMessage" => "un problème a eu lieu avec vos données, veuiller ressayer"
		];
	}
	/**
	 * Check post data for password. This do:
	 * - check conformity of post data
	 * @return array post data if success
	 * @return array error data if fail
	 */
	function forgotPasswordCheck()
	{
		$post = (new requestManager())->getPost([
			'email' => 'string|email',
			'CSRFtoken' => 'token'
		]);
		if ($post != false)
		{
			return $post;
		}
		return [
			"err" => true,
			"errMessage" => "un problème a eu lieu avec vos données, veuiller ressayer"
		];
	}
	/**
	 * Check data for change password system. This check do:
	 * - check conformity of post data
	 * - check if password is the same as his conformation
	 * @return array post data if success
	 * @return array error data if fail
	 */
	function changePasswordCheck()
	{
		$post = (new requestManager())->getPost([
			'password' => 'string',
			'confirm-password' => 'string',
			'CSRFtoken' => 'token'
		]);
		if ($post != false)
		{
			if ($post['password'] == $post['confirm-password'])
			{

				return $post;
			}
			return [
				"err" => true,
				"errMessage" => "Le mot de passe et sa confirmation ne sont pas identiques"
			];
		}
		return [
			"err" => true,
			"errMessage" => "un problème a eu lieu avec vos données, veuiller ressayer"
		];
	}
}
?>
