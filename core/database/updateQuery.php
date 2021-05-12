<?php

UPDATE table
SET colonne_1 = 'valeur 1', colonne_2 = 'valeur 2', colonne_3 = 'valeur 3'
WHERE condition

namespace core\database;

class updateQuery()
{
	private $table;
	private $set = [];
	private $conditions = [];

	public function update($table)
	{
		return $this;
	}

	public function set()
	{
		foreach(func_get_args() as $arg)
		{
			$this->set[] = $arg;
		}
		return $this;
	}

	public function where()
	{
		foreach(func_get_args() as $arg)
		{
			$this->conditions[] = $arg;
		}
		return $this;
	}

	public function toString()
	{
		return 'UPDATE ' . $table
			. ' SET ' . implode(', ', $set)
			. ' WHERE ' . implode(',', $conditions);
	}
}
?>