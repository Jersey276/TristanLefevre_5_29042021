<?php

namespace app\service\check;

use core\request\RequestManager;
use app\service\check\UserCheck;

/**
 *  class for comment data verification about comment system
 * 	@author Tristan
 * 	@version 1
 */
class CommentCheck
{
    private $request;
    public function __construct()
    {
        $this->request = new RequestManager();
    }
    /**
     *  Verify new commentary
     * @return mixed post data if check success, array  with error messagewhen fail
     */
    public function checkNewComment()
    {
        $post = $this->request->getPost([
            'comment' => 'string',
            'token' => 'longToken',
            'nameToken' => 'string',
        ]);
        if ($post != false) {
            return $post;
        }
        return ['err' => true,
                'errMessage' => "Votre commentaire n'a pu être posté, veuiller ressayer"];
    }

	public function checkAdminComment()
	{		
		$post = $this->request->getPost([
			'token' => 'longToken',
            'nameToken' => "string"
		]);
		if ($post != false)
		{
			return $post;
		}
		return ['err' => true,
				'errMessage' => "une erreur a eu lieu avec vos données"];
	}
}