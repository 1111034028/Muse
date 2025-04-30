<?php

namespace project\controllers;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use project\core\Request;

class Controller
{
    protected function verifyToken(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->getHeader('Authorization'));
        try {
            return JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (ExpiredException | Exception $e) {
            return null;
        }
    }
}

?>