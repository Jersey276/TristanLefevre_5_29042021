<?php

namespace app\service\mail;

use core\mail\mailManager;

class UserMail
{
	/**
	 * Send mail for valid email
	 * @param string sender mail
	 * @param string token to send
	 */
	function checkEmailMail($email, $token)
	{
		(new MailManager())->sendmail(
			$email,
			"Bienvenue sur " . filter_input(INPUT_SERVER, 'SERVER_NAME')
			,"bienvenue sur "  
			.filter_input(INPUT_SERVER, 'SERVER_NAME') 
			.". /r/n voici un lien pour valider votre email : <a href='" . filter_input(INPUT_SERVER, 'SERVER_NAME') .'/login/'. $token . "'>changer mon mot de passe<a>"
		);
	}
	/**
	 * Send mail for reset password
	 * @param string sender mail
	 * @param string token to send
	 */
	function resetPassword($email, $token)
	{
		(new MailManager())->sendmail(
			$email,
			"demande de nouveau mot de passe",
			"Vous avez demandé de changer le mot de passe, voici un lien pour le faire : <a href='"
			. filter_input(INPUT_SERVER, 'SERVER_NAME')
			.'/forgotPassword/'. $token 
			. "'>changer mon mot de passe<a>");
	}
}
?>