<?php
namespace project\controllers;

use project\models\MusicModel;       // 音樂資料庫操作 Model
use project\models\GetMusByName;  // API 獲取 Model
use project\core\Request;         // 引入 Request 用於接收輸入
use Exception;


class InsMusicController
{
    private $musicModel;
    private $apiModel;

    public function __construct()
    {
        $this->musicModel = new MusicModel();
        $this->apiModel = new GetMusByName();
    }

    public function addMusicViaApi(Request $request): bool
    {
        try {
            // 驗證音樂名稱
            $musicName = $this->validateMusicName($request->body()["musicName"] ?? null);
            
            // 獲取音樂資料
            $spotifyResult = $this->fetchSpotifyData($musicName);
            
            // 獲取 YouTube 連結
            $YTLink = $this->fetchYouTubeLink($musicName);

            //資料存入資料庫
            $this->musicModel->InsertMusic(
                $spotifyResult['歌曲名稱'],
                $spotifyResult['演唱者'] ?? '未知演唱者',
                $YTLink
            );
            // $this->musicModel->InsertMusic(
            //     "tiktok",
            //     "Me",
            //     "https"
            // );


            echo "音樂資料已成功新增至資料庫！\n";
            return true;

        } catch (Exception $e) {
            echo "處理音樂資料時發生錯誤：" . $e->getMessage() . "\n";
            return false;
        }
    }

    private function validateMusicName(?string $musicName): string
    {
        if (empty($musicName)) {
            throw new Exception("音樂名稱是必填的。");
        }
        return $musicName;
    }

    private function fetchSpotifyData(string $musicName): array
    {
        $spotifyResults = $this->apiModel->searchAndCheckExplicit($musicName);

        if (empty($spotifyResults)) {
            throw new Exception("未找到 Spotify 資料。");
        }

        // 假設只有單筆資料情境，選取第一筆
        return $spotifyResults;
    }

    private function fetchYouTubeLink(string $songName): string
    {
        $youtubeLink = $this->apiModel->getYTLink($songName);

        if (empty($youtubeLink)) {
            throw new Exception("無法獲取 YouTube 連結。");
        }

        return $youtubeLink;
    }

}
?>
