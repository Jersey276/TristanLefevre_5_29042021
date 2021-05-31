<?php

namespace core\mail;

class MailManager {

    public function sendMail($receiver, $subject, $message)
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        $headers .= 'From: <no-reply@tristan-lefevre.com>' . "\r\n";
        

        $message = wordwrap($message, 70, "\r\n");
        mail(
            $receiver, 
            $subject,
            "<html>
            <head>
                <title>.$subject.</title>
            </head>
            <body>
            ".
            $message
            ."</body>
            </html>
            ",$headers);
    }
}