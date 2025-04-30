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
            $url = $music[0]["Music_Url"];
            $music[0]["Music_Url"] = base64_encode($url);

            if (!$music) {
                return ['error' => '找不到指定的音樂'];
            }

            return ['success' => '找到音樂', 'music' => $music];
        } catch (Exception $e) {
            return ['error' => '查詢失敗', 'message' => $e->getMessage()];
        }
    }
}
?>