<?php

namespace app\service\query;

use core\database\SelectQuery;

class UserQuery
{
    public function getUserById()
    {
        return (new SelectQuery())->select("*")->from("user")->where("user.idUser = :id")->toString();
    }

    public function getUserByPseudo()
    {
        return (new SelectQuery())->select("*")->from("user")->where("user.pseudo = :pseudo")->toString();
    }
}
