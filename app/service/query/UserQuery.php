<?php

namespace app\service\query;

use core\database\DeleteQuery;
use core\database\SelectQuery;
use core\database\UpdateQuery;

class UserQuery
{
    /**
     * Query for collect user by his Id
     * @return String query
     */
    public function getUserById()
    {
        return (new SelectQuery())
        ->select("*")
        ->from("user")
        ->where("user.idUser = :id")
        ->toString();
    }

    /**
     * Query for collect user by his pseudo
     * @return String query
     */
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
            ->leftJoin('role', 'user.idRole = role.idRole')
            ->where("user.pseudo = :pseudo")
            ->toString();
    }

    /**
     * Query for change email of user by his pseudo
     * @return String query
     */
    public function changeEmail()
    {
        return (new UpdateQuery())
        ->update('user')
        ->set('email = :email', 'isEmailChecked = false')
        ->where('pseudo = :pseudo')
        ->toString();
    }

    /**
     * Query for change password of user by his pseudo
     * @return String query
     */
    public function changePassword()
    {
        return (new UpdateQuery())
        ->update('user')
        ->set('password = :password')
        ->where('pseudo = :pseudo')
        ->toString();
    }

    /**
     * Query for delete a user by his pseudo
     * @return String query
     */
    public function deleteProfil()
    {
        return (new DeleteQuery())
        ->delete('user')
        ->where('pseudo = :pseudo')
        ->toString();
    }

    /**
     * Query for get a list of users
     * @return String query
     */
    public function getListUser()
    {
        return (new SelectQuery())
        ->select(
            'user.idUser',
            'user.pseudo',
            'role.nameRole as role'
        )
        ->from('user')
        ->leftJoin('role', 'user.idRole = role.idRole')
        ->where(1)
        ->toString();
    }

    /**
     * Query for role of specific user
     * @return String query
     */
    public function changeRole()
    {
        return (new UpdateQuery())
        ->update('user')
        ->set('idRole = :idRole')
        ->where('user.pseudo = :pseudo')
        ->toString();
    }
}
