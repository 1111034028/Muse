<?php
namespace project\DataBase;

use PDO;
use project\core\DataBase;
use Exception;
// include "../core/Database.php";
// $sql = "SELECT Artist FROM music";

// print_r(DatabaseHelper::getData($sql));

class DatabaseHelper{
    public static function getData(string $sql, array $params = []): array{
        try {
            // 獲取資料庫連線
            $db = Database::getConnection();

            // 預備並執行 SQL 語句
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            // 返回查詢結果
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // 捕捉錯誤並返回
            return ['error' => '資料庫操作失敗', 'message' => $e->getMessage()];
        }
    }
    
    public static function executeQuery(string $sql, array $params = []): bool{
        try {
            $db = Database::getConnection(); // 獲取資料庫連線
            $stmt = $db->prepare($sql);      // 預備 SQL 語句

            // 綁定參數
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            // 執行 SQL 並返回執行結果
            return $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("資料庫執行錯誤：" . $e->getMessage());
        }
    }
}

?>