<?php
namespace core\request;

class requestManager
{
	/**
	 * get all post variable
	 * @param array list of all conditions about a post element. each conditions is build like this : ["name_var" => "conditions1|conditions2"]
	 * @return array of all post variable checked and valided
	 * @return false if a var is problematic 
	 */
	function getPost($conditionsForm) {
		$posts = filter_input_array(INPUT_POST);
		$postKeys = array_keys($posts);

		foreach ($postKeys as $postKey)
		{
			if (!array_key_exists($postKey, $conditionsForm)) {
				return false;
			}
			$conditions = explode("|", $conditionsForm[$postKey]);
			$tempPost = $posts[$postKey];
			foreach($conditions as $condition) {
				$tempPost = $this->checkAndPreparePost($posts[$postKey],$condition);
				if (!$tempPost) {
					return false;
				}
			}
			$post[$postKey] = $tempPost;
		}
		return $posts;
	}
	/**
	 * Verify validity of a data and prepare it
	 * @param mixed variable to check
	 * @param string condition use to check variable
	 * @param mixed Optionnal - argument use with conditions
	 */
	private function checkAndPreparePost($data, $conditions, $args = null) {
		switch ($conditions)
		{
			//check email and clean it
			case "email" :
				if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
					return filter_var($data, FILTER_SANITIZE_EMAIL);
				}
				return false;
				break;
			//clean string of all unusual characters and injection try
			case "string" :
				return htmlentities($data);
				break;
		}
	}
}