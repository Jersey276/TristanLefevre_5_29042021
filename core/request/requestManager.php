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
    public function getPost($conditionsForm)
    {
        $posts = filter_input_array(INPUT_POST);
        foreach (array_keys($posts) as $postKey) {
            if (!array_key_exists($postKey, $conditionsForm)) {
                return false;
            }
            $conditions = explode("|", $conditionsForm[$postKey]);
            $tempPost = $posts[$postKey];
            foreach ($conditions as $condition) {
                if ($condition == "optionnal" && $posts[$postKey] = "") {
                    break;
                }
                $tempPost = $this->checkAndPreparePost($posts[$postKey], $condition);
                if (!$tempPost) {
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
        switch ($conditions) {
            //check email and clean it
            case "email":
                if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
                    return filter_var($data, FILTER_SANITIZE_EMAIL);
                }
                return false;
                break;
            //check int and clean it
            case "int":
                if (filter_var($data, FILTER_VALIDATE_INT)) {
                    return filter_var($data, FILTER_SANITIZE_INT);
                }
                return false;
                break;
            //check long CSRF token and remove it after use
            case "longToken":
                return $this->checkCSRFLongToken(filter_input(INPUT_POST, 'nameToken'), $data, 1300);
                break;
            //check return string without html, js and sql effect
            case "string":
                return htmlentities($data, ENT_QUOTES);
                break;
            //check CSRF token
            case "token":
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
    public function Session($key, $val = null)
    {
        if (isset($val)) {
            $_SESSION[$key] = $val;
        }
        return filter_var($_SESSION[$key]);
    }
    /**
     * Verify if this session variable exist
     * @return bool result of array key exists
     */
    public function isSetSession($key)
    {
        return array_key_exists($key, $_SESSION);
    }
	/**
	 * unset a session var
	 * @return bool result of unset
	 */
	public function unsetSession($varName)
	{
		unset($_SESSION[$varName]);
	}
    /**
     * kill session
     * @return bool result of session destroy
     */
    public function killSession()
    {
        return session_destroy();
    }
    /**
     * Generate and assign new CSRF Token
     * @return string token
     */
    public function newCSRFToken()
    {
        $token = uniqid(rand(), true);
        $this->session('token', $token);
        $this->session('token_time', time());
        return $token;
    }
    /**
     * Generate and assign new CSRF long Token
     * @param string function of this token
     * @return array name of token and token key
     */
    public function newCSRFLongToken($function)
    {
        $token = uniqid(rand(), true);
        $time = time();
        $nameToken = $function ."-". $time . '-token';
        $this->session($nameToken, ['token' => $token, 'token_time' => $time]);
        return ["nameToken" => $nameToken, "token" => $token ];
    }
    /**
     * Verify CSRF Token
     * @param string csrf token
     * @param int time for validity
     * @return bool check if CSRF token is set and valid
     */
    public function checkCSRFToken($postToken, $time)
    {
        if ($this->session('token') != null && $this->session('token_time') != null && isset($postToken)) {
            if ($this->session('token') == $postToken) {
                if ($this->session('token_time') >= (time() - $time)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Verify Long CSRF Token
     * @param string name of token
     * @param string csrf token
     * @param int time of validity
     * @return bool check if CSRF is set and valid
     */
    public function checkCSRFLongToken($nameToken, $postToken, $time)
    {
        if ($this->isSetSession($nameToken)) {
            $token = $this->session($nameToken);
            if ($token['token_time'] != null && isset($postToken)) {
                if ($token['token'] == $postToken) {
                    if ($token['token_time'] >= time() - $time) {
                        $this->unsetSession($nameToken);
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get user Ip Address
     * @return string ip adress of user
     */
    public function getIpAddr()
    {
        if (!empty(filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP'))) {
            $ipAddr=filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        } elseif (!empty(filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR'))) {
            $ipAddr=filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } else {
            $ipAddr= filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        }
        return $ipAddr;
    }
}
