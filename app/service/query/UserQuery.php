<?php

namespace app\service\query;

use core\database\DeleteQuery;
use core\database\SelectQuery;
use core\database\UpdateQuery;

class UserQuery
{
    public function getUserById()
    {
        return (new SelectQuery())
        ->select("*")
        ->from("user")
        ->where("user.idUser = :id")
        ->toString();
    }

    public function getUserByPseudo()
    {
        return (new SelectQuery())
            ->select(
                "user.idUser",
                "user.pseudo",
                "user.email",
                "role.nameRole as role"
            )
            ->from("user")
            ->leftJoin('role','user.idRole = role.idRole')
            ->where("user.pseudo = :pseudo")
            ->toString();
    }

    public function changeEmail()
    {
        return (new UpdateQuery())
        ->update('user')
        ->set('email = :email','isEmailChecked = false')
        ->where('pseudo = :pseudo')
        ->toString();
    }

    public function changePassword()
    {
        return (new UpdateQuery())
        ->update('user')
        ->set('password = :password')
        ->where('pseudo = :pseudo')
        ->toString();
    }

    public function deleteProfil()
    {
        return (new DeleteQuery())
        ->delete('user')
        ->where('pseudo = :pseudo')
        ->toString();
    }
}
