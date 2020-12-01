<?php

namespace Sowe\Database;

use Sowe\Database\Exceptions\QueryException;

class Query
{
    private $database;
    private $query;
    private $result;

    public function __construct(Connection $database, string $query, array $params = [])
    {
        $this->database = $database;
        $this->query = $this->parse_params($query, $params);
    }

    private function parse_params($query, $params)
    {
        if(sizeof($params)){
            foreach($params as $key => $value){
                if(is_bool($value)){
                    $value = "b'" . ($value ? 1 : 0) . "'";
                }else{
                    $value = "'" . $this->database->real_escape_string($value) . "'";
                }
                $query = str_replace(
                    $key,
                    $value,
                    $query
                );
            }
        }
        return $query;
    }

    public function run(){
        $result = $this->database->query($this->query);
        if(!$result){
            throw new QueryException("SQL Error: ". $this->database->errno .":". $this->database->error . " (Query: ". $this->query .")");
        }
        $this->result = $result;
        return $this;
    }

    public function num_rows()
    {
        return $this->database->affected_rows;
    }

    public function id()
    {
        return $this->database->insert_id;
    }

    public function fetchOne()
    {
        return $this->result->fetch_assoc();
    }

    public function fetchAll()
    {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    public function __toString()
    {
        return $this->query;
    }
}
