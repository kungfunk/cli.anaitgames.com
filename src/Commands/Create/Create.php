<?php
namespace Commands\Create;

use Database\PDOConnector;
use Utils\ResourceLoader;

class Create
{
    public function create($database)
    {
        $connection = new PDOConnector($database);
        $sql = ResourceLoader::load('schema.sql');
        return $connection->exec($sql);
    }
}
