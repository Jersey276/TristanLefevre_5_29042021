<?php

namespace core\model;

abstract class AbstractModel
{
	public function hydrate($datas)
	{
		foreach ($datas as $key => $data)
		{
			$method = 'set'.$key;

			if (method_exists($this, $method))
			{
				$this->$method($data);
			}
		}
		return $this;
	}
}