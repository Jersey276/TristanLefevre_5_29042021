<?php

namespace app\service\check;

use App\app;
use app\service\query\UserQuery;
use core\database\SelectQuery;
use core\request\RequestManager;

/**
 *  class for verification of user data
 * 	@author Tristan
 * 	@version 1
 */
class UserCheck
{
    /**
     * Check data for register system. This check do:
     * - check if user already use email
     * @param string email to check
     * @return bool result
     */
    public function isEmailUsed($info)
    {
        $query = new SelectQuery;
        $statement = $query->select("*")->from('user')->where("email = :email")->toString();
        $check = (APP::getDB())->prepare($statement, [':email' => $info], "select", "app\model\User");
        if ($check == []) {
            return false;
        }
        return true;
    }
    /**
     * Check data for register system. This check do:
     * - check if a user already use this pseudo
     * @param string pseudo to check
     * @return bool result
     */
    public function isPseudoUsed($info)
    {
        $query = new SelectQuery;
        $statement = (new UserQuery())->getUserByPseudo();
        $check = (APP::getDB())->prepare($statement, [':pseudo' => $info], "select", 'app\model\User');
        if ($check == []) {
            return false;
        }
        return true;
    }


    public function changeEmailCheck()
    {
        $post = (new RequestManager())->getPost([
            "email" => "string|email",
            "token" => "longToken",
            "nameToken" => "string"
        ]);
        if ($post != false)
        {
            return $post;
        }
        return false;
    }

    public function changePasswordCheck()
    {
        $post = (new RequestManager())->getPost([
            "password" => "string",
            "confirmPassword" => "string",
            "token" => "longToken",
            "nameToken" => "string"
        ]);
        if ($post != false) {
            if ($post['password'] == $post['confirmPassword']) {
                return $post;
            }
            return [
                'result' => false, 
                'message' => 'le mot de passe et sa confirmation ne sont pas identique'
            ];
        }
        return [
            'result' => false, 
            'message' => 'un problème a eu lieu avec les données, veuiller recommencer'
        ];
    }

    public function removeProfilCheck()
    {
        $request = new RequestManager();
        $post = $request->getPost([
            "pseudoConfirm" => 'string',
            "token" => "longToken",
            "nameToken" => "string"
        ]);
        if ($post != false) {
            if ($post['pseudoConfirm'] == $request->session('pseudo'))
            {
                return $post;
            }
            return [
                'result' => false, 
                'message' => "Le pseudonyme de confirmation n'est pas le vôtre"
            ];
        }
        return [
            'result' => false, 
            'message' => 'un problème a eu lieu avec les données, veuiller recommencer'
        ];
    }
}
