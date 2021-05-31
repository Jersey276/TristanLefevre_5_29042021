<?php

namespace app\model;

use core\database\databaseManager;
use core\database\selectQuery;
use core\model\AbstractModel;

class Post extends AbstractModel
{
    private $idPost;
    private $titlePost;
    private $chapoPost;
    private $contentPost;
    private $createdAt;
    private $modifiedAt;
    private $author;
    private $nbComToApprouve;

    public function getDisplayUrl()
    {
        return "/post/" . $this->idPost;
    }
    public function getEditUrl()
    {
        return "/admin/post/" . $this->idPost;
    }
    public function getRemoveUrl()
    {
        return "/admin/post/remove/" . $this->idPost;
    }

    public function getCommentUrl()
    {
        return "/admin/post/" . $this->idPost. "/comment";
    }

    public function newDate()
    {
        return strftime("%A %d %B %G à %H:%I", strtotime($this->createdAt));
    }

    public function updateDate()
    {
        return strftime("%A %d %B %G à %H:%I", strtotime($this->modifiedAt));
    }

    //setter
    public function setidPost($idPost)
    {
        $this->idPost = $idPost;
    }
    public function settitlePost($titlePost)
    {
        $this->titlePost = $titlePost;
    }
    public function setchapoPost($chapoPost)
    {
        $this->chapoPost = $chapoPost;
    }
    public function setcontentPost($contentPost)
    {
        $this->contentPost = $contentPost;
    }
    public function setcreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    public function setnbComToApprouve($nbComToApprouve)
    {
        $this->nbComToApprouve = $nbComToApprouve;
    }

    //getter
    public function getidPost()
    {
        return $this->idPost;
    }
    public function gettitlePost()
    {
        return $this->titlePost;
    }
    public function getchapoPost()
    {
        return $this->chapoPost;
    }
    public function getcontentPost()
    {
        return $this->contentPost;
    }
    public function getcreatedAt()
    {
        return $this->createdAt;
    }
    public function getmodifiedAt()
    {
        return $this->modifiedAt;
    }
    public function getauthor()
    {
        return $this->author;
    }
    public function getnbComToApprouve()
    {
        return $this->nbComToApprouve;
    }
}
