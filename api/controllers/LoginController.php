<?php

namespace project\controllers;

use Exception;
use Firebase\JWT\ExpiredException;
use project\core\Request;
use project\models\User;
use project\models\User_add;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LoginController
{

    function __construct(
        // private $exampleService = new ();

    ) {

    }
    public function demo(Request $request)
    {
        return $request->query();
    }

    public function postDemo(Request $request)
    {
        return $request->body();
    }

    public function deleteDemo(Request $request)
    {
        return 'delete completed.';
    }
    //register
    public function register(Request $request)
    {

        // $data = $request->body();
        // if (empty($data)) {
        //     $data = json_decode(file_get_contents('php://input'), true);
        // }
        $username = $request->body()['username'] ?? null;
        $email = $request->body()['email'] ?? null;
        $password = $request->body()['password'] ?? null;

        // var_dump($request->body());
        // var_dump($username, $email, $password);
        if (empty($username) || empty($email) || empty($password)) {
            return ['error' => '所有欄位都是必填的'];
        }

        // FIXME: Should hash the password
        // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        session_start();
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;

        if ($user->save()) {
            return ['success' => '註冊成功'];
        } else {
            return ['error' => '註冊失敗，請稍後再試'];
        }

    }
    //login
    public function login(Request $request)
    {
        // TODO: Remove this line
        $username = $request->body()['username'] ?? null;
        $password = $request->body()['password'] ?? null;

        if (empty($username) || empty($password)) {
            return ['error' => '所有欄位都是必填的'];
        }

        $user = User::findByUsername($username, $password);

        if (!$user) {
            return ['error' => '使用者不存在'];
        }

        $payload = [
            'sub' => $user->member_id,
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
            // 'role' => $user->role, // TODO: Add role if needed
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        return [
            'success' => true,
            'message' => '登入成功',
            'token' => $token,
        ];

    }
    // //user add
    // public function add(Request $request)
    // {

    //     // $data = $request->body();
    //     // if (empty($data)) {
    //     //     $data = json_decode(file_get_contents('php://input'), true);
    //     // }
    //     // $username = $request->body()['username'] ?? null;
    //     // $pin_num = $request->body()['pin_num'] ?? null;

    //     // // var_dump($request->body());
    //     // // var_dump($username, $email, $password);
    //     // if (empty($username) || empty($pin_num)) {
    //     //     return ['error' => '所有欄位都是必填的'];
    //     // }

    //     // $user = new User();
    //     // $user->username = $username;
    //     // $user->pin_num = $pin_num;

    //     // if ($user->save_add(int $memberId)) {
    //     //     return ['success' => '新增成功'];
    //     // } else {
    //     //     return ['error' => '新增失敗，請稍後再試'];
    //     // }
    //     $username = $request->body()['username'] ?? null;
    //     $pin_num = $request->body()['pin_num'] ?? null;
    //     $memberId = $request->body()['member_id'] ?? null; // 把主帳號 ID 也拿進來

    //     if (empty($username) || empty($pin_num) || empty($memberId)) {
    //         return ['error' => '所有欄位都是必填的'];
    //     }

    //     $user = new User();
    //     $user->username = $username;
    //     $user->pin_num = $pin_num;

    //     if ($user->save_add((int) $memberId)) {
    //         return ['success' => '新增成功'];
    //     } else {
    //         return ['error' => '新增失敗，請稍後再試'];
    //     }
    // }
    //user imformation edit
    public function Imedit(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->getHeader('Authorization'));
        try {
            $parsedToken = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (ExpiredException | Exception $e) {
            // FIXME: 401
            return ['error' => '未登入，請先登入', 'detail' => $e->getMessage()];
        }
        
        $username = $request->body()['username'] ?? null;
        $email = $request->body()['email'] ?? null;

        // var_dump($request->body());
        // var_dump($username, $email, $password);
        if (empty($username) || empty($email)) {
            return ['error' => '所有欄位都是必填的'];
        }

        // FIXME: Username should be unique and cannot be changed
        $user = new User();
        $user->member_id = $parsedToken->sub;
        $user->username = $username;
        $user->email = $email;
        // FIXME: Support password change

        if ($user->save_edit()) {
            return ['success' => '更新成功'];
        } else {
            return ['error' => '更新失敗，請稍後再試'];
        }

    }
    // //user change password
    // public function changePassword(Request $request)
    // {
    //     // if (!isset($_SESSION['user_id'])) {
    //     //     return ['error' => '未登入，請先登入'];
    //     // }
    //     // $data = $request->body();
    //     // if (empty($data)) {
    //     //     $data = json_decode(file_get_contents('php://input'), true);
    //     // }
    //     $newPassword = $request->body()['newPassword'] ?? null;
    //     $confirmPassword = $request->body()['confirmPassword'] ?? null;

    //     if (empty($newPassword) || empty($confirmPassword)) {
    //         return ['error' => '所有欄位都是必填的'];
    //     }

    //     session_start();
    //     $user = new User();
    //     $user->id = $_SESSION['user_id'];
    //     $user->newPassword = $newPassword;
    //     $user->confirmPassword = $confirmPassword;

    //     if ($user->save_change()) {
    //         return ['success' => '密碼更新成功'];
    //     } else {
    //         return ['error' => '密碼更新失敗，請稍後再試'];
    //     }

    // }
    // //user add pin login
    // public function loginPinnum(Request $request)
    // {
    //     // if (!isset($_SESSION['user_id'])) {
    //     //     return ['error' => '未登入，請先登入'];
    //     // }
    //     // $data = $request->body();
    //     // if (empty($data)) {
    //     //     $data = json_decode(file_get_contents('php://input'), true);
    //     // }
    //     $pin_num = $request->body()['pin_num'] ?? null;

    //     if (empty($pin_num)) {
    //         return ['error' => '所有欄位都是必填的'];
    //     }

    //     session_start();
    //     $user = new User();
    //     $user->id = $_SESSION['user_id'];
    //     $user->pin_num = $pin_num;

    //     if ($user->findByPin()) {
    //         return ['success' => '歡迎'];
    //     } else {
    //         return ['error' => '密碼錯誤，請稍後再試'];
    //     }

    // }

    //forget password
    public function forgetpwd(Request $request)
    {
        $email = $request->body()['email'] ?? null;

        // var_dump($request->body());
        // var_dump($username, $email, $password);
        if (empty($email)) {
            return ['error' => '所有欄位都是必填的'];
        }

        $user = User::findByEmail($email);

        if (!$user) {
            return ['error' => '使用者不存在'];
        }

        session_start();
        $_SESSION['reset_member_id'] = $user->member_id;
        $_SESSION['reset_email'] = $user->email;

        return ['success' => '驗證成功，請重設密碼'];

    }

    public function resetpwd(Request $request)
    {
        session_start();
        // $member_id = $_SESSION['reset_member_id'] ?? null;

        $newPassword = $request->body()['newPassword'] ?? null;
        $confirmPassword = $request->body()['confirmPassword'] ?? null;

        if (empty($newPassword) || empty($confirmPassword)) {
            return ['error' => '所有欄位都是必填的'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['error' => '兩次密碼輸入不一致'];
        }

        $email = $_SESSION['reset_email'] ?? null;

        $user = new User();
        $user->email = $email;
        $user->password = $newPassword;


        if ($user->save_reset()) {
            return ['success' => '密碼更新成功'];
        } else {
            return ['error' => '密碼更新失敗，請稍後再試'];
        }

    }
}