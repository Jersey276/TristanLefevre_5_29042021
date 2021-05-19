<?php

namespace app\controller;

use app\model\article;
use app\manager\PostManager;
use core\controller\AbstractController;

/**
 * Controller for post system
 * @author Tristan
 * @version 1
 */
class PostController extends AbstractController
{
	/**
	 * List all Article
	 * @return template listArticle.twig with reduced information about article
	 */
	function listPosts()
	{
		return print_r(
			$this->render(
				'post/postList',
				['articles' => (new PostManager())->listPosts()]
			)
		);
	}

	/**
	 * Get one article
	 * @param int article id
	 * @return template article.twig with detailled information about article
	 */
	function getPost($id)
	{
		return print_r(
			$this->render(
			'post/postDetail',
			['article' => (new PostManager())->getPost($id)]
			)
		);
	}

	/**
	 * Create new article
	 * @return
	 */
	function store()
	{

	}

	/**
	 * List all article on admin specific page
	 * @return
	 */
	function listPostsAdmin()
	{

	}

	/**
	 * Edit one Article
	 * @param int article id
	 * @return
	 */
	function editArticle($id)
	{

	}

	/**
	 * Remove one article
	 * @param int article id
	 * @return 
	 */
	function removeArticle($id)
	{

	}
}