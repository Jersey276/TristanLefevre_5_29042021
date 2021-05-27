<?php

namespace app\controller;

use app\manager\UserManager;
use core\controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * 
     */

    public function modifyProfilForm($pseudo, $message = null)
    {
        $response = (new UserManager())->getUser($pseudo);
        if (isset($message))
        {
            $response['message'] = $message;
        }
        return print_r(
            $this->render(
                'user\UserProfilForm', 
                $response
            )
        );
    }

    public function changeEmail($pseudo)
    {
        $response = (new UserManager())->modifyEmail($pseudo);
        $message = ['message' => $response['message']];
        if ($response['result'])
        {
            $message['type'] = 'warning';
            return $this->modifyProfilForm($pseudo, $message);
        }
        $message['type'] = 'danger';
        return $this->modifyProfilForm($pseudo, $message);
    }

    public function changePassword($pseudo)
    {
        $response = (new UserManager())->changeProfilPassword($pseudo);
        $message = ['message' => $response['message']];
        if ($response['result'])
        {
            $message['type'] = 'success';
            return $this->modifyProfilForm($pseudo, $message);
        }
        $message['type'] = 'danger';
        return $this->modifyProfilForm($pseudo, $message);
    }

    public function removeProfil($pseudo)
    {
        $response = (new UserManager())->removeProfil($pseudo);
        if ($response['result'])
        {
            return header('Location : /');
        }
        $message = [
            'message' => $response['message'],
            'type' => 'danger'
        ];
        return $this->modifyProfilForm($pseudo, $message);
    }
}
