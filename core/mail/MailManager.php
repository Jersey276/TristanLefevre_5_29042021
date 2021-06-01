<?php

namespace core\mail;

use core\env\DotEnv;

class MailManager {

    /**
     * Send a mail
     * @param string receiver of mail
     * @param string subject of mail
     * @param string message for mail (can be html message)
     */
    public function sendMail($receiver, $subject, $message)
    {
        $senderMail = getenv('MAIL_FROM');

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        $headers .= 'From: <'. $senderMail .'>' . "\r\n";
        

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
