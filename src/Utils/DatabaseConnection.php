<?php

namespace App\Utils;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static $pdo = null;

    public static function getPDO()
    {
        if (self::$pdo === null) {
            $host = DB_HOST;
            $port = DB_PORT;
            $database = DB_DATABASE;
            $username = DB_USERNAME;
            $password = DB_PASSWORD;

            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$database";
                self::$pdo = new PDO($dsn, $username, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Database connection failed: " . $e->getMessage() . PHP_EOL;
                exit(1);
            }
        }

        return self::$pdo;
    }

    public static function databaseExists($database)
    {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $result = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Error checking database existence: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    public static function createDatabase($database)
    {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
            echo "Database '$database' created successfully!" . PHP_EOL;
        } catch (PDOException $e) {
            echo "Error creating database: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    public static function selectDatabase($database)
    {
        try {
            self::$pdo->exec("USE $database");
        } catch (PDOException $e) {
            echo "Error selecting database: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }
}
