<?php
namespace project\models;
use PDO;
use Exception;
use project\core\Database;
use project\DataBase\DatabaseHelper;

// include '../Database/DatabaseHelper.php';


// $test = new MusicModel();
// echo $test->InsertMusic("a", "b", "c");
// echo "Hello World";
// return 0;
class MusicModel
{
    public $music_id = "";
    public $music_name = "";
    public $artist = "";
    public $musicURL = "";
    public $Album = "";

    public function findMusicByName(string $music_Name)
    {
        $sql = "SELECT Music_ID, Music_Name, Artist, Music_Url
                FROM Music 
                WHERE Music_Name = :music_Name";
        $params = [':music_Name' => $music_Name];

        $result = DatabaseHelper::getData($sql, $params);

        if (empty($result)) {
            return null; // 沒有找到符合條件的音樂
        }

        return $result[0]; // 返回第一筆數據（匹配的音樂）
    }

    public function findMusicByID(string $music_ID)
    {
        $sql = "SELECT Music_ID, Music_Name, Artist, Music_Url
                FROM Music 
                WHERE Music_ID = :music_ID";
        $params = [':music_ID' => $music_ID];

        $result = DatabaseHelper::getData($sql, $params);

        if (empty($result)) {
            return null; // 沒有找到符合條件的音樂
        }

        return $result; // 返回第一筆數據（匹配的音樂）
    }

    public function getTop3()
    {
        $sql = "SELECT Music_ID, Music_Name, Artist, Music_Url 
                FROM Music 
                LIMIT 3";
        
        return DatabaseHelper::getData($sql); // 無需參數
    }

    public function getFrom4To6()
    {
        $sql = "SELECT Music_ID, Music_Name, Artist, Music_Url 
                FROM Music 
                LIMIT 3 OFFSET 3";

        return DatabaseHelper::getData($sql); // 無需參數
    }

    // 獲取所有音樂清單
    public function getAllMusic()
    {
        $sql = "SELECT * FROM Music";

        try {
            return DatabaseHelper::getData($sql); // 獲取所有音樂資料
        } catch (Exception $e) {
            throw new Exception("獲取所有音樂時發生錯誤：" . $e->getMessage());
        }
    }
    public function main(){
        InsertMusic("a", "b", "c");
        print_r("Hello world");
    }
    // 新增音樂到資料庫
    public function InsertMusic($musicName, $artist, $musicURL)
    {
        $sql = "INSERT INTO Music (Music_Name, Artist, Music_Url) 
                VALUES (:music_name, :artist, :music_url)";
        $params = [
            ':music_name' => $musicName,
            ':artist' => $artist,
            ':music_url' => $musicURL
            ];
    
        try {
            DatabaseHelper::getData($sql, $params);
            echo "音樂新增成功！名稱：$musicName, 歌手：$artist\n";
            return true;
        } catch (Exception $e) {
            throw new Exception("新增音樂時發生錯誤：" . $e->getMessage());
        }
    }
}
?>

