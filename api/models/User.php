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
    public $member_id = '';
    public $username = '';
    public $email = '';
    public $password = '';
    public $verified_at = null;

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

    //user information edit
    // FIXME: Rename to update
    public function save_edit()
    {
        $db = Database::getConnection();

        $fields = [];
        $params = [':member_id' => $this->member_id];

        if (!empty($this->username)) {
            $fields[] = "Username = :username";
            $params[':username'] = $this->username;
        }

        if (!empty($this->email)) {
            $fields[] = "Email = :email";
            $params[':email'] = $this->email;
        }

        if (!empty($this->verified_at)) {
            // FIXME: Use lowercase for all column names
            $fields[] = "Verified_At = :verified_at";
            $params[':verified_at'] = $this->verified_at;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE member SET " . implode(', ', $fields) . " WHERE Member_Id = :member_id";
        $stmt = $db->prepare($sql);

        return $stmt->execute($params);
    }


    //user change password
    public function save_change()
    {
        $db = Database::getConnection();

        $fields = [];
        $params = [':member_id' => $this->member_id];

        if (!empty($this->password)) {
            $fields[] = "Password = :password";
            $params[':password'] = $this->password;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE member SET " . implode(', ', $fields) . " WHERE Member_Id = :member_id";
        $stmt = $db->prepare($sql);

        return $stmt->execute($params);
        // $sql = "UPDATE member SET Password = :password WHERE Member_Id = :member_id";
        // $stmt = $db->prepare($sql);

        // $stmt->bindParam(':member_id', $this->member_id);
        // $stmt->bindParam(':password', $this->password);

        // return $stmt->execute();
    }

    public static function findById(string $member_id)
    {
        $db = Database::getConnection();

        $sql = "SELECT * FROM member WHERE Member_Id = :member_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':member_id', $member_id);
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