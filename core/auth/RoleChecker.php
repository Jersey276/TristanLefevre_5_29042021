<?php

namespace core\auth;

use core\request\RequestManager;

/**
 * class for check if user role can use a function
 * @author Tristan
 * @version 1
 */
class RoleChecker
{
    private const ROLE_RULE = [
        'User' =>  ['User','Writer','Admin'],
        'Writer' => ['Writer', 'Admin'],
        'Admin' => ['Admin']
    ];

    /**
     * Check if user actual role can use
     * @param string role of function to check
     * @return bool result of check of user role capacity
     */
    public static function role($role) : bool
    {
        $request = new requestManager();
        if ($request->isSetSession('role')) {
            if (in_array($request->session('role'), self::ROLE_RULE[$role])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is a guest
     * @return bool result of check if user is a guest
     */
    public static function guest()
    {
        $request = new requestManager();
        if ($request->isSetSession('role')) {
            return false;
        }
        return true;
    }
}
