<?php
namespace project\models;

use app\core\Model;
use project\core\Database;
use app\core\Application;
use PDO;
use PDOException;

class User
{
    private static $table = 'member';
    public $username = '';
    public $email = '';
    public $password = '';

    //register
    public function save()
    {
        $db = Database::getConnection();

        $sql = "INSERT INTO member (Username, Email, Password) VALUES (:username, :email, :password)";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        return $stmt->execute();
    }

    //login
    public static function findByUsername(string $username, string $password)
    {
        // $db = Database::getConnection();
        // $sql = "SELECT * FROM member WHERE Username = :username and Password = :password";
        // $stmt = $db->prepare($sql);

        // $stmt->bindParam(':username', $username);
        // $stmt->bindParam(':password', $password);

        // return $stmt->execute();
        $db = Database::getConnection();
        $sql = "SELECT * FROM member WHERE Username = :username and Password = :password";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['Password'] === $password) {
            $user = new User();
            $user->member_id = $row['Member_Id'];
            $user->username = $row['Username'];
            $user->email = $row['Email'];

            return $user;
        }
        return null;
    }

    //user imformation edit
    public function save_edit()
    {
        $db = Database::getConnection();

        $sql = "UPDATE member SET Username = :username, Email = :email WHERE Member_Id = :member_id";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':member_id', $this->member_id);

        return $stmt->execute();
    }

    // //user change password
    // public function save_change()
    // {
    //     $db = Database::getConnection();

    //     $sql = "UPDATE member SET Password = :password WHERE Username = :username";
    //     $stmt = $db->prepare($sql);

    //     $stmt->bindParam(':username', $this->username);
    //     $stmt->bindParam(':password', $this->password);

    //     return $stmt->execute();
    // }

    //user forget password
    public static function findByEmail(string $email)
    {
        $db = Database::getConnection();

        $sql = "SELECT * FROM member WHERE Email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $user = new User();
            $user->member_id = $row['Member_Id'];
            $user->username = $row['Username'];
            $user->email = $row['Email'];
            return $user;
        }

        return null;
    }

    public function save_reset()
    {
        $db = Database::getConnection();

        $sql = "UPDATE member SET Password = :password WHERE Email = :email";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        return $stmt->execute();
    }
}