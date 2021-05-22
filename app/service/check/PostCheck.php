<?php

namespace app\service\check;

use core\request\RequestManager;


class PostCheck
{
	function checkPostForm()
	{	
		$post = (new RequestManager())->getPost([
			'titlePost' => 'string',
			'chapoPost' => 'string',
			'contentPost' => 'string',
			'CSRFtoken' => 'longToken',
			'nameToken' => 'string'
		]);
		if ($post != false)
		{
			return $post;
		}
		return [
			"err" => true,
			"errMessage" => "Il y a des problèmes avec vos données, veuiller ressayer"
		];
	}
	function checkRemovePost()
	{
		$post = (new RequestManager())->getPost([
			'id' => 'int',
			'tokenName' => 'string',
			'CSRFtoken' => 'longToken'
		]);
		if ($post != false)
		{
			return $post;
		}
		return false;
	}
}