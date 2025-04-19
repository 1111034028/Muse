<?php

namespace project;

// foreach ($_GET as $key => $val) {
//     echo $key . ':' . $_GET[$key] . '<BR>';
// }
// phpinfo();

use Dotenv\Dotenv;
use project\controllers\LoginController;
use project\core\Application;

require_once __DIR__ . '/vendor/autoload.php';

// $app = new Application(dirname(__DIR__), $config);

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = new Application();

header('Access-Control-Allow-Origin:  http://127.0.0.1:5501'); // Replace with your frontend URL
header("Access-Control-Allow-Credentials: true"); 
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
// $app->on(Application::EVENT_BEFORE_REQUEST, function(){
//     // echo "Before request from second installation";
// });


// $app->router->get('/api/Login', [LoginController::class, 'demo']);
// $app->router->post('/api/Login', [LoginController::class, 'postDemo']);
// $app->router->delete('/api/Login', [LoginController::class, 'deleteDemo']);
// FIXME: Rename to $app->router->post('/api/auth/register', [AuthController::class, 'register']);
$app->router->post('/api/Login/register', [LoginController::class, 'register']);
$app->router->post('/api/Login/login', [LoginController::class, 'login']);
// $app->router->post('/api/Login/add', [LoginController::class, 'add']);
// FIXME: Rename to $app->router->patch('/api/me', [UserController::class, 'update']);
$app->router->post('/api/Login/Imedit', [LoginController::class, 'Imedit']);
$app->router->post('/api/Login/changePassword', [LoginController::class, 'changePassword']);
// $app->router->get('/api/Login/loginPinnum', [LoginController::class, 'loginPinnum']);
$app->router->post('/api/Login/forgetpwd', [LoginController::class, 'forgetpwd']);
$app->router->post('/api/Login/resetpwd', [LoginController::class, 'resetpwd']);



$app->run();