<?php

namespace app\util\mail;

class mailManager {

    public function sendMail($toName, $toEmail, $message)
    {
        mail($toEmail, 'contact de'.$toName, $message);
    }
}