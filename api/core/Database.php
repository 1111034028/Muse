<?php
namespace project\core;

use PDO;
use PDOException;

class Database {

    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            try {
                self::$connection = new PDO(
                    'mysql:host=localhost;dbname=musicdb;charset=utf8',
                    'root',
                    ''
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