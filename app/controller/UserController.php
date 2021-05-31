<?php

namespace app\controller;

use app\manager\UserManager;
use app\model\User;
use core\controller\AbstractController;

class UserController extends AbstractController
{
    
    /**
     * ask data of user and show them on UserProfilForm
     * @param string pseudo of user
     * @param array message and type of alert for bootstrap
     * @return TwigTemplate|function template of user profil form, or error
     */
    public function modifyProfilForm($pseudo, $message = null)
    {
        $response = (new UserManager('user'))->getUser($pseudo);
        if ($response['profil'] != false) {
            if (isset($message)) {
                $response['message'] = $message;
            }
            return print_r(
                $this->render(
                    'user\UserProfilForm',
                    $response
                )
            );
        }
        if ($response['code'] == 404) {
            return $this->error404();
        }
        return $this->error403();
    }
    /**
     * Ask to modify Email, collect and send result message
     * @param string pseudo of user
     * @param bool if this function ask change from admin pages
     * @return function ask modifyProfilForm/adminModifyProfilForm with message
     */
    public function changeEmail($pseudo, $isAdmin = false)
    {
        $response = (new UserManager('user'))->modifyEmail($pseudo);
        $message = ['message' => $response['message']];
        if ($response['result']) {
            $message['type'] = 'warning';
        } else {
            $message['type'] = 'danger';
        }
        if ($isAdmin) {
            return $this->adminModifyProfilForm($pseudo, $message);
        }
        return $this->modifyProfilForm($pseudo, $message);
    }

    /**
     * Ask to modify password, collect and send result message
     * @param string pseudo of user
     * @return function ask modifyProfilForm/adminModifyProfilForm with message
     */
    public function changePassword($pseudo)
    {
        $response = (new UserManager('user'))->changeProfilPassword($pseudo);
        $message = ['message' => $response['message']];
        if ($response['result']) {
            $message['type'] = 'success';
            return $this->modifyProfilForm($pseudo, $message);
        }
        $message['type'] = 'danger';
        return $this->modifyProfilForm($pseudo, $message);
    }

    /**
     * Ask to remove, collect and send result message
     * @param string pseudo of user
     * @return header|function return to homePage|ask modifyProfilForm/adminModifyProfilForm  with message
     */
    public function removeProfil($pseudo)
    {
        $response = (new UserManager('user'))->removeProfil($pseudo);
        if ($response['result']) {
            return header('Location : /');
        }
        $message = [
            'message' => $response['message'],
            'type' => 'danger'
        ];
        return $this->modifyProfilForm($pseudo, $message);
    }

    /**
     * Ask for a list of user and show them on adminUserList
     * @return TwigTemplate admin User list with a array of all User
     */
    public function adminListProfil()
    {
        $response = (new UserManager('user'))->getListUser();
        return print_r(
            $this->render(
                'admin\user\adminUserList',
                $response
            )
        );
    }

    /**
     * ask data of user and show them on AdminUserForm
     * @param string pseudo of user
     * @param array message and type of alert for bootstrap
     * @return TwigTemplate admin template of user profil form
     */
    public function adminModifyProfilForm($pseudo, $message = null)
    {
        $response = (new UserManager('user'))->getUser($pseudo, true);
        $response[($response['profil'])->getrole()] = 'selected';
        if (isset($message)) {
            $response['message'] = $message;
        }
        return print_r(
            $this->render(
                'admin\user\adminUserForm',
                $response
            )
        );
    }

    /**
     * Ask to change role and ask for admin template when operation is over (Admin only)
     * @param pseudo pseudo of user
     * @return function ask adminModifyProfilForm with message
     */
    public function adminModifyRole($pseudo)
    {
        $response = (new UserManager('user'))->changeRole($pseudo);
        $message = ['message' => $response['message']];
        if ($response['result']) {
            $message['type'] = 'success';
        } else {
            $message['type'] = 'danger';
        }
        
        return $this->adminModifyProfilForm($pseudo, $message);
    }

    /**
     * ask token and display admin user ban form
     * @param string pseudo of user
     * @param string message in case of error
     * @return TwigTemplate admin remove profil form
     */
    public function adminRemoveProfilForm($pseudo, $message = null)
    {
        $var = [
            'token' => (new UserManager('user'))->askNewCSRFLongToken('banUser'),
            'pseudo' => $pseudo
        ];
        if (isset($message)) {
            $var['message'] = $message;
        }
        return print_r(
            $this->render(
                'admin\user\adminUserBanForm',
                $var
            )
        );
    }

    /**
     * Redirect to ChangeEmail with admin confirmation
     * @param string pseudo of User
     * @return function ask changeEmail with admin confirmation
     */
    public function adminModifyEmail($pseudo)
    {
        return $this->changeEmail($pseudo, true);
    }

    /**
     * ask to remove profil of user and show them resumt
     * @return header|function redirect to admin user list | ask admin remove
     * profil with message
     */
    public function adminRemoveProfil($pseudo)
    {
        $response = (new UserManager('user'))->removeAdminProfil($pseudo);
        if ($response['result']) {
            return header('Location:\admin\profil');
        }
        return $this->adminRemoveProfilForm($pseudo, $response['message']);
    }
}
