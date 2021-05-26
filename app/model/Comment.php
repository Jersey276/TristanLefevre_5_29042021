<?php

namespace app\model;

use core\model\AbstractModel;

class Comment extends AbstractModel
{
    private $idComment;
    private $idPost;
    private $idUser;
    private $textComment;
    private $isApprouved;
    private $author;

	//special function
	public function getapprouvedUrl()
	{
		return "/admin/post/" . $this->idPost . '/comment/'. $this->idComment .'/approuve';
	}

	public function getRemoveUrl()
	{
		return "/admin/post/" . $this->idPost . '/comment/'. $this->idComment .'/remove';
	}

	//getter
    public function getidComment()
    {
        return $this->idComment;
    }
    public function getidPost()
    {
        return $this->idPost;
    }
    public function getidUser()
    {
        return $this->idUser;
    }
    public function gettextComment()
    {
        return $this->textComment;
    }
    public function getisApprouved()
    {
        return $this->isApprouved;
    }
    public function getauthor()
    {
        return $this->author;
    }

	// setter
    public function setidComment($idComment)
    {
        $this->idComment = $idComment;
    }
    public function setidPost($idPost)
    {
        $this->idPost = $idPost;
    }
    public function setidUser($idUser)
    {
        $this->idUser = $idUser;
    }
    public function settextComment($textComment)
    {
        $this->textComment = $textComment;
    }
    public function setisApprouved($isApprouved)
    {
        $this->isApprouved = $isApprouved;
    }
    public function setauthor($author)
    {
        $this->author = $author;
    }
}
