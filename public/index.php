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

    ->get('/register','userController','registerForm')
    ->post('/register','userController','register')
    ->get('/login','userController','loginForm')
    ->post('/login','userController','login')
    ->get('/forgotPassword','userController','forgotPasswordForm')
    ->post('/forgotPassword','userController','forgotPassword')
    ->get('/forgotPassword/[a:token]','userController','changePasswordForm')
    ->post('/forgotPassword/[a:token]','userController','changePassword')
    ->get('/login/[a:token]','userController','validEmail')
    ->run();
