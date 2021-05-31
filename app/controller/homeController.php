<?php

namespace app\controller;

use core\controller\AbstractController;
use app\service\mail\ContactMail;
use core\request\requestManager as RequestManager;
use app\manager\UserManager;

/**
 * Controller of home page, contact form and admin home page
 * @author Tristan
 * @version 2
 */
class HomeController extends AbstractController
{
    /**
     * Show homepage
     * @return TwigTemplate home page
     */
    function home() {
        $token = (new UserManager())->askNewCSRFLongToken('contact') ;
        return print_r(
            $this->render(
                "home",
                ['token' => $token]
            )
        );
    }
    
    /**
     * Display cv
     */
    function showCV()
    {
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

    /**
     * send message to admin
     * @return json message of confirmation
     */
    function sendmail()
    {
        $request = new RequestManager();
        $formData = $request->getPost([
            "name" => "string",
            "email" => "string|email",
            "message" => "string",
            "token" => "longToken",
            "nameToken" => "string"
        ]);
        if ($formData != false) 
        {
            (new ContactMail())->contactMail($formData);
            $message = array("type" => "success", "message" => "votre message a bien été envoyé");
        } else
        {
            $message = array("type" => "danger", "message" => "un élément du formulaire n'a pas été correctement inséré");
        }
        return print_r(json_encode($message));
    }
    
    /**
     * Access to Admin home page
     * @return TwigTemplate admin page with 
     */
    function adminHome()
    {
        return print_r($this->render('admin/adminHome',['active1' => 'active']));
    }
    
}