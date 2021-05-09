<?php

namespace core\mail;

class mailManager {

    public function sendMail($to, $subject, $message)
    {
        $message = wordwrap($message, 70, "\r\n");
        mail($to, $subject, $message);
    }
}