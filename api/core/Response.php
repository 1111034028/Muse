<?php

namespace project\core;

class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }
    public function json($data=null, $code = 200)
    {
        // header('Content-Type: application/json');
        // http_response_code($code);
        // echo json_encode([
        //     'code' => $code,
        //     'data' => $data
        // ]);
        // exit();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}