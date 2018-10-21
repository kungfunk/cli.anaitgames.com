<?php
namespace Database;

use PDO;

class PDORepository
{
    protected $connection;
    protected $insertedIds = [];

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getInsertedIds()
    {
        return $this->insertedIds;
    }

    public function getLastInsertedId()
    {
        return $this->connection->lastInsertId();
    }
}
