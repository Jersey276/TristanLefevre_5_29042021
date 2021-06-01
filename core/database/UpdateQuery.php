<?php

namespace core\database;

class UpdateQuery
{
    private $table;
    private $set = [];
    private $conditions = [];

    public function update($table)
    {
        $this->table = $table;
        return $this;
    }

    public function set()
    {
        foreach (func_get_args() as $arg) {
            $this->set[] = $arg;
        }
        return $this;
    }

    public function where()
    {
        foreach (func_get_args() as $arg) {
            $this->conditions[] = $arg;
        }
        return $this;
    }

    public function toString()
    {
        return 'UPDATE ' . $this->table
            . ' SET ' . implode(', ', $this->set)
            . ' WHERE ' . implode(',', $this->conditions);
    }
}
