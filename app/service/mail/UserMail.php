<?php

namespace app\service\mail;

use core\mail\mailManager;

/**
 * Function for send prebuild mail about user need
 * @author Tristan
 * @version 2
 */
class UserMail
{
    /**
     * Send mail for valid email
     * @param string sender email
     * @param string token to send
     * @param bool check is you send a welcome mail or a mail for report mail address change
     */
    public function checkEmailMail($email, $token, $isonlyChange = false)
    {
        if ($isonlyChange) {
            return (new MailManager())->sendmail(
                $email,
                "Changement d'adresse mail",
                "vous avez modifié votre adresse mail. \r\n voici un lien pour valider votre email : <a href='" . filter_input(INPUT_SERVER, 'SERVER_NAME') .'/login/'. $token . "'>Valider mon email</a>"
            );
        }
        return (new MailManager())->sendmail(
            $email,
            "Bienvenue sur " . filter_input(INPUT_SERVER, 'SERVER_NAME'),
            "bienvenue sur "
            .filter_input(INPUT_SERVER, 'SERVER_NAME')
            .". \r\n voici un lien pour valider votre email : <a href='" . filter_input(INPUT_SERVER, 'SERVER_NAME') .'/login/'. $token . "'>Valider mon email</a>"
        );
    }
    /**
     * Send mail for reset password
     * @param string sender email
     * @param string token to send
     */
    public function resetPassword($email, $token)
    {
        (new MailManager())->sendmail(
            $email,
            "demande de nouveau mot de passe",
            "Vous avez demandé de changer le mot de passe, voici un lien pour le faire : <a href='"
            . filter_input(INPUT_SERVER, 'SERVER_NAME')
            .'/forgotPassword/'. $token
            . "'>changer mon mot de passe</a>"
        );
    }

    /**
     * Send mail for report ban to deleted user
     * @param string sender email
     * @param string message
     */
    public function reportBan($email, $message)
    {
        (new MailManager())->sendmail(
            $email,
            "Votre compte a été supprimé",
            "Un administrateur à supprimé votre compte pour la raison suivante :/r/n"
            . $message
        );
    }
}
