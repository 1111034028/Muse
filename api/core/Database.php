<?php
namespace project\core;

use PDO;
use PDOException;

class Database
{

    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            try {
                self::$connection = new PDO(
                    sprintf(
                        'mysql:host=%s;port=%s;dbname=%s;charset=utf8',
                        $_ENV['DB_HOST'],
                        $_ENV['DB_PORT'],
                        $_ENV['DB_DATABASE'],
                    ),
                    $_ENV['DB_USERNAME'],
                    $_ENV['DB_PASSWORD'],
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('資料庫連線失敗：' . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>