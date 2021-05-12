<?php

namespace core\database;

class deleteQuery
{
	private $table;
	private $conditions = [];

	public function delete($table)
	{
		$this->$table = $table;
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
		return "DELETE " . $table
			. " WHERE " . implode(', ', $conditions);
	}
}
?>