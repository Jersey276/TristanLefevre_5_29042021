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
    private $check;

    public function __construct()
    {
        parent::__construct((App::getDB()));
        $this->query = new CommentQuery();
        $this->check = new CommentCheck();
    }

    /**
     * get Comment for a post
     * @param int post id
     * @param bool if request is used by admin system
     * @return array approuved comment (with non-approuved comment and token for admin)
     */
    public function getComment(int $id, bool $isAdmin = false) : array
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

    /**
     * check Comment form data and post it on database
     * @param int id of post
     * @return array table with result and var for TwigTemplate
     */
    public function postComment(int $id) : array
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

    /**
     * Check valid comment form and valid a comment on database
     * @param int comment id
     * @return array table with result and var for TwigTemplate
     */
    public function validComment(int $id) : array
    {
        $post = (new CommentCheck())->checkAdminComment();
        if (empty($post['err'])) {
            (App::getDB())->prepare(
                $this->query->approuveComment(),
                [':idComment' => $id],
                'update'
            );
            return [
                'result' => true,
                'message' => [
                    'type' => 'success',
                    'text' => 'commentaire approuvé et posté'
                ]
            ];
        }
        return [
            'result' => false,
            'message' => [
                'type' => 'danger',
                'text' => $post['errMessage']
            ]
        ];
    }

    /**
     * Check valid comment form and remove a comment from database
     * @param int comment id
     * @return array table with result and var for TwigTemplate
     */
    public function removeComment(int $id) : array
    {
        $post = (new CommentCheck())->checkAdminComment();
        if (empty($post['err'])) {
            (App::getDB())->prepare(
                $this->query->removeComment(),
                [':idComment' => $id],
                'delete'
            );
            return [
                'result' => true,
                'message' => [
                    'type' => 'success',
                    'text' => 'commentaire supprimé'
                ]
            ];
        }
        return [
            'result' => false,
            'message' => [
                'type' => 'danger',
                'text' => $post['errMessage']
            ]
        ];
    }
}
