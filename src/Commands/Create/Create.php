<?php
namespace Commands\Create;

use Database\PDOConnectorFactory;
use Utils\ResourceLoader;

class Create
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function create()
    {
        $connection = PDOConnectorFactory::getConnection($this->database);

        if (!PDOConnectorFactory::isEmpty()) {
            throw new \PDOException("The database {$this->database} is not empty.");
        }

        return $connection->exec(ResourceLoader::load('schema.sql'));
    }
}
