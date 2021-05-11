<?php

namespace app\controller;

use core\controller\AbstractController;
use core\mail\mailManager as MailManager;
use core\request\requestManager as RequestManager;

class homeController extends AbstractController{
    function home() {
        return print_r($this->render("home"));
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
        $request = new RequestManager();
        $formData = $request->getPost([
            "name" => "string",
            "email" => "string|email",
            "message" => "string"
        ]);
        if ($formData != false) 
        {
            $mail = new MailManager();
            $mail->sendMail('tristan-lefevre@hotmail.fr', "contact de ". $formData['name'], $formData['name']. '('. $formData['email'] . ') vous demande : /r/n'. $formData['message']);
            $message = array("type" => "success", "message" => "votre message a bien été envoyé");
        } else
        {
            $message = array("type" => "danger", "message" => "un élément du formulaire n'a pas été correctement inséré");
        }
        return print_r(json_encode($message));
    }
}