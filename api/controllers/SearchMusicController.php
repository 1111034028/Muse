<?php

namespace project\controllers;

use project\core\Request;
use project\DataBase\DatabaseHelper;

class SearchMusicController{
    
    public function getPagedMusicData(): void{
        
        $request = new Request();
        $request->setParams($_GET);
        try {
            // 從 Request 中獲取當前頁數和每頁大小的參數
            $currentPage = (int) $request->getParam('page', 1); // 預設為第 1 頁
            $search = $request->getParam('search', "");
            // 防止非法參數值
            if ($currentPage < 1) {
                throw new \InvalidArgumentException("頁碼和每頁大小必須為正整數。");
            }

            // 計算 offset
            $offset = ($currentPage - 1) * 10;

            // 查詢總記錄數
            $totalRecordsQuery = "SELECT COUNT(*) AS totalRecords FROM Music";
            $totalRecordsResult = DatabaseHelper::getData($totalRecordsQuery);
            $totalRecords = (int) ($totalRecordsResult[0]['totalRecords'] ?? 0);

            // 計算總頁數
            $totalPages = (int) ceil($totalRecords / 10);

            $search = addslashes($search);
            
            $pagedRecordsQuery = "SELECT Music_ID, Music_Name, Artist, Album, Music_URL 
                        FROM Music 
                        WHERE Music_Name LIKE '%$search%' OR Artist LIKE '%$search%'
                        ORDER BY Music_Name
                        LIMIT 10 OFFSET $offset";

            $records = DatabaseHelper::getData($pagedRecordsQuery);

            // 構建回應數據
            $response = [
                "status" => "success",
                "data" => [
                    "currentPage" => $currentPage,
                    "pageSize" => 10,
                    "totalPages" => $totalPages,
                    "totalRecords" => $totalRecords,
                    "records" => $records
                ]
            ];
            if($records == []){
                $response = [
                    "status" => "error",
                    "message" => "查無此結果"
                ];
            }

            // 輸出為 JSON
            header('Content-Type: application/json');
            echo json_encode($response, JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            // 捕捉錯誤並返回 JSON 錯誤訊息
            $errorResponse = [
                "status" => "error",
                "message" => $e->getMessage()
            ];
            header('Content-Type: application/json');
            echo json_encode($errorResponse, JSON_PRETTY_PRINT);
        }
    }
}
