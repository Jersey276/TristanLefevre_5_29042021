<?php

namespace app\service\query;

use core\database\SelectQuery;
use core\database\InsertQuery;
use core\database\UpdateQuery;
use core\database\DeleteQuery;

class CommentQuery
{
    public function postNewComment()
    {
        return (new InsertQuery())
        ->insertInto('comment')
        ->key('idPost','idUser','textComment')
        ->value(':idPost',':idUser',':textComment')
        ->toString();
    }
    public function getApprouveComment()
    {
        return (new SelectQuery())
		->select('comment.idComment', 'comment.idUser', 'comment.idPost', 'comment.textComment','comment.isApprouved','user.pseudo as author')
        ->from('Comment')
		->leftJoin('user','User.idUser = comment.idUser')
        ->where('isApprouved = true')
		->where('comment.idPost = :post')
        ->toString();
    }

	public function getnonApprouvedComment()
    {
        return (new SelectQuery())
		->select('comment.idComment', 'comment.idUser', 'comment.idPost', 'comment.textComment','comment.isApprouved','user.pseudo as author')
        ->from('Comment')
		->leftJoin('user','User.idUser = comment.idUser')
        ->where('isApprouved = false')
		->where('comment.idPost = :post')
        ->toString();
    }
    public function approuveComment()
    {
        return (new UpdateQuery())
        ->update('comment')
        ->set('isApprouved = true')
        ->where('idComment = :idComment')
        ->toString();
    }

    public function removeComment()
    {
        return (new DeleteQuery())
        ->delete('comment')
        ->where('idComment = :idComment')
        ->toString();
    }
}
