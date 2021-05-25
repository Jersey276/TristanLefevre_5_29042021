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
    public function postComment($id)
    {
        $response = (new CommentManager())->postComment($id);
        return print_r(
            $this->render(
                'message',
                $response['message']
            )
        );
    }
}

