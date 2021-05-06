<?php

namespace app\controller;

use app\util\mail\mailManager as MailManager;

class homeController extends Controller{
    function home() {

        echo $this->render("home",[]);
    }

    function sendmail()
    {
        $formData = $_POST;
        $mail = new MailManager();
        $mail->sendMail($formData['name'], $formData['email'], $formData['message']);
    }
}