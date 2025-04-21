<?php

namespace project\controllers;

use Exception;
use Firebase\JWT\ExpiredException;
use project\core\Request;
use project\Mailer\Mailer;
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

    public function sendVerificationEmail(Request $request)
    {
        $email = $request->body()['email'] ?? null;
        $redirect_url = $request->body()['redirect_url'] ?? 'http://127.0.0.1:5501/frontend/public/index.html';

        if (empty($email)) {
            return ['error' => '所有欄位都是必填的'];
        }

        $user = User::findByEmail($email);

        if (!$user) {
            return ['error' => '使用者不存在'];
        }

        $mailer = new Mailer();
        $to = $email;
        $subject = '歡迎註冊';

        $payload = [
            'sub' => $user->member_id,
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        $link = sprintf('%s/api/Login/verify?token=%s&redirect_url=%s', $_ENV['APP_URL'], $token, $redirect_url);

        $body = sprintf(
            '<html><body><h1>%s</h1><p>%s：<a href="%s">%s</a></p></body></html>',
            '歡迎！',
            '請點擊以下連結以完成驗證',
            $link,
            $link
        );

        $mail_sent = $mailer->sendEmail($to, $subject, $body);

        if ($mail_sent) {
            return ['success' => '已寄送驗證郵件！'];
        } else {
            return ['error' => '寄送驗證郵件失敗，請稍後再試'];
        }
    }

    public function verify(Request $request)
    {
        $token = $request->query()['token'] ?? null;
        $redirect_url = $request->query()['redirect_url'] ?? 'http://127.0.0.1:5501/frontend/public/index.html';

        if (empty($token)) {
            return ['error' => '無效的驗證連結'];
        }

        try {
            $parsedToken = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

            $user = new User();
            $user->member_id = $parsedToken->sub;
            $user->verified_at = date('Y-m-d H:i:s', time());

            if ($user->save_edit()) {
                header("Location: $redirect_url");
                exit;
                // return ['success' => '驗證成功'];
            } else {
                return ['error' => '驗證失敗，請稍後再試'];
            }
        } catch (ExpiredException | Exception $e) {
            // FIXME: 401
            return ['error' => '無效的驗證連結', 'detail' => $e->getMessage()];
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
    //user information edit
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
        $user = User::findById($parsedToken->sub);
        $user2 = User::findByEmail($email);

        if ($user2 && $user2->email !== $user->email) {
            return ['error' => '信箱已被使用'];
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
    //user change password
    public function changePassword(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->getHeader('Authorization'));
        try {
            $parsedToken = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (ExpiredException | Exception $e) {
            return ['error' => '未登入，請先登入', 'detail' => $e->getMessage()];
        }

        $newPassword = $request->body()['newPassword'] ?? null;
        $confirmPassword = $request->body()['confirmPassword'] ?? null;

        if (empty($newPassword) || empty($confirmPassword)) {
            return ['error' => '所有欄位都是必填的'];
        }

        $user = new User();
        $user->member_id = $parsedToken->sub;
        $user->password = $newPassword;

        if ($user->save_change()) {
            return ['success' => '密碼更新成功'];
        } else {
            return ['error' => '密碼更新失敗，請稍後再試'];
        }

    }

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