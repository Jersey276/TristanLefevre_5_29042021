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
    private $request;

    public function __construct()
    {
        $this->query = new PostQuery();
        $this->request = new RequestManager();
    }
    /**
     * Collect all post from database
     * @return array list of Post objects
     */
    public function listPosts()
    {
        return (App::getDB())->prepare($this->query->getAllArticles(), [], "select", "app\model\Post");
    }

    /**
     * Get post by id
     * @param int id of post
     * @param bool ask for CSRFtoken
     * @return Post article
     */
    public function getPost($id, $editor = false)
    {
        $postStatement =
            (App::getDB())->prepare(
                $this->query->getArticle(),
                [':id' => $id],
                "select",
                "app\model\Post",
                true
            );
        $post = (new Post())->hydrate($postStatement);
        $var = ['post' => $post];
        return $var;
    }
		return $article;
	}
}
