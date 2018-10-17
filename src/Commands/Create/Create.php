<?php
namespace Commands\Create;

use Database\PDOConnector;
use Utils\ResourceLoader;

class Create
{
    private $connection;

    public function create($database)
    {
        $PDOConnector = new PDOConnector($database);

        if (!$PDOConnector->isEmpty()) {
            throw new \PDOException("The database {$database} is not empty.");
        }

        $this->connection = $PDOConnector->getConnection();
        $sql = ResourceLoader::load('schema.sql');
        return $this->connection->exec($sql);
    }
}
