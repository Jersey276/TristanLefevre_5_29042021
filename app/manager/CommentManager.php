<?php

namespace app\manager;

use app\app;
use app\model\User;

use app\service\check\CommentCheck;

use app\service\query\CommentQuery;
use app\service\query\UserQuery;


use core\manager\AbstractManager;

class CommentManager extends AbstractManager
{
    private $query;

    public function __construct()
    {
        parent::__construct((App::getDB()));
        $this->query = new CommentQuery();
    }

    /**
     * get Comment for a post
     * @param int post id
     * @param bool if request is used for
     */
    public function getComment($id, $isAdmin = false)
    {
        $approuvedComment = (App::getDB())->prepare(
            $this->query->getApprouveComment(),
            [':post' => $id],
            "select",
            "app\model\Comment"
        );
        if ($isAdmin) {
            $toApprouveComment = (App::getDB())->prepare(
                $this->query->getNonApprouvedComment(),
                [':post' => $id],
                "select",
                "app\model\Comment"
            );
            $var = [ 'approuved' => $approuvedComment , 'toApprouve' => $toApprouveComment, 'token' => $this->askNewCSRFLongToken('comment')];
        } else {
            $var = $approuvedComment;
        }
        return $var;
    }

    public function postComment($id)
    {
        $post = (new CommentCheck())->checkNewComment();
        if (empty($post['err'])) {
            $author = (new User())
                ->hydrate(
                    $this->database->prepare(
                        (new UserQuery())->getUserByPseudo(),
                        [':pseudo' => $this->request->session('pseudo')],
                        'select',
                        'app\model\user',
                        true
                    )
                );
            $this->database->prepare(
                $this->query->postNewComment(),
                [
                    ':idUser' => $author->getidUser(),
                    ':idPost' => $id,
                    ':textComment' => $post['comment']
                ],
                'insert'
            );
            return [
                'result' => true,
                'message' => [
                    'type' => 'success',
                    'message' => 'Votre commentaire a été envoyé, il sera controlé par la modération avant publication',
                    'btnReturn' => '/post/'. $id,
                    'btnMessage' => "retour à l'article"
                ]
            ];
        }
        return [
            'result' => false,
                'message' => [
                    'type' => 'danger',
                    'message' => $post['errMessage'],
                    'btnReturn' => '/post/'. $id,
                    'btnMessage' => "retour à l'article"
            ]
        ];
    }
}

