<?php

namespace core\auth;

use core\request\requestManager;

class roleChecker
{
    private const ROLE_RULE = [
        'User' =>  ['User','Writer','Admin'],
        'Writer' => ['Writer', 'Admin'],
        'Admin' => ['Admin']
    ];

    public static function role($role)
    {
        $request = new requestManager();
        if ($request->isSetSession('role')) {
            if (in_array($request->session('role'), self::ROLE_RULE[$role])) {
                return true;
            }
        }
        return false;
    }
    public static function guest()
    {
        $request = new requestManager();
        if ($request->isSetSession('role')) {
            return false;
        }
        return true;
    }
}
