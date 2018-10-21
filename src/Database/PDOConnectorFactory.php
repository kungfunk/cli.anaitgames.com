<?php
namespace Database;

use PDO;

class PDOConnectorFactory
{
    private const OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    public static function getConnection($database): PDO
    {
        $driver = getenv('DB_DRIVER');
        $host = getenv('DB_HOST');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $charset = getenv('DB_CHARSET');

        return new PDO(
            "{$driver}:host={$host};dbname={$database};charset={$charset}",
            $user,
            $pass,
            self::OPTIONS
        );
    }

    public static function isEmpty(PDO $connection): bool {
        $checkSql = "SHOW TABLES";
        $statement = $connection->prepare($checkSql);
        $statement->execute();
        $tables = $statement->fetchAll(PDO::FETCH_NUM);

        return !(bool) count($tables);
    }
}
