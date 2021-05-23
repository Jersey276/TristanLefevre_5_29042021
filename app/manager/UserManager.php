<?php

namespace app\manager;

use core\request\RequestManager;
use app\service\check\AuthCheck;
use app\service\mail\UserMail;
use app\service\query\AuthQuery;

use core\auth\AuthentificationManager as AuthManager;
use app\App;

/**
 * Manager of all function about User class (Authentification / profil modification)
 * @author Tristan
 * @version 1
 */
class UserManager
{
    /**
     * AuthManager instance
     */
    private $auth;

    public function __construct()
    {
        $this->auth = AuthManager::getInstance(App::getDB());
    }

    /**
     * Check all data, register new user and send mail for confirm email
     * @return array array of result with all var for template
     */
    public function register()
    {
        $post = (new AuthCheck)->registerCheck();
        if (empty($post['err'])) {
            $awnser = $this->auth->register($post["pseudo"], $post["password"], $post["email"]);
            if ($awnser != false) {
                $token = $this->auth->askToken("email", $awnser);
                (new UserMail())->checkEmailMail($post['email'], $token);
                return [
                    "result" => true,
                    "messageVar" => [
                        "type" => 'success',
                        "message" =>  "un message à été envoyé pour valider votre adresse mail.",
                        "btnReturn" => "\login",
                        "btnMessage" => "Se Connecter"
                        ]
                    ];
            }
            $post['errMessage'] = "inscription echoué, veuiller ressayer plus tard ou contacter ";
        }

        return [
            "result" => false,
            "errVar" => [
                "message" => $post['errMessage'],
                "CSRFtoken" => $this->getCSRFToken()
            ]
        ];
    }

    /**
     * Check all data and verify if user can be logged
     * @return array array of result with all var for template
     */
    public function login()
    {
        $post = (new AuthCheck())->loginCheck();

        if (empty($post['err'])) {
            $awnser = $this->auth->login($post['pseudo'], $post["password"]);
            if ($awnser['isConnected']) {
                return [
                    "result" => true
                ];
            }
            $post['errMessage'] = $awnser['message'];
        }
        $errvar = [
            "message" => [
                "type" => "danger",
                "text" => $post['errMessage']
            ],
            "CSRFtoken" => $this->getCSRFToken()
        ];
        if (isset($post['data'])) {
            $errvar = array_merge($errvar, $post['data']);
        }
        return [
            "result" => false,
            "errVar" => $errvar
        ];
    }
    /**
     * Check Email and send mail with link for change password
     * @return array array of result with all var for template
     */
    public function forgotpassword()
    {
        $post = (new AuthCheck)->forgotPasswordCheck();
        if (empty($post['err'])) {
            $token = $this->auth->askToken("password", $post['email']);
            if ($token != false) {
                (new UserMail())->resetPassword($post['email'], $token);
                return [
                    'result' => true,
                    'messageVar' => [
                        'type' => 'warning',
                        'message' => 'un message à été envoyé pour changer votre mot de passe.',
                        'btnReturn' => '\login',
                        'btnMessage' => "se connecter"
                    ]
                ];
            }
        }
        return [
            'result' => false,
            'errVar' => [
                'message' => $post['errMessage'],
                'CSRFtoken' => $this->getCSRFToken()
            ]
        ];
    }

    /**
     * Verify data and change password of user
     * @param string token
     * @return array array of result with all var for template
     */
    public function changePassword($keyToken)
    {
        $post = (new AuthCheck())->changePasswordCheck();
        if (empty($post['err'])) {
            $query = new AuthQuery();
            $token = (App::getDB())->prepare(
				$query->getTokenQuery(),
				[ ":token" => $keyToken],
				"select",
				"app\model\token",
				true);
            if ($this->auth->useToken($token, "password", $post['password'])) {
                return [
                'result' => true,
                'messageVar' => [
                    'type' => 'success',
                    'message' => 'Le mot de passe à été mis à jour',
                    'btnReturn' => '\login',
                    'btnMessage' => "se connecter"
                    ]
                ];
            }
        }
        return [
            'result' => false,
            "errVar" => [
                "token" => $keyToken,
                "message" => $post['errMessage'],
                "CSRFtoken" => $this->getCSRFToken()
                ]
            ];
    }

    /**
     * Check token
     * @param string token
     * @return array array of result with all var for template
     */
    public function changePasswordForm($keyToken)
    {
        $token = (App::getDB())->prepare((new AuthQuery())->getTokenQuery(),[':token' => $keyToken], "select", "app\model\Token", true);
        if ($token != false) {
            return [
                'result' => true,
                'var' => [
                    'token' => $token['token'],
                    'CSRFtoken' => $this->getCSRFToken()
                ]
            ];
        }
        return [
            'result' => false
        ];
    }

    /**
     * use token to validate email
     * @return array array of result with all var for template
     */
    public function validEmail($keyToken)
    {
        $token = (App::getDB())->prepare((new AuthQuery())->getTokenQuery(),[':token' => $keyToken], "select", "token", true);
        if ($this->auth->useToken($token, "email")) {
            return [
                'type' => 'success',
                'message' => 'Votre adresse mail a été vérifié, vous pouvez vous connecter',
                'btnReturn' => '\login',
                'btnMessage' => "se connecter"
            ];
        }
        return [
            'type' => 'danger',
            'message' => 'une erreur à eu lieu lors de la validation de l\' adresse mail',
            'brnReturn' => '\\',
            'btnMessage' => "retour à l'acceuil"];
    }

    //generic Function
    /**
     * get new CSRF Token
     * @return string CSRF Token
     */
    public function getCSRFToken()
    {
        return (new requestManager())->newCSRFToken();
    }
}
