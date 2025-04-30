<?php

namespace project\controllers;

use project\core\Request;

class GetLyricsController
{
    private string $accessToken;

    public function __construct()
    {
        $this->accessToken = "2jUjRILIDgIjPz9riRh4FSc-BY23OPCdDfQtkm0KRglBljHSf9JTZBkHrAweKAla";
    }

    // 搜尋音樂並獲取歌詞
    public function getLyrics()
    {
        $request = new Request();
        $request->setParams($_GET);

        $musicName = $request->getParam('musicName', '');

        if (empty($musicName)) {
            return [
                "status" => "error",
                "message" => "請提供音樂名稱"
            ];
        }

        // Genius API 搜尋
        $url = "https://api.genius.com/search?q=" . urlencode($musicName);
        $response = $this->makeApiRequest($url);

        if (!isset($response['response']['hits'][0]['result']['url'])) {
            return [
                "status" => "error",
                "message" => "未找到匹配的歌詞網址"
            ];
        }
        $lyricsUrl = $response['response']['hits'][0]['result']['url'];

        // 從歌詞網址抓取歌詞內容
        $html = $this->makeRequest($lyricsUrl);
        if ($html) {
            $doc = new \DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML($html);
            libxml_clear_errors();

            $xpath = new \DOMXPath($doc);
            // 更新的 XPath 查詢，匹配可能的歌詞標籤結構
            $lyricsNode = $xpath->query("//p | //div[contains(@class, 'Lyrics__Container')]");

            $lyrics = "";
            foreach ($lyricsNode as $node) {
                $text = trim($node->nodeValue);

                // 過濾多餘內容
                if (!$this->isExtraneousContent($text)) {
                    $lyrics .= $text . "\n";
                }
            }

            if (!empty($lyrics)) {
                return [
                    "status" => "success",
                    "lyrics" => $lyrics
                ];
            }
        }

        return [
            "status" => "error",
            "message" => "未能抓取到歌詞，可能網頁結構已更新"
        ];
    }

    // 檢查是否為多餘內容
    private function isExtraneousContent(string $text): bool
    {
        // 判斷多餘內容的條件，例如包含以下關鍵字
        $extraneousKeywords = [
            "How to Format Lyrics:",
            "transcription guide",
            "Frequently Asked Questions",
            "About the group",
            "Find answers",
            "Explore its deeper meaning"
        ];

        foreach ($extraneousKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                return true; // 多餘內容
            }
        }

        return false; // 歌詞部分
    }

    // 發送 API 請求
    private function makeApiRequest(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->accessToken}"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // 發送普通 HTTP 請求
    private function makeRequest(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟隨重定向
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}

?>