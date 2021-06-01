<?php

namespace app\controller;

use core\controller\AbstractController;
use app\manager\CommentManager;
use app\model\Comment;

/**
 * Controller of Comment system
 * @author Tristan
 * @version 1
 */
class CommentController extends AbstractController
{
    /**
     * Ask to post comment and display result on message template
     * @param int post id
     * @return TwigTemplate message with variable of result
     */
    public function postComment(int $id)
    {
        $response = (new CommentManager())->postComment($id);
        return print_r(
            $this->render(
                'message',
                $response['message']
            )
        );
    }

    /**
     * List all comment from article
     * @param int id of post
     * @param array message with his type if this
     * function is used by another
     * @return TwigTemplate admin comment list from a post
     */
    public function adminListComment(int $post, string $message = null)
    {
        $response = (new CommentManager())->getComment($post, true);
        if ($message != null) {
            $response['message'] = $message;
        }
        return print_r(
            $this->render(
                'admin/post/adminPostComment',
                $response
            )
        );
    }

    /**
     * Valid a comment
     * @param int post id
     * @param int comment id
     * @return function reload admin list comment with message
     */
    public function validComment(int $post, int $comment)
    {
        $response = (new CommentManager())->validComment($comment);
        $this->adminListComment($post, $response['message']);
    }

    /**
     * Remove a comment
     * @param int post id
     * @param int comment id
     * @return function reload admin list comment with message
     */
    public function removeComment(int $post, int $comment)
    {
        $response = (new CommentManager())->removeComment($comment);
        $this->adminListComment($post, $response['message']);
    }
}
