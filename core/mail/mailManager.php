<?php

namespace core\mail;

class mailManager {

    public function sendMail($toName, $message)
    {
        $message = wordwrap($message, 70, "\r\n");
        mail("tristan-lefevre@hotmail.fr", $toName, $message);
    }
}