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
    public function listPosts()
    {
        return print_r(
            $this->render(
                'post/postList',
                ['posts' => (new PostManager())->listPosts()]
            )
        );
    }

    /**
     * Get one article
     * @param int article id
     * @return template article.twig with detailled information about article
     */
    public function getPost($id)
    {
        return print_r(
            $this->render(
                'post/postDetail',
                (new PostManager())->getPost($id)
            )
        );
    }

    /**
     * Display empty article form
     * @return
     */
    public function addPostForm()
    {
        return print_r(
            $this->render(
                'admin/post/adminPostForm',
                [ 'CSRFToken' => (new PostManager())->asknewCSRFLongToken('newPost') ]
            )
        );
    }

    /**
     * Create new article
     * @return TwigTemplate
     * @return TwigTemplate form with data
     */
    public function addPost()
    {
        $response = (new PostManager())->addPost();
        if ($response['result']) {
            return header('Location: /admin/post');
        }
        return print_r($this->render('admin/post/adminPostForm', $response['var']));
    }

    /**
     * List all article on admin specific page
     * @return TwigTemplate listPostAdmin list of posts with all button for edit,remove and manage comment
     */
    public function listPostsAdmin()
    {
        return print_r(
            $this->render(
                'admin/post/adminPostList',
                (new PostManager())->ListPostsAdmin()
            )
        );
    }

    /**
     * Display post form with all data from article
     * @param int id of article
     */
    public function modifyPostForm($id)
    {
        $post = (new PostManager())->getPost($id, true);
        if ($post != false) {
            return print_r(
                $this->render(
                    'admin/post/adminPostForm',
                    $post
                )
            );
        }
        return $this->error403(true);
    }

    /**
     * Edit one Article
     * @param int article id
     * @return header redirection
     */
    public function modifyPost($id)
    {
        $response = (new PostManager)->modifyPost($id);
        return print_r(
            $this->render(
                'admin/post/adminPostForm',
                ($response['var'])
            )
        );
    }

    /**
     *
     */
    public function removePostForm($id)
    {
        $postEditor = (new PostManager)->getPost($id, true);
        if ($postEditor != false) {
            $post = $postEditor['post'];
            $token = ((new PostManager)->askNewCSRFLongToken("removePost"));
            return print_r(
                $this->render(
                    'admin/adminMessage',
                    [
                    "isForm" => true,
                    "type" => "danger",
                    "message" => "Etes-vous sûr de vouloir supprimer cet article ?",
                    "link" => [
                        "url" => $post->getDisplayUrl(),
                        "message" => $post->gettitlePost()
                    ],
                    "form" => [
                        "key" => "id",
                        "val" => $id,
                        "tokenName" => $token['nameToken'],
                        "token" => $token['token']
                    ],
                    "btn1" => [
                        "link" => "/admin/post/remove/". $id,
                        "type" => "danger",
                        "message" => "Oui"
                    ],
                    "btnReturn" => [
                        "link" => "/admin/post",
                        "type" => "danger",
                        "message" => "Non"
                    ]
                ]
                )
            );
        }
        return $this->error403(true);
    }

    /**
     * Remove one article
     * @param int article id
     * @return header redirection to post List
     */
    public function removePost($id)
    {
        (new PostManager)->removePost($id);
        return header('Location: /admin/post');
    }
}
