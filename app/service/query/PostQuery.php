<?php

namespace app\service\query;

use core\database\SelectQuery;

class PostQuery
{
	/**
	 * generate query for collect all article
	 * @return string query
	 */
	function getAllArticles()
	{
		return (new SelectQuery)
			->select('post.idPost','post.titlePost','post.chapoPost','post.createdAt','user.pseudo as author')
			->from('post')
			->leftJoin('user','user.idUser = post.idUser')
			->where(1)
			->toString();
	}

	/**
	 * get article by id
	 * @param int id of article
	 * @return string query
	 */
	function getArticle($id)
	{
		return (new SelectQuery)
			->select('post.idPost','post.titlePost','post.chapoPost','post.contentPost','post.createdAt','user.pseudo as author')
			->from('post')
			->leftJoin('user','user.idUser = post.idUser')
			->where("post.idPost =". $id)
			->toString();
	}
}