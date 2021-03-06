<?php

namespace app\service\query;

use core\database\SelectQuery;

class AuthQuery
{
    public function getTokenQuery()
    {
        return (new SelectQuery())->select('*')
        ->from('token')
        ->where("token = :token")
        ->toString();
    }
}
