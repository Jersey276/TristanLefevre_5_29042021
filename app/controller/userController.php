<?php

namespace app\controller;

use core\controller\AbstractController;
use core\request\requestManager;
use core\auth\authentificationManager;
use core\mail\mailManager;
use core\database\databaseManager;
use app\app;

class userController extends AbstractController{
	public function registerForm()
	{
		print_r($this->render('auth/registerForm'));
	}
	public function register()
	{
		$request = new requestManager();
		$post = $request->getPost([
			'pseudo' => 'string',
			'email' => 'string|email',
			'password' => 'string',
			'confirm-password' => 'string' 
		]);
		if ($post == false) {
			print_r($this->render('auth/registerForm', [ 
				'pseudo' => $post['pseudo'], 
				'email' => $post['email'], 
				'password' => $post['password'],
				'confirm' => $post['confirm-password'],
				'message' => "un problème a eu lieu avec vos données, veuiller ressayer"
				]));
			exit;
		}
		if ($post['password'] != $post['confirm-password']) {
			print_r($this->render('auth/registerForm', [ 
				'pseudo' => $post['pseudo'], 
				'email' => $post['email'], 
				'password' => $post['password'],
				'confirm' => $post['confirm-password'],
				'message' => "le mot de passe et sa confirmation ne sont pas identiques"
				]));
			exit;
		}
		$dbConnexion = APP::getDBConnector();
		$auth = authentificationManager::getInstance(new databaseManager($dbConnexion['db_host'], $dbConnexion['db_base'],$dbConnexion["db_user"],$dbConnexion["db_pass"]));
		if ($auth->register($post["pseudo"], $post["password"], $post["email"]))
		{

			print_r($this->render("message", ['type' => 'success', 'message' => 'un message à été envoyé pour valider votre adresse mail.', 'btnReturn' => '\login', 'btnMessage' => "se connecter"]));
		}

	}
	public function loginForm()
	{
		print_r($this->render('auth/loginForm'));
	}
	public function login()
	{
		$request = new requestManager();
		$post = $request->getPost([
			"pseudo" => "string",
			"password" => "string"
		]);
		if($post == false)
		{
			print_r($this->render('auth/loginForm', ['message' => "un problème a eu lieu avec vos données, veuiller ressayer"]));
			exit;
		}
		$dbConnexion = APP::getDBConnector();
		$auth = authentificationManager::getInstance(new databaseManager($dbConnexion['db_host'], $dbConnexion['db_base'],$dbConnexion["db_user"],$dbConnexion["db_pass"]));
		if ($auth->login($post["pseudo"], $post["password"])) {
			print_r ($this->render("home",[]));	
		}
		print_r("false");
	}

}