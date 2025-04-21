<?php

namespace project\controllers;

use Exception;
use project\core\Request;
use project\models\SubUser;
use project\controllers\Controller;

class SubUserController extends Controller
{
    public function create(Request $request)
    {
        $parsed_token = $this->verifyToken($request);
        if (!$parsed_token) {
            return ['error' => '未登入，請先登入'];
        }

        $username = $request->body()['username'] ?? null;
        $pin_num = $request->body()['pin_num'] ?? null;

        if (empty($username)) return ['error' => '使用者名稱是必填的'];
        if (empty($pin_num)) return ['error' => 'PIN 碼是必填的'];
        if (!preg_match('/^\d{4}$/', $pin_num)) return ['error' => 'PIN 碼必須是 4 位數字'];

        $sub_user = new SubUser();
        $existing_user = $sub_user->findByUsername($username, $parsed_token->sub);
        if ($existing_user) {
            return ['error' => '此使用者名稱已被註冊'];
        }

        // TODO: Hash the PIN number
        // $hashed_pin_num = password_hash($pin_num, PASSWORD_BCRYPT);

        $sub_user->member_id = $parsed_token->sub;
        $sub_user->username = $username;
        $sub_user->pin_num = $pin_num;

        try {
            $sub_user->save();
            return ['success' => '註冊成功'];
        } catch (Exception $e) {
            return ['error' => '註冊失敗，請稍後再試', 'detail' => $e->getMessage()];
        }
    }

    public function list(Request $request)
    {
        $parsed_token = $this->verifyToken($request);
        if (!$parsed_token) {
            return ['error' => '未登入，請先登入'];
        }
        
        try {
            $sub_user = new SubUser();
            $sub_users = $sub_user->findByMemberId($parsed_token->sub);
            return ['data' => $sub_users];
        } catch (Exception $e) {
            return ['error' => '獲取列表失敗', 'detail' => $e->getMessage()];
        }
    }

    public function get(Request $request)
    {
        $parsed_token = $this->verifyToken($request);
        if (!$parsed_token) {
            return ['error' => '未登入，請先登入'];
        }

        $sub_user_id = $request->getParam('id');

        $sub_user = new SubUser();
        $current_sub_user = $sub_user->findById($sub_user_id, $parsed_token->sub);

        if (!$current_sub_user) {
            return ['error' => '找不到使用者'];
        }
        
        return ['data' => $current_sub_user];
    }

    public function update(Request $request)
    {
        $parsed_token = $this->verifyToken($request);
        if (!$parsed_token) {
            return ['error' => '未登入，請先登入'];
        }

        $sub_user_id = $request->getParam('id');

        $sub_user = new SubUser();
        $current_sub_user = $sub_user->findById($sub_user_id, $parsed_token->sub);

        if (!$current_sub_user) {
            return ['error' => '找不到使用者'];
        }

        $username = $request->body()['username'] ?? null;
        $pin_num = $request->body()['pin_num'] ?? null;
        $preferences = $request->body()['preferences'] ?? null;

        if (!empty($pin_num) && !preg_match('/^\d{4}$/', $pin_num)) {
            return ['error' => 'PIN 碼必須是 4 位數字'];
        }

        if ($username) {
            if ($username !== $current_sub_user->username) {
                $existing_sub_user = $sub_user->findByUsername($username, $parsed_token->sub);
                if ($existing_sub_user) {
                    return ['error' => '此使用者名稱已被註冊'];
                }
            }
        }

        // TODO: Hash the PIN number
        // $hashed_pin_num = password_hash($pin_num, PASSWORD_BCRYPT);

        $sub_user->sub_member_id = $sub_user_id;
        $sub_user->username = $username;
        $sub_user->pin_num = $pin_num;
        $sub_user->preferences = json_encode($preferences);

        try {
            $sub_user->update();
            return ['success' => '更新成功'];
        } catch (Exception $e) {
            return ['error' => '更新失敗', 'detail' => $e->getMessage()];
        }
    }

    public function delete(Request $request)
    {
        $parsed_token = $this->verifyToken($request);
        if (!$parsed_token) {
            return ['error' => '未登入，請先登入'];
        }

        $sub_user_id = $request->getParam('id');

        $sub_user = new SubUser();
        $current_sub_user = $sub_user->findById($sub_user_id, $parsed_token->sub);

        if (!$current_sub_user) {
            return ['error' => '找不到使用者'];
        }

        try {
            $sub_user->delete($sub_user_id, $parsed_token->sub);
            return ['success' => '刪除成功'];
        } catch (Exception $e) {
            return ['error' => '刪除失敗', 'detail' => $e->getMessage()];
        }
    }
}

