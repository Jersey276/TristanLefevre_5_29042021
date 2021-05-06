<?php

require '..\app\app.php';

APP::load();
use app\Router;

$router = new Router();
$router
    ->get('/', 'homeController', 'home')
    ->get('/cv','homeController', 'showCV')
    ->post('/contact','homeController', 'sendMail')
    ->get('/test/[i:id]','homeController','test')
    ->run();
