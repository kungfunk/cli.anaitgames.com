<?php
namespace Database;

use PDO;

class PDOConnectorFactory
{
    private const OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    private static $connection;

    public static function getConnection($database): PDO
    {
        if (is_null(self::$connection))
        {
            $driver = getenv('DB_DRIVER');
            $host = getenv('DB_HOST');
            $user = getenv('DB_USER');
            $pass = getenv('DB_PASS');
            $charset = getenv('DB_CHARSET');

            self::$connection = new PDO(
                "{$driver}:host={$host};dbname={$database};charset={$charset}",
                $user,
                $pass,
                self::OPTIONS
            );
        }
        return self::$connection;
    }

    public static function isEmpty(): bool {
        $checkSql = "SHOW TABLES";
        $statement = self::$connection->prepare($checkSql);
        $statement->execute();
        $tables = $statement->fetchAll(PDO::FETCH_NUM);

        return !(bool) count($tables);
    }
}
