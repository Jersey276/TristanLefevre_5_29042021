<?php

namespace app\controller;

use core\controller\AbstractController;

class ErrorController extends AbstractController
{
    public function error404()
    {
        return parent::error404();
    }
    public function error403($isAdmin = false)
    {
        return parent::error403($isAdmin);
    }
}
