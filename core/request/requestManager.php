<?php
namespace core\request;

class RequestManager
{
	/**
	 * get all post variable
	 * @param array list of all conditions about a post element. each conditions is build like this : ["name_var" => "conditions1|conditions2"]
	 * @return array of all post variable checked and valided
	 * @return false if a var is problematic 
	 */
	function getPost($conditionsForm)
	{
		$posts = filter_input_array(INPUT_POST);
		foreach (array_keys($posts) as $postKey)
		{
			if (!array_key_exists($postKey, $conditionsForm)) {
				return false;
			}
			$conditions = explode("|", $conditionsForm[$postKey]);
			$tempPost = $posts[$postKey];
			foreach($conditions as $condition) {
				if($condition == "optionnal" && $posts[$postKey] = "") {
					break;
				}
				$tempPost = $this->checkAndPreparePost($posts[$postKey],$condition);
				if (!$tempPost)
				{
					return false;
				}
			}
			$posts[$postKey] = $tempPost;
		}
		return $posts;
	}
	/**
	 * Verify validity of a data and prepare it
	 * @param mixed variable to check
	 * @param string condition use to check variable
	 */
	private function checkAndPreparePost($data, $conditions)
	{
		switch ($conditions)
		{
			//check email and clean it
			case "email" :
				if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
					return filter_var($data, FILTER_SANITIZE_EMAIL);
				}
				return false;
				break;
			case "string" :
				return htmlentities($data, ENT_QUOTES);
				break;
			case "token" :
				return $this->checkCSRFToken($data, 600);
				break;
		}
	}
	/**
	 * get/set Session Variable
	 * @param string session variable name
	 * @param mixed value to use on this session variable
	 * @return mixed session variable
	 */
	function Session($key, $val = null)
	{
		if (isset($val))
		{
			$_SESSION[$key] = $val;
		}
		return filter_var($_SESSION[$key]);
	}
	/**
	 * Verify if this session variable exist
	 * @return bool result of array key exists
	 */
	function isSetSession($key)
	{
		return array_key_exists($key, $_SESSION);
	}
	/**
	 * kill session
	 * @return bool result of session destroy
	 */
	function killSession()
	{
		return session_destroy();
	}
	/**
	 * Generate and assign new CSRF Token
	 * @return string token
	 */
	function newCSRFToken()
	{
		$token = uniqid(rand(), true);
		$this->session('token', $token);
		$this->session('token_time', time());
		return $token;
	}
	/**
	 * Verify CSRF Token
	 * @param string csrf token
	 * @param int time for validity
	 * @return bool check if CSRF token is set, valid and match to 
	 */
	function checkCSRFToken($postToken, $time)
	{
		if($this->session('token') != null && $this->session('token_time') != null && isset($postToken))
			if($this->session('token') == $postToken)
				if($this->session('token_time') >= (time() - $time))
					return true;
		return false;
	}

	/**
	 * Get user Ip Address
	 * @return string ip adress of user
	 */
	function getIpAddr(){
		if (!empty(filter_input(INPUT_SERVER,'HTTP_CLIENT_IP')))
		{
			$ipAddr=filter_input(INPUT_SERVER,'HTTP_CLIENT_IP');
		}
			elseif (!empty(filter_input(INPUT_SERVER,'HTTP_X_FORWARDED_FOR')))
		{
			$ipAddr=filter_input(INPUT_SERVER,'HTTP_X_FORWARDED_FOR');
		}
			else
		{
			$ipAddr= filter_input(INPUT_SERVER,'REMOTE_ADDR');
		}
		return $ipAddr;
		}

}