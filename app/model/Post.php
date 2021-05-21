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
    private $author;

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

    public function date()
    {
        setlocale(LC_ALL, 'fr_FR@euro', 'fr_FR.utf-8', 'fr_FR');
        return strftime("%A %e %B %Y", strtotime($this->createdAt));
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
    public function setAuthor($author)
    {
        $this->author = $author;
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
    public function getauthor()
    {
        return $this->author;
    }
}
