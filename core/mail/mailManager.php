<?php

namespace core\mail;

class mailManager {

    public static function sendMail($receiver, $subject, $message)
    {
        $message = wordwrap($message, 70, "\r\n");
        mail($receiver, $subject, $message);
    }
}