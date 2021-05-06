<?php

namespace app\controller;

use app\util\mail\mailManager as MailManager;

class homeController extends Controller{
    function home() {

        echo $this->render("home",[]);
    }


}