<?php

require '..\app\app.php';

app\APP::load();
use app\Router;

$router = new Router();
$router
    ->get('/', 'homeController', 'home')
    ->get('/cv','homeController', 'showCV')
    ->post('/contact','homeController', 'sendMail')
    ->get('/test/[i:id]','homeController','test')

    ->get('/register','authController','registerForm','Guest')
    ->post('/register','authController','register','Guest')
    ->get('/login','authController','loginForm','Guest')
    ->post('/login','authController','login','Guest')
    ->get('/forgotPassword','authController','forgotPasswordForm','Guest')
    ->post('/forgotPassword','authController','forgotPassword','Guest')
    ->get('/forgotPassword/[a:token]','authController','changePasswordForm','Guest')
    ->post('/forgotPassword/[a:token]','authController','changePassword','Guest')
    ->get('/login/[a:token]','authController','validEmail','Guest')
    ->get('/logout','authController','logout','User')
    ->run();
