<?php
namespace core\database;

class SelectQuery
{
    private $fields = [];
    private $conditions = [];
    private $from = [];
    private $join = [];
    private $separator = [];

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
    public function and()
    {
        $this->separator[] = 'and';
        return $this;
    }

    public function or()
    {
        $this->separator[] = 'or';
        return $this;
    }
    public function leftJoin($table, $arg)
    {
        $this->join[] = ' LEFT JOIN ' . $table . ' ON ' . $arg;
        return $this;
    }

    public function toString()
    {
        $message = 'SELECT '. implode(', ', $this->fields)
            . ' FROM ' . implode(', ', $this->from) . implode('', $this->join);

        if (!empty($separator))
        {
            $message = $message . ' WHERE ' . $conditions[0];
            for ($i = 0; $i < count($separator); $i++)
            {
                switch ($separator[$i])
                {
                    case 'and':
                        $message = $message . 'AND' . $conditions[$i++];
                        break;
                    case 'or':
                        $message = $message . 'OR' . $conditions[$i++];
                        break;
                }
            }
            return $message;
        }
        return $message . ' WHERE ' . implode(' AND ', $this->conditions);

    }
}