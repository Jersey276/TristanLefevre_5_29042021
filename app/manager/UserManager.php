<?php

namespace app\manager;

use app\service\check\AuthCheck;
use app\service\mail\UserMail;
use app\service\query\AuthQuery;

use core\auth\AuthentificationManager as AuthManager;
use app\App;
use app\model\User;
use app\service\check\UserCheck;
use app\service\query\UserQuery;
use core\auth\RoleChecker;
use core\manager\AbstractManager;

/**
 * Manager of all function about User class (Authentification / profil modification)
 * @author Tristan
 * @version 3
 */
class UserManager extends AbstractManager
{
    /**
     * @var AuthManager instance
     */
    private $auth;

    /**
     * @var AuthQuery authQuery
     */
    private $authQuery;

    /**
     * @var UserQuery userQuery
     */
    private $userQuery;

    /**
     * @var AuthCheck authCheck
     */
    private $authCheck;

    /**
     * @var UserCheck userCheck
     */
    private $userCheck;

    /**
     * @var UserMail userMail
     */
    private $userMail;

    /**
     * constructor
     * @param bool check if user if for Auth
     */
    public function __construct($function = null)
    {
        parent::__construct(App::getDB());
        switch ($function) {
            case ('auth'):
                $this->authQuery = new AuthQuery();
                $this->authCheck = new AuthCheck();
                break;
            case ('user'):
                $this->userQuery = new UserQuery();
                $this->userCheck = new UserCheck();
                break;
        }
        if ($function != null) {
            $this->userMail = new UserMail();
            $this->auth = AuthManager::getInstance($this->database);
        }
    }

    // Authentification System
    /**
     * Check all data, register new user and send mail for confirm email
     * @return array array of result with all var for template
     */
    public function register() : array
    {
        $post = $this->authCheck->registerCheck();
        if (empty($post['err'])) {
            $awnser = $this->auth->register(
                $post["pseudo"],
                $post["password"],
                $post["email"]
            );
            if ($awnser != false) {
                $token = $this->auth->askToken("email", $awnser);
                $this->userMail->checkEmailMail($post['email'], $token);
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
    public function login() : array
    {
        $post = $this->authCheck->loginCheck();

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
    public function forgotPassword() : array
    {
        $post = $this->authCheck->forgotPasswordCheck();
        if (empty($post['err'])) {
            $token = $this->auth->askToken("password", $post['email']);
            if ($token != false) {
                $this->userMail->resetPassword($post['email'], $token);
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
            $post['errMessage'] = "Un problème a eu lieu lors de l'envoi du message";
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
    public function changePassword($keyToken) : array
    {
        $post = $this->authCheck->changePasswordCheck();
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
    public function changePasswordForm($keyToken) : array
    {
        $token = $this->database->prepare(
            $this->authQuery->getTokenQuery(),
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
    public function validEmail($keyToken) : array
    {
        $token = $this->database->prepare(
            $this->authQuery->getTokenQuery(),
            [':token' => $keyToken],
            "select",
            "token",
            true
        );
        if ($token != false && $this->auth->useToken($token, "email")) {
            return [
                'type' => 'success',
                'message' => 'Votre adresse mail a été vérifié, 
                vous pouvez vous connecter',
                'btnReturn' => '\login',
                'btnMessage' => "se connecter"
            ];
        }
        return [
            'type' => 'danger',
            'message' => 'une erreur à eu lieu lors de la validation 
            de l\' adresse mail',
            'brnReturn' => '\\',
            'btnMessage' => "retour à l'acceuil"];
    }

    // user profile display/modification
    /**
     * get user profile
     * @param string Pseudo of user
     * @return array information with CSRF token
     */
    public function getUser($pseudo, $isAdmin = false) : array
    {
        $userQuery = $this->database->prepare(
            $this->userQuery
            ->getUserByPseudo(),
            [':pseudo' => $pseudo],
            "select",
            "app\model\user",
            true
        );
        if (!empty($userQuery)) {
            $user = (new User())->hydrate($userQuery);
            if ($user->getpseudo() != $this->request->session('pseudo')) {
                if (!$isAdmin) {
                    return ['profil' => false, "code" => 403];
                }
            }
            return [
                'profil' => $user ,
                'token' => $this->askNewCSRFLongToken('profil')
            ];
        }
        return ['profil' => false, "code" => 404];
    }

    /**
     * check user role, modify user mail, ask email confirm token
     * and send mail of email confirmation
     * @param string pseudo of user
     * @return array result with message
     */
    public function modifyEmail($pseudo) : array
    {
        if (RoleChecker::role('Admin') ||
            RoleChecker::role('User') &&
            $this->request->session('pseudo') == $pseudo) {
            $user = (new User())->hydrate(
                $this->database->prepare(
                    $this->userQuery->getUserByPseudo(),
                    [':pseudo' => $pseudo ],
                    'select',
                    'app\model\user',
                    true
                )
            );
            $post = $this->userCheck->changeEmailCheck();
            if ($post != false) {
                $this->database->prepare(
                    $this->userQuery->changeEmail(),
                    [':email' => $post['email'], ':pseudo' => $pseudo],
                    'update'
                );
                if ($post['email'] != $user->getemail()) {
                    $token = $this->auth->askToken('email', $user->getidUser());
                    $this->userMail->checkEmailMail($post['email'], $token, true);
                    return [
                'result' => true,
                'message' => 'votre adresse mail a été change 
                et doit être vérifié. un mail vous a été envoyé'
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
        'message' => "Bien essayé, mais l'utilisateur que vous 
        voulez modifier l'adresse mail n'est pas vous"
    ];
    }


    /**
     * change password and notify user of change
     * @param string pseudo of user
     * @return array result with message
     */
    public function changeProfilPassword(string $pseudo) : array
    {
        if (RoleChecker::role('Admin') ||
            RoleChecker::role('User') &&
            $this->request->session('pseudo') == $pseudo) {
            $post = $this->userCheck->changePasswordCheck();
            if (empty($post['result'])) {
                $password = password_hash($post['password'], PASSWORD_BCRYPT);
                $this->database->prepare(
                    $this->userQuery->changePassword(),
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
            'message' => "Bien essayé, mais l'utilisateur que vous 
            voulez modifier le mot de passe n'est pas vous"
        ];
    }

    /**
     * check data, remove profil and kill session
     * @param string pseudo of user
     * @return array result with message
     */
    public function removeProfil(string $pseudo) : array
    {
        if (RoleChecker::role('Admin') ||
            RoleChecker::role('User') &&
            $this->request->session('pseudo') == $pseudo) {
            $post = $this->userCheck->RemoveProfilCheck();
            if (empty($post['result']) && isset($post['pseudoConfirm'])) {
                $this->database->prepare(
                    $this->userQuery->deleteProfil(),
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
                'message' => "Bien essayé, mais l'utilisateur que vous 
                voulez supprimer n'est pas vous"
            ];
    }

    /**
     * get List of user
     * @return array users list
     */
    public function getListUser() : array
    {
        $users = $this->database->prepare(
            $this->userQuery->getListUser(),
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
    public function changeRole(string $pseudo) : array
    {
        if (RoleChecker::role('Admin')) {
            $post = $this->userCheck->changeRoleCheck();
            if (empty($post['result'])) {
                $this->database->prepare(
                    $this->userQuery->changeRole(),
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
    public function removeAdminProfil(string $pseudo) : array
    {
        if (RoleChecker::role('Admin')) {
            $user = (new User())->hydrate(
                $this->database->prepare(
                    $this->userQuery
                    ->getUserByPseudo(),
                    [':pseudo' => $pseudo],
                    "select",
                    "app\model\user",
                    true
                )
            );

            $post = $this->userCheck->RemoveProfilAdminCheck();
            if (empty($post['result'])) {
                $this->database->prepare(
                    $this->userQuery->deleteProfil(),
                    [':pseudo' => $post['pseudo']],
                    'delete'
                );
                $this->userMail->reportBan($user->getemail(), $post['message']);
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
    public function getCSRFToken() : string
    {
        return $this->request->newCSRFToken();
    }
}
