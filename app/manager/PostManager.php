<?php

namespace app\manager;

use app\app;
use core\database\DatabaseManager;
use app\service\query\PostQuery;
use app\service\query\UserQuery;

use app\service\check\PostCheck;

use core\auth\RoleChecker;

use core\request\RequestManager;

use core\manager\AbstractManager;

use app\model\Post;
use app\model\User;

/**
 * Manager for all post object related function
 * @author Tristan
 * @version 1
 */
class PostManager extends AbstractManager
{
    private $query;

    public function __construct()
    {
        parent::__construct(App::getDB());
        $this->query = new PostQuery();
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
     * @return array result on true with Post data on success case, false with code on fail case or forbidden access
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
        if ($postStatement != false) {
            $post = (new Post())->hydrate($postStatement);
            $var = ['post' => $post];
            if ($editor) {
                $roleChecker = new RoleChecker();
                if ($roleChecker->role("Admin") || $roleChecker->role("Writer") &&
                $post->getauthor() == $this->request->session('pseudo')) {
                    $token = $this->request->newCSRFLongToken('post');
                    $var = array_merge($var, ['CSRFToken' => $token]);
                } else {
                    return ['result' =>false, 'code' => 403];
                }
            }
            return ['result' => true, 'var' => $var];
        }
        return ['result' => false, 'code' => 404];
    }

    /**
     * Get personnal post and all other post if user is Admin
     * @return array all variable need for admin post list (include list of personnal article, list of other article)
     */
    public function listPostsAdmin()
    {
        $pseudo = $this->request->session('pseudo');

        $var = ["myPosts" => (App::getDB())->prepare(
            $this->query->getMyArticles(),
            [ ":pseudo" => $pseudo],
            "select",
            "app\model\Post"
        )];
        if (RoleChecker::role('Admin')) {
            $var = array_merge($var, [ 'otherPosts' => (App::getDB())->prepare(
                $this->query->getOtherArticles(),
                [ ":pseudo" => $pseudo],
                "select",
                "app\model\Post"
            )
                ]);
        }
        return $var;
    }

    /**
     * Create a new article
     * @return array result with message data in case of error
     */
    public function addPost()
    {
        $post = (new PostCheck())->checkPostForm();
        if (empty($post['err'])) {
            $user = (new User())->hydrate(
                (APP::getDB())->prepare(
                    (new UserQuery())->getUserByPseudo(),
                    ['pseudo' => $this->request->session('pseudo')],
                    "select",
                    "app\model\User",
                    true
                )
            );
            (App::getDB())->prepare(
                $this->query->newArticle(),
                [':idUser' => $user->getidUser(),
                ':titlePost' => $post['titlePost'],
                ':chapoPost' => $post['chapoPost'],
                ':contentPost' => $post['contentPost']
                ],
                'insert'
            );
            return [
                "result" => true
            ];
        }

        return [
            'result' => false,
            'var' => [
                'type' => "danger",
                "message" => $post['errMessage'],
                "CSRFToken" => $this->asknewCSRFLongToken('post')
            ]
        ];
    }

    /**
     * modify a post on database
     * @param int post id
     * @return array result of database
     */
    public function modifyPost($id)
    {
        $post = (new PostCheck())->checkPostForm();
        if (empty($post['err'])) {
            (App::getDB())->prepare(
                $this->query->modifyArticle(),
                [':titlePost' => $post['titlePost'],
                ':chapoPost' => $post['chapoPost'],
                ':contentPost' => $post['contentPost'],
                ':id' => $id],
                'update'
            );
            return [
                "result" => true,
                "var" => [
                    "type" => "success",
                    "message" => "l'article a été mis à jour",
                    "CSRFToken" => $this->asknewCSRFLongToken('post')
                ]
            ];
        }
        return [
            'result' => false,
            'var' => [
                'type' => "danger",
                "message" => $post['errMessage'],
                "CSRFToken" => $this->asknewCSRFLongToken('post')
            ]
        ];
    }

    /**
     * remove a post on database
     * @param int post id
     * @return bool result
     */
    public function removePost($id)
    {
        $query = (App::getDB())->prepare(
            $this->query->removePost(),
            [':id' => $id],
            "delete"
        );
        if ($query) {
            return true;
        }
        return false;
    }

}
