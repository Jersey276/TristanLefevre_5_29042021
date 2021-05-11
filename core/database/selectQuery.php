<?php
namespace core\database;

class SelectQuery
{
    private $fields = [];
    private $conditions = [];
    private $from = [];
    private $join = [];

    public function select()
    {
        $this->fields = func_get_args();
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

    public function from($table, $alias = null)
    {
        if(is_null($alias))
        {
            $this->from[] = $table;
        } else
        {
            $this->from[] = "$table AS $alias";
        }
        return $this;
    }

    public function leftJoin($table, $arg)
    {
        $this->join[] = ' LEFT JOIN ' . $table . ' ON ' . $arg;
        return $this;
    }

    public function toString()
    {
        return 'SELECT '. implode(', ', $this->fields)
            . ' FROM ' . implode(', ', $this->from) 
            . implode('', $this->join) 
            . ' WHERE ' . implode(' AND ', $this->conditions);

    }
}