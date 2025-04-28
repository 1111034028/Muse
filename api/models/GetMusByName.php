<?php
namespace project\models;

use Exception;
use GuzzleHttp\Client;

require 'vendor/autoload.php'; // 引入 Composer 的自動載入

class GetMusByName {
    private $clientID = "e3441c6a5c2e4d96948efe428eedc915";
    private $clientSecret = "599d9149c7f040c59c595f529c529e5b";
    private $youtubeApiKey = "AIzaSyBJprZFE2_PEyc5MIxSDCSDcgziFZqVHXI";

    // 獲取 OAuth Token
    private function getToken() {
        $url = "https://accounts.spotify.com/api/token";
        $authHeader = base64_encode("$this->clientID:$this->clientSecret"); // 修正此處

        $options = [
            "http" => [
                "header" => "Authorization: Basic $authHeader\r\nContent-Type: application/x-www-form-urlencoded",
                "method" => "POST",
                "content" => http_build_query(["grant_type" => "client_credentials"])
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $tokenData = json_decode($response, true);

        if (!isset($tokenData["access_token"])) {
            throw new Exception("無法獲取 Spotify Token");
        }

        return $tokenData["access_token"];
    }

    // 搜索歌曲並返回第一筆資料
    public function searchAndCheckExplicit($query){
        try {
            $token = $this->getToken(); // 取得 Spotify Token
            $searchUrl = "https://api.spotify.com/v1/search?q=" . urlencode($query) . "&type=track&limit=1";

            $options = [
                "http" => [
                    "header" => "Authorization: Bearer $token"
                ]
            ];

            $context = stream_context_create($options);
            $response = file_get_contents($searchUrl, false, $context);
            $searchData = json_decode($response, true);

            // 檢查並返回第一筆結果
            if (isset($searchData["tracks"]["items"][0])) {
                $track = $searchData["tracks"]["items"][0];
                $result = [
                    "歌曲名稱" => $track["name"],
                    "ID" => $track["id"],
                    "成人標籤" => $track["explicit"] ? "是" : "否",
                    "演唱者" => implode(", ", array_map(function ($artist) {
                        return $artist["name"];
                    }, $track["artists"]))
                ];
                print_r($result);
                
                return $result;
            } else {
                return ["error" => "未找到相關的 Spotify 音樂資料"];
            }
        } catch (Exception $e) {
            return ["error" => "搜索 Spotify 資料時發生錯誤：" . $e->getMessage()];
        }
    }

    
    public function getYTLink($musicName) {
        $apiKey = "AIzaSyBJprZFE2_PEyc5MIxSDCSDcgziFZqVHXI";
        $maxResults = 1;

        // 組裝API URL
        $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($musicName) . "&type=video&maxResults=" . $maxResults . "&key=" . $apiKey;
        try {
            // 發送HTTP GET請求
            $response = @file_get_contents($url); // 使用 @ 抑制警告訊息
        
            // 檢查回應是否成功
            if ($response === false) {
                throw new Exception("無法獲取API回應，請檢查API金鑰或網路連線。");
            }
        
            // 將JSON格式的回應轉換成PHP陣列
            $data = json_decode($response, true);
        
            // 如果解析失敗
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON解碼失敗: " . json_last_error_msg());
            }
        
            // 提取videoId並組合成YouTube連結
            $links = [];
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (isset($item['id']['videoId'])) {
                        $videoId = $item['id']['videoId'];
                        $links[] = "https://www.youtube.com/watch?v=" . $videoId;
                    }
                }
            }

            if(count($links) > 0){
                return $links[0];
            }
            return "錯誤資料";
        
        } catch (Exception $e) {
            // 回傳錯誤訊息
            return "錯誤" . $e->getMessage();
        }
    }
}

?>
