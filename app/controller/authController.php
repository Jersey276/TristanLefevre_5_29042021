<?php

namespace app\controller;

use core\controller\AbstractController;
use core\request\requestManager;
use core\auth\authentificationManager;
use core\mail\mailManager;
use core\database\databaseManager;
use core\database\selectQuery;
use app\app;

class authController extends AbstractController{
	public function registerForm()
	{
		print_r($this->render('auth/registerForm',['CSRFToken' => (new requestManager())->newCSRFToken()]));
	}
	public function register()
	{
		$post = (new requestManager())->getPost([
			'pseudo' => 'string',
			'email' => 'string|email',
			'password' => 'string',
			'confirm-password' => 'string',
			'CSRFtoken' => 'string|token'
		]);
		if ($post == false)
		{
			return print_r($this->render('auth/registerForm', [ 
				'pseudo' => $post['pseudo'], 
				'email' => $post['email'], 
				'message' => "un problème a eu lieu avec vos données, veuiller ressayer",
				'CSRFtoken' => (new requestManager())->newCSRFToken()
				]));
		}
		if ($post['password'] != $post['confirm-password'])
		{
			return print_r($this->render('auth/registerForm', [ 
				'pseudo' => $post['pseudo'], 
				'email' => $post['email'], 
				'message' => "le mot de passe et sa confirmation ne sont pas identiques",
				'CSRFtoken' => (new requestManager())->newCSRFToken()
				]));
		}
		$database = new databaseManager();
		$auth = authentificationManager::getInstance($database);
		$awnser = $auth->register($post["pseudo"], $post["password"], $post["email"]);
		
		if ($awnser != false)
		{
			$user = $database->prepare((new SelectQuery())->select('idUser')->from('user')->where("user.pseudo = '".$post['pseudo']."'")->toString(),null,"select","user",true);
			$token = $auth->askToken("email",$user['idUser']);
			if ($token != false)
			{
				mailManager::sendmail($post['email'],"nouveau mot de passe","bienvenue sur "  . filter_input(INPUT_SERVER, 'SERVER_NAME') .". /r/n voici un lien pour valider votre email : <a href='" . filter_input(INPUT_SERVER, 'SERVER_NAME') .'/login/'. $token . "'>changer mon mot de passe<a>");
			}
			return print_r($this->render("message", ['type' => 'success', 'message' => 'un message à été envoyé pour valider votre adresse mail.', 'btnReturn' => '\login', 'btnMessage' => "se connecter"]));
		}

	}
	public function loginForm()
	{
		return print_r($this->render('auth/loginForm',['CSRFToken' => (new requestManager)->newCSRFToken()]));
	}
	public function login()
	{
		$post = (new requestManager())->getPost([
			"pseudo" => "string",
			"password" => "string",
			"CSRFtoken" => "string|token"
		]);
		if($post == false)
		{
			return print_r($this->render('auth/loginForm', [
				'message' => [ "type" => "danger", "text" => "un problème a eu lieu avec vos données, veuiller ressayer"], 'CSRFtoken' => (new requestManager())->newCSRFToken()
			]));
		}
		$auth = authentificationManager::getInstance(new databaseManager());
		$awnser = $auth->login($post["pseudo"], $post["password"]);
		if($awnser['isConnected']) {
			return header('Location:' . "/");	
		}
		return print_r($this->render("auth/loginForm",['message' => ['type' => 'danger', 'text' => $awnser['message'], 'CSRFtoken' => (new requestManager())->newCSRFToken()]]));
	}
	public function forgotPasswordForm()
	{
		$CSRFtoken = (new requestManager)->newCSRFToken();
		return print_r($this->render('auth/forgotPasswordForm', ['CSRFtoken' => $CSRFtoken ]));
	}
	public function forgotPassword()
	{
		$post = (new requestManager())->getPost([
			'email' => 'string|email',
			'CSRFtoken' => 'string|token'

		]);
		$auth = authentificationManager::getInstance(new databaseManager());
		if($post == false)
		{
			return print_r($this->render("auth/forgotPasswordForm",['message' => 'un problème a eu lieu avec vos données, veuiller ressayer', 'CSRFtoken' => (new requestManager())->newCSRFToken()]));
		}
		$token = $auth->askToken("password",$post['email']);
		if ($token != false)
		{
			mailManager::sendmail($post['email'],"nouveau mot de passe","Vous avez demandé de changer le mot de passe, voici un lien pour le faire : <a href='" . filter_input(INPUT_SERVER, 'SERVER_NAME').'/forgotPassword/'. $token . "'>changer mon mot de passe<a>");
		}
		return print_r($this->render("message", ['type' => 'warning', 'message' => 'un message à été envoyé pour changer votre mot de passe.', 'btnReturn' => '\login', 'btnMessage' => "se connecter"]));
	}
	public function changePasswordForm($token)
	{
		$database = new databaseManager();
		$token = $database->prepare((new SelectQuery())->select('*')->from('token')->where("token = '" . $token . "'")->toString(), null,"select","token",true);
		if ($token != false)
		{
			return print_r($this->render('auth/changePasswordForm',['token' => $token['token'], 'CSRFToken' => (new requestManager)->newCSRFToken()]));
		}
		return header( filter_input(INPUT_SERVER, "SERVER_PROTOCOL") . ' 404 Not Found');
	}
	public function changePassword($token)
	{
		$database = new databaseManager();
		$auth = authentificationManager::getInstance($database);
		$post = (new requestManager())->getPost([
			'password' => 'string',
			'confirm-password' => 'string',
			'CSRFtoken' => 'string|token'
		]);
		if ($post == false)
		{
		return print_r($this->render('auth/registerForm', [ 
			'pseudo' => $post['pseudo'], 
			'email' => $post['email'], 
			'message' => "le mot de passe et sa confirmation ne sont pas identiques",
			'token' => (new requestManager())->newCSRFToken()
				]));
		}
		if ($post['password'] != $post['confirm-password']) {
			return print_r($this->render('auth/registerForm', [ 
				'pseudo' => $post['pseudo'], 
				'email' => $post['email'], 
				'message' => "le mot de passe et sa confirmation ne sont pas identiques",
				'CSRFtoken' => (new requestManager())->newCSRFToken()
				]));
		}
		$token = $database->prepare((new SelectQuery())->select('*')->from('token')->where("token = '" . $token . "'")->toString(), null,"select","token",true);
		if ($auth->useToken($token, "changePassword", $post['password']))
		{
			return print_r($this->render("message", ['type' => 'success', 'message' => 'Le mot de passe à été mis à jour', 'btnReturn' => '\login', 'btnMessage' => "se connecter"]));
		}
		return print_r($this->render("message",['type' => 'danger', 'message' => 'une erreur à eu lieu lors de la modification dumot de passe']));
	}
	public function validEmail($token)
	{
		$database = new databaseManager();
		$auth = authentificationManager::getInstance($database);
		$token = $database->prepare((new SelectQuery())->select('*')->from('token')->where("token = '" . $token . "'")->toString(), null,"select","token",true);
		if ($auth->useToken($token, "validEmail"))
		{
			return print_r($this->render("message", ['type' => 'success', 'message' => 'Votre adresse mail a été vérifié, vous pouvez vous connecter', 'btnReturn' => '\login', 'btnMessage' => "se connecter"]));
		}
		return print_r($this->render("message",['type' => 'danger', 'message' => 'une erreur à eu lieu lors de la validation de l\' adresse mail', 'brnReturn' => '\\', 'btnMessage' => "retour à l'acceuil"]));
	}
	public function logout()
	{
		(new requestManager)->killSession();
		return header('Location:/');
	}
}