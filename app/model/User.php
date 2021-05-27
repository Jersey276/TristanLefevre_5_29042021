<?php

namespace app\model;

use core\model\AbstractModel;

class User extends AbstractModel
{
    private $idUser;
    private $idRole;
    private $pseudo;
    private $email;
    private $password;
    private $role;
    private $isEmailChecked;

    //getter
    public function getidUser()
    {
        return $this->idUser;
    }
    public function getidRole()
    {
        return $this->idRole;
    }
    public function getpseudo()
    {
        return $this->pseudo;
    }
    public function getemail()
    {
        return $this->email;
    }
    public function getpassword()
    {
        return $this->password;
    }
    public function getrole()
    {
        return $this->role;
    }
    public function getisEmailChecked()
    {
        return $this->isEmailChecked;
    }

    //Setter
    public function setidUser($idUser)
    {
        $this->idUser = $idUser;
    }
    public function setidRole($idRole)
    {
        $this->idRole = $idRole;
    }
    public function setpseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }
    public function setemail($email)
    {
        $this->email = $email;
    }
    public function setpassword($password)
    {
        $this->password = $password;
    }
    public function setrole($role)
    {
        $this->role = $role;
    }
    public function setisEmailChecked($isEmailChecked)
    {
        $this->isEmailChecked = $isEmailChecked;
    }
}
