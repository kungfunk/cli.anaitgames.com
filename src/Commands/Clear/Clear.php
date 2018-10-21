<?php
namespace Commands\Clear;

use Database\PDOConnectorFactory;
use Utils\ResourceLoader;

class Clear
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function clear()
    {
        $connection = PDOConnectorFactory::getConnection($this->database);

        if (PDOConnectorFactory::isEmpty($connection)) {
            throw new \PDOException("The database {$this->database} is empty. Nothing to clear.");
        }

        return $connection->exec(ResourceLoader::load('clear.sql'));
    }
}
