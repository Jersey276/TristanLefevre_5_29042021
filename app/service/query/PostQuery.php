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

    /**
     * get article by id
     * @param int id of article
     * @return string query
     */
    public function getArticle()
    {
        return (new SelectQuery)
            ->select('post.idPost', 'post.titlePost', 'post.chapoPost', 'post.contentPost', 'post.createdAt', 'user.pseudo as author')
            ->from('post')
            ->leftJoin('user', 'user.idUser = post.idUser')
            ->where("post.idPost = :id")
            ->toString();
    }
}
