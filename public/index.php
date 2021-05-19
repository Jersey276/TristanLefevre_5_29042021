<?php

require '..\app\app.php';

app\APP::load();
use app\Router;

$router = new Router();
$router
    ->get('/', 'HomeController', 'home')
    ->get('/cv','HomeController', 'showCV')
    ->post('/contact','HomeController', 'sendMail')
    ->get('/test/[i:id]','TestController','test')

    ->get('/register','AuthController','registerForm','Guest')
    ->post('/register','AuthController','register','Guest')
    ->get('/login','AuthController','loginForm','Guest')
    ->post('/login','AuthController','login','Guest')
    ->get('/forgotPassword','AuthController','forgotPasswordForm','Guest')
    ->post('/forgotPassword','AuthController','forgotPassword','Guest')
    ->get('/forgotPassword/[a:token]','AuthController','changePasswordForm','Guest')
    ->post('/forgotPassword/[a:token]','AuthController','changePassword','Guest')
    ->get('/login/[a:token]','AuthController','validEmail','Guest')
    ->get('/logout','AuthController','logout','User')

    ->get('/admin','HomeController','adminHome','Writer')
    
    ->run();
