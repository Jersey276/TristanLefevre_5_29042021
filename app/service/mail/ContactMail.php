<?php

namespace app\service\mail;

use core\mail\MailManager;

class ContactMail
{
	/**
	 * Send mail for contact
	 * @param array all post data from contact form
	 */
	function contactMail($formData)
	{
		(new MailManager())->sendMail(
			getenv('ADMIN_EMAIL'), 
			"contact de ". $formData['name'], 
			$formData['name']. '('. $formData['email'] . ') vous demande : <br> '. $formData['message']);

	}
}