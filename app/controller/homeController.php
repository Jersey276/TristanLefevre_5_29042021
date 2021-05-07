<?php

namespace app\controller;

use core\mail\mailManager as MailManager;

class homeController extends Controller{
    function home() {
        print $this->render("home",[]);
    }

    function showCV() {
        // Store the file name into variable
        $file = 'CV.pdf';
        $filename = 'CV.pdf';
        
        // Header content type
        header('Content-type: application/pdf');    
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        // Read the file
        @readfile($file);
    }

    function sendmail()
    {
        var_dump($_POST);
        $formData = $_POST;
        $mail = new MailManager();
        $mail->sendMail($formData['name'], $formData['email'], $formData['message']);
    }
}