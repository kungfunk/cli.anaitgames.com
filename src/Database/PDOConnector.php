<?php
namespace Database;

use PDO;

class PDOConnector
{
    private const OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    private $pdo;

    public function __construct($database)
    {
        $driver = getenv('DB_DRIVER');
        $host = getenv('DB_HOST');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $charset = getenv('DB_CHARSET');

        $this->pdo = new PDO(
            "{$driver}:host={$host};dbname={$database};charset={$charset}",
            $user,
            $pass,
            self::OPTIONS
        );
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }

    public function isEmpty(): bool {
        $checkSql = "SHOW TABLES";
        $statement = $this->pdo->prepare($checkSql);
        $statement->execute();
        $tables = $statement->fetchAll(PDO::FETCH_NUM);

        return !(bool) count($tables);
    }
}
