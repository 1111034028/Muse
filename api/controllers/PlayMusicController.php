<?php

namespace project\controllers;

use Exception;
use project\core\Request;
use project\models\MusicModel; // 假設包含 findMusicByID 方法的 Model

class PlayMusicController
{
    private $musicModel;

    public function __construct() {
        $this->musicModel = new MusicModel(); // 建立 MusicModel 實例
    }

    // 根據音樂 ID 查詢特定音樂
    public function getMusicByName(Request $request) {

        $musicName = $request->body()['musicName'] ?? null;

        try {
            $music = $this->musicModel->findMusicByName($musicName);

            if (!$music) {
                return ['error' => '找不到指定的音樂'];
            }

            return ['success' => '找到音樂', 'music' => $music];
        } catch (Exception $e) {
            return ['error' => '查詢失敗', 'message' => $e->getMessage()];
        }
    }

    // 獲取熱門音樂 (前 3 筆資料)
    public function getTop3Music() {
        try {
            $music = $this->musicModel->getTop3();

            return ['success' => '獲取熱門音樂成功', 'music' => $music];
        } catch (Exception $e) {
            return ['error' => '獲取熱門音樂失敗', 'message' => $e->getMessage()];
        }
    }

    // 獲取個性推薦音樂 (第 4 到第 6 筆資料)
    public function getRecommendedMusic() {
        try {
            $music = $this->musicModel->getFrom4To6();

            return ['success' => '獲取推薦音樂成功', 'music' => $music];
        } catch (Exception $e) {
            return ['error' => '獲取推薦音樂失敗', 'message' => $e->getMessage()];
        }
    }

    public function playMusic(Request $request) {

        $musicID = $request->body()['musicId'] ?? null;

        try {
            $music = $this->musicModel->findMusicByID($musicID);

           
            $music[0]["Music_Url"] = base64_encode($music[0]["Music_Url"]);
            $url = $music[0]["Music_Url"];
            if (!$music) {
                return ['error' => '找不到指定的音樂'];
            }
            // API URL
            $APIurl = "http://127.0.0.1:5000/predict";

            // POST 資料
            $data = [
                "base64_link" => $url
            ];

            // 初始化 cURL
            $ch = curl_init($APIurl);

            // 設定 cURL 選項
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);

            // 執行請求並獲取回應
            $response = curl_exec($ch);
            // 檢查是否有錯誤
            if (curl_errno($ch)) {
                echo "錯誤：" . curl_error($ch);
            } else {
                // 解析 JSON 回應為關聯陣列
                $response_data = json_decode($response, true);

                // 確保回應成功解析並包含目標鍵值
                if (is_array($response_data) && isset($response_data["music_routur"])) {
                    $music[0]["Music_Url"] = $response_data["music_routur"];
                } else {
                    echo "回應格式不正確或未包含 'music_routur'";
                }
            }
            // 關閉 cURL
            curl_close($ch);
            return ['success' => '找到音樂', 'music' => $music];
        } catch (Exception $e) {
            return ['error' => '查詢失敗', 'message' => $e->getMessage()];
        }
    }
}
?>