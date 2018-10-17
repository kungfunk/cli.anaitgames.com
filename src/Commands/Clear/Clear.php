<?php
namespace Commands\Clear;

use Database\PDOConnector;
use Utils\ResourceLoader;

class Clear
{
    public function create($database)
    {
        $PDOConnector = new PDOConnector($database);

        if ($PDOConnector->isEmpty()) {
            throw new \PDOException("The database {$database} is empty. Nothing to clear.");
        }

        $connection = $PDOConnector->getConnection();
        $sql = ResourceLoader::load('clear.sql');
        return $connection->exec($sql);
    }
}
