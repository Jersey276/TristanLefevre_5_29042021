<?php

namespace core\mail;

class MailManager {

    public function sendMail($receiver, $subject, $message)
    {
        $message = wordwrap($message, 70, "\r\n");
        mail($receiver, $subject, $message);
    }
}