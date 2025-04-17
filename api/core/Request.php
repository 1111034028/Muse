<?php

namespace project\core;

class Request
{

    public function getPath()
    {
        //return explode('?', $_SERVER['REQUEST_URI'])[0];
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function getMethod()
    {
        // return $_SERVER['REQUEST_METHOD'];
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function query()
    {
        return $_GET;
    }

    public function body()
    {
        // if (count($_POST) > 0) {
        //     return $_POST;
        // }
        // $inputJSON = file_get_contents('php://input');
        // // return json_decode($inputJSON, TRUE); //convert JSON into array
        // $data = json_decode($inputJSON, TRUE); // 將 JSON 轉換為陣列
        // error_log("JSON Data: " . print_r($data, true)); // 除錯：檢查 JSON 資料
        // return $data;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputJSON = file_get_contents('php://input');
            return json_decode($inputJSON, true);
        }
        return [];
    }

    public function getHeader($key)
    {
        return $this->getHeaders()[$key] ?? null;
    }

    public function getHeaders()
    {
        $headers = [];
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        return $headers;
    }
}