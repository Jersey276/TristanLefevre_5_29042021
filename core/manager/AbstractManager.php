<?php

namespace core\manager;

use core\request\RequestManager;
use core\database\DatabaseManager;

abstract class AbstractManager
{
    protected $request;
    protected $database;

    public function __construct($database)
    {
        $this->request = new RequestManager();
        $this->database = $database;
    }

    /**
     * generate a CSRF Long Token
     * @param string function
     * @return array token info (his name and his key)
     */
    public function askNewCSRFLongToken($function)
    {
        return $this->request->newCSRFLongToken($function);
    }
}
