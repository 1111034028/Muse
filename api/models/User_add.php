<?php
namespace project\models;

use app\core\Model;
use project\core\Database;
use app\core\Application;
use PDO;
use PDOException;

class User_add{
    private static $table = 'member_add';
    public $username = '';
    public $pin_num = '';
    public $member_id = '';

    //user add
    public function save_add(int $memberId): bool
    {
        $db = Database::getConnection();

        $sql = "INSERT INTO member_add (Member_Id, Username, Pin_Num) VALUES (:member_id, :username, :pin_num)";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':member_id', $memberId);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':pin_num', $this->pin_num);

        return $stmt->execute();
    }

    //user add pin login
    public function findByPin(string $pin_num)
    {
        $db = Database::getConnection();

        $sql = "SELECT * FROM member_add WHERE Pin_Num = :pin_num";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':pin_num', $pin_num);

        return $stmt->execute();
    }
}
?>