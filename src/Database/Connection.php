<?php

namespace Sowe\Database;

use Sowe\Database\Exceptions\ConnectionException;

class Connection extends \mysqli
{
    public function __construct($host, $user, $password, $database, $charset = "utf8", $persistent = false)
    {
        if($persistent){
            $host = "p:" . $host;
        }
        
        @parent::__construct($host, $user, $password, $database);

        if ($this->connect_errno) {
            throw new ConnectionException("Database connection error: ".  $this->connect_error);
        }

        if (!$this->set_charset($charset)) {
            throw new ConnectionException("Unnable to set database charset: ".  $this->error);
        }
    }

    public function __destruct()
    {
        @$this->close();
    }

    public function secure_query(string $query, array $params = [])
    {
        return new Query($this, $query, $params);
    }
}
