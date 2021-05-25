<?php

namespace app\service\query;

use core\database\SelectQuery;
use core\database\InsertQuery;
use core\database\UpdateQuery;
use core\database\DeleteQuery;

class PostQuery
{
    /**
     * generate query for collect all article
     * @return string query
     */
    public function getAllArticles()
    {
        return (new SelectQuery)
            ->select('post.idPost', 'post.titlePost', 'post.chapoPost', 'post.createdAt', 'user.pseudo as author')
            ->from('post')
            ->leftJoin('user', 'user.idUser = post.idUser')
            ->where(1)
            ->orderBy('idPost', 'ASC')
            ->toString();
    }

    public function AdminGetAllArticle()
    {
        return (new SelectQuery)
            ->select('post.idPost', 'post.titlePost', 'post.chapoPost', 'post.createdAt', 'user.pseudo as author',)
            ->from('post')
            ->leftJoin('user', 'user.idUser = post.idUser')
            ->leftJoin('comment','comment.idPost = post.idPost')
            ->where('comment.isApprouved = 0')
            ->groupBy('idPost')
            ->toString();
    }

    /**
     * get article by id
     * @param int id of article
     * @return string query
     */
    public function getArticle()
    {
        return (new SelectQuery)
            ->select(
                'post.idPost',
                'post.titlePost',
                'post.chapoPost',
                'post.contentPost',
                'post.createdAt',
                'user.pseudo as author'
            )
            ->from('post')
            ->leftJoin('user', 'user.idUser = post.idUser')
            ->where("post.idPost = :id")
            ->groupBy('idPost')
            ->toString();
    }

    public function getMyArticles()
    {
        return (new SelectQuery)
        ->select(
            'post.idPost',
            'post.titlePost',
            'post.chapoPost',
            'post.createdAt',
            'user.pseudo as author',
            'COUNT(CASE WHEN comment.isApprouved=0 THEN 1 END) as nbComToApprouve'
        )
        ->from('post')
        ->leftJoin('user', 'user.idUser = post.idUser')
        ->leftJoin('comment','comment.idPost = post.idPost')
        ->where("user.pseudo = :pseudo")
        ->groupBy('idPost')
        ->toString();
    }

    public function getOtherArticles()
    {
        return (new SelectQuery)
        ->select(
            'post.idPost',
            'post.titlePost',
            'post.chapoPost',
            'post.createdAt',
            'user.pseudo as author',
            'COUNT(CASE WHEN comment.isApprouved=0 THEN 1 END) as nbComToApprouve'
            )
        ->from('post')
        ->leftJoin('user', 'user.idUser = post.idUser')
        ->leftJoin('comment','comment.idPost = post.idPost')
        ->where("user.pseudo != :pseudo")
        ->groupBy('idPost')
        ->toString();
    }
    public function newArticle()
    {
        return (new InsertQuery)
        ->insertInto('post')
        ->key('idUser', 'titlePost', 'chapoPost', 'contentPost')
        ->value(':idUser',':titlePost', ':chapoPost', ':contentPost')
        ->toString();
    }
    public function modifyArticle()
    {
        return (new UpdateQuery)
        ->update('post')
        ->set(
            "titlePost = :titlePost",
            "chapoPost = :chapoPost",
            "contentPost = :contentPost"
        )
        ->where("idPost = :id")
        ->toString();
    }
    public function removePost()
    {
        return (new DeleteQuery)
            ->delete('post')
            ->where("idPost = :id")
            ->toString();
    }
}
