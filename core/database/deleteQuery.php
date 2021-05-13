<?php

namespace core\database;

class DeleteQuery
{
	private $table;
	private $conditions = [];

	public function delete($table)
	{
		$this->table = $table;
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
		return "DELETE FROM " . $this->table
			. " WHERE " . implode(', ', $this->conditions);
	}
}
?>