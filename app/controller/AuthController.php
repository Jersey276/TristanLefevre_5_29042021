<?php

namespace app\controller;

use core\controller\AbstractController;
use core\request\requestManager;

use app\manager\UserManager;

/**
 * Controller of Authentification System
 * @author Tristan
 * @version 2
 */
class AuthController extends AbstractController
{
    /**
     * Show register form
     * @return TwigTemplate template of register Form with generated CSRF Token
     */
    public function registerForm()
    {
        return print_r($this->render('auth/registerForm', [
            'CSRFtoken' => (new UserManager())->getCSRFToken()]));
    }

    /**
     * register new user
     * @return TwigTemplate template of Message template with message on succes / register Form with error message and new CSRF Token in case of fail
     */
    public function register()
    {
        $response = (new UserManager('auth'))->register();
        if ($response['result']) {
            return print_r($this->render("message", $response['messageVar']));
        }
        return print_r($this->render('auth/registerForm', $response['errVar']));
    }

    /**
     * Show Login form
     * @return TwigTemplate template of Login Form with generated CSRF Token
     */
    public function loginForm()
    {
        return print_r(
            $this->render(
                'auth/loginForm',
                ['CSRFtoken' => (new UserManager('auth'))->getCSRFToken()]
            )
        );
    }

    /**
     * Ask manager to log in user and show result
     * @return header redirection to homePage
     * @return TwigTemplate template of login Form with error message and new CSRF Token in case of fail
     */
    public function login()
    {
        $response = (new UserManager('auth'))->login();
        if ($response['result']) {
            return header('Location:/');
        }
        return print_r($this->render("auth/loginForm", $response['errVar']));
    }

    /**
     * Show form when user click on 'forgot Password' link
     * @return TwigTemplate template of Forget Form with generated CSRF Token
     */
    public function forgotPasswordForm()
    {
        return print_r(
            $this->render(
                'auth/forgotPasswordForm',
                ['CSRFtoken' => (new UserManager())->getCSRFToken() ]
            )
        );
    }

    /**
     * ask manager to send mail and show result
     * @return TwigTemplate message page that report to user that a mail with link is send / forgot password form with error message and new CSRF Token in case of fail
     */
    public function forgotPassword()
    {
        $response = (new UserManager('auth'))->forgotPassword();
        if ($response['result']) {
            return print_r(
                $this->render(
                    "message",
                    $response['messageVar']
                )
            );
        }
        return print_r(
            $this->render(
                "auth/forgotPasswordForm",
                $response['errVar']
            )
        );
    }

    /**
     * ask manager validity of token and show changepassword Form if success
     * @param string token
     * @return TwigTemplate|function template of ChangePassword Form if token is valid / 404 error when token isn't valid
     */
    public function changePasswordForm($token)
    {
        $response = (new UserManager('auth'))->changePasswordForm($token);
        if ($response['result']) {
            return print_r($this->render('auth/changePasswordForm', $response['var']));
        }
        return $this->error404();
    }

    /**
     * Ask manager to change password. Send response depending of success
     * @param string token
     * @return TwigTemplate message when success, change password form with new CSRF Token when fail
     */
    public function changePassword($token)
    {
        $response = (new UserManager('auth'))->changePassword($token);
        if ($response['result']) {
            return print_r($this->render("message", $response['messageVar']));
        }
        return print_r($this->render("auth/changePasswordForm", $response['errVar']));
    }

    /**
     * Valid email of user when he click on link on sended mail
     * @param string token the user received on mail
     * @return TwigTemplate message with result of request
     */
    public function validEmail($token)
    {
        $response = (new UserManager('auth'))->validEmail($token);
        return print_r($this->render("message", $response));
    }

    /**
     * Log out user
     * @return header Redirect to homePage()
     */
    public function logout()
    {
        (new requestManager)->killSession();
        return header('Location:/');
    }
}
