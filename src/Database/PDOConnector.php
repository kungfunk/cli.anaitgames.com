<?php
namespace Database;

use PDO;

class PDOConnector
{
    private $driver;
    private $host;
    private $user;
    private $pass;
    private $charset;
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    public function __construct($database)
    {
        $this->driver = getenv('DB_DRIVER');
        $this->host = getenv('DB_HOST');
        $this->user = getenv('DB_USER');
        $this->pass = getenv('DB_PASS');
        $this->charset = getenv('DB_CHARSET');

        return new PDO(
            "{$this->driver}:host={$this->host};dbname={$database};charset={$this->charset}",
            $this->user,
            $this->pass,
            $this->options
        );
    }
}
