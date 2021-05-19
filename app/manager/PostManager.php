<?php

namespace app\manager;

use app\app;
use core\database\DatabaseManager;
use app\service\query\PostQuery;

use core\auth\RoleChecker;

use core\request\RequestManager;

use app\model\Post;

/**
 * Manager for all post object related function
 * @author Tristan
 * @version 1
 */
class PostManager
{
	private $query;

	function __construct()
	{
		$this->query = new PostQuery();
	}
	/**
	 * Collect all post from database and get 
	 * @return array list of Post objects
	 */
	function listPosts()
	{
		return (App::getDB())->prepare($this->query->getAllArticles(),"select","app\model\Post");
	}
	/**
	 * Get post by id
	 * @param int id of post
	 * @return Post 
	 */
	function getPost($id)
	{
		$article = new Post();
		((App::getDB())->prepare($this->query->getArticle($id),"select","app\model\Post", true)
			);
		return $article;
	}
}
?>