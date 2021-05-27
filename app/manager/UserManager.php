<?php

namespace app\manager;

use core\request\RequestManager;
use app\service\check\AuthCheck;
use app\service\mail\UserMail;
use app\service\query\AuthQuery;

use core\auth\AuthentificationManager as AuthManager;
use app\App;
use app\model\User;
use app\service\check\UserCheck;
use app\service\query\UserQuery;
use core\auth\roleChecker;
use core\database\DeleteQuery;
use core\manager\AbstractManager;

/**
 * Manager of all function about User class (Authentification / profil modification)
 * @author Tristan
 * @version 1
 */
class UserManager extends AbstractManager
{
    /**
     * AuthManager instance
     */
    private $auth;

    public function __construct()
    {
        parent::__construct(App::getDB());
        $this->auth = AuthManager::getInstance($this->database);
    }
    // Authentification System
    /**
     * Check all data, register new user and send mail for confirm email
     * @return array array of result with all var for template
     */
    public function register()
    {
        $post = (new AuthCheck)->registerCheck();
        if (empty($post['err'])) {
            $awnser = $this->auth->register(
                $post["pseudo"],
                $post["password"],
                $post["email"]
            );
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
            $token = $this->database->prepare(
                $query->getTokenQuery(),
                [ ":token" => $keyToken],
                "select",
                "app\model\token",
                true
            );
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
        $token = $this->database->prepare(
            (new AuthQuery())->getTokenQuery(),
            [':token' => $keyToken],
            "select",
            "app\model\Token",
            true
        );
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
        $token = $this->database->prepare(
            (new AuthQuery())->getTokenQuery(),
            [':token' => $keyToken],
            "select",
            "token",
            true
        );
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

    // user profile display/modification
    /**
     * get user profile
     * @param string Pseudo of user
     * @return array information with CSRF token
     */
    public function getUser($pseudo)
    {
        $user = (new User())->hydrate(
            $this->database->prepare(
                (new UserQuery())
                ->getUserByPseudo(),
                [':pseudo' => $pseudo],
                "select",
                "app\model\user",
                true
            )
        );
        return ['profil' => $user , 'token' => $this->askNewCSRFLongToken('profil')];
    }

    /**
     * check date, modify user mail, ask email confirm token and send mail of email confirmation
     * @param string pseudo of user
     * @return array result with message
     */
    public function modifyEmail($pseudo)
    {
        if (roleChecker::role('Admin') ||
            roleChecker::role('User') &&
            $this->request->session('pseudo') == $pseudo) {
            $user = (new User())->hydrate(
                $this->database->prepare(
                    (new UserQuery())->getUserByPseudo(),
                    [':pseudo' => $pseudo ],
                    'select',
                    'app\model\user',
                    true
                )
            );
            $post = (new UserCheck())->changeEmailCheck();
            if ($post != false) {
                $this->database->prepare(
                    (new UserQuery())->changeEmail(),
                    [':email' => $post['email'], ':pseudo' => $pseudo],
                    'update'
                );
                if ($post['email'] != $user->getemail()) {
                    $token = $this->auth->askToken('email', $user->getidUser());
                    (new UserMail())->checkEmailMail($post['email'], $token, true);
                    return [
                'result' => true,
                'message' => 'votre adresse mail a été change et doit être vérifié. un mail vous a été envoyé'
            ];
                }
                return [
                'result' => true,
                'message' => "L'adresse mail n'a pas changé"
            ];
            }
            return [
            'result' => false,
            'message' => 'Une erreur à eu lieu, veuiller recommencer'
        ];
        }
        return [
        'result' => false,
        'message' => "Bien essayé, mais l'utilisateur que vous voulez modifier l'adresse mail n'est pas vous"
    ];
    }


    /**
     * change password and notify user of change
     * @param string pseudo of user
     * @return array result with message
     */
    public function changeProfilPassword($pseudo)
    {
        if (roleChecker::role('Admin') ||
            roleChecker::role('User') &&
            $this->request->session('pseudo') == $pseudo) {
            $post = (new UserCheck())->changePasswordCheck();
            if (empty($post['result'])) {
                $password = password_hash($post['password'], PASSWORD_BCRYPT);
                $this->database->prepare(
                    (new UserQuery())->changePassword(),
                    [':password' => $password, ':pseudo' => $pseudo],
                    'update'
                );
                return [
                    'result' => true,
                    'message' => "Le mot de passe à bien été mis à jour"
                ];
            }
            return [
                'result' => false,
                'message' => $post['message']
            ];
        }
        return [
            'result' => false,
            'message' => "Bien essayé, mais l'utilisateur que vous voulez modifier le mot de passe n'est pas vous"
        ];
    }

    /**
     * check data, remove profil and kill session
     * @param string pseudo of user
     * @return array result with message
     */
    public function removeProfil($pseudo)
    {
        if (roleChecker::role('Admin') ||
            roleChecker::role('User') &&
            $this->request->session('pseudo') == $pseudo) {
            $post = (new UserCheck())->RemoveProfilCheck();
            if (empty($post['result'])) {
                $this->database->prepare(
                    (new UserQuery())->deleteProfil(),
                    [':pseudo' => $post['pseudoConfirm']],
                    'delete'
                );
                $this->request->killSession();
                return [
                    'result' => true
                ];
            }
            return [
                    'result' => false,
                    'message' => $post['message']
                ];
        }
        return [
                'result' => false,
                'message' => "Bien essayé, mais l'utilisateur que vous voulez supprimer n'est pas vous"
            ];
    }

    /**
     * get List of user
     * @return array users list
     */
    public function getListUser()
    {
        $users = $this->database->prepare(
            (new UserQuery())->getListUser(),
            [],
            'select',
            'app\model\User'
        );
        return [ 'users' => $users ];
    }

    /**
     * check data and modify userRole
     * @param string pseudo of user
     * @return array result with message
     */
    public function changeRole($pseudo)
    {
        if (roleChecker::role('Admin')) {
            $post = (new UserCheck())->changeRoleCheck();
            if (empty($post['result'])) {
                $this->database->prepare(
                    (new UserQuery())->changeRole(),
                    [':idRole' => $post['role'], ':pseudo' => $pseudo],
                    'update'
                );
                return [
                'result' => true,
                'message' => "Le Role à bien été mis à jour"
            ];
            }
            return [
            'result' => false,
            'message' => $post['message']
        ];
        }
        return [
        'result' => false,
        'message' => "Bien essayé, mais vous n'avez pas les droits pour changer ça"
        ];
    }

    /**
     * check data, remove User profil and send message for report ban to user
     * @param string pseudo of user
     * @return array result with message
     */
    public function removeAdminProfil($pseudo)
    {
        if (roleChecker::role('Admin')) {
            $user = (new User())->hydrate(
                $this->database->prepare(
                    (new UserQuery())
                    ->getUserByPseudo(),
                    [':pseudo' => $pseudo],
                    "select",
                    "app\model\user",
                    true
                )
            );

            $post = (new UserCheck())->RemoveProfilAdminCheck();
            if (empty($post['result'])) {
                $this->database->prepare(
                    (new UserQuery())->deleteProfil(),
                    [':pseudo' => $post['pseudo']],
                    'delete'
                );
                (new UserMail())->reportBan($user->getemail(), $post['message']);
                return [
                    'result' => true
                ];
            }
            return [
                    'result' => false,
                    'message' => $post['message']
                ];
        }
        return [
                'result' => false,
                'message' => "Bien essayé, mais vous n'avez pas les droits"
            ];
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
