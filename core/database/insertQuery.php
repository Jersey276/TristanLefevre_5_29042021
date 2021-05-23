<?php

namespace core\database;

class InsertQuery
{
    private $keys;
    private $values = [];
    private $table;

    public function insertInto($table)
    {
        $this->table = $table;
        return $this;
    }
    public function key()
    {
        $this->keys = implode(', ', func_get_args());
        return $this;
    }

    public function value()
    {
        array_push($this->values, " " .implode(", ", func_get_args()) . " ");
        return $this;
    }

    public function toString()
    {
        return 'INSERT INTO '. $this->table
            . ' ( ' . $this->keys. ' ) '
            . ' VALUES (' . implode(' ),( ', $this->values) . '); ';
    }
}
