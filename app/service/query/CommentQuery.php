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
}
