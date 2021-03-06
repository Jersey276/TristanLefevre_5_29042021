<?php

require dirname( __FILE__, 2).'/app/App.php';
app\APP::load();
use app\Router;

$router = new Router();
$router
    ->get('/', 'HomeController', 'home')
    ->get('/cv', 'HomeController', 'showCV')
    ->post('/contact', 'HomeController', 'sendMail')
    ->get('/test/[i:id]', 'TestController', 'test')

    ->get('/register', 'AuthController', 'registerForm', 'Guest')
    ->post('/register', 'AuthController', 'register', 'Guest')
    ->get('/login', 'AuthController', 'loginForm', 'Guest')
    ->post('/login', 'AuthController', 'login', 'Guest')
    ->get('/forgotPassword', 'AuthController', 'forgotPasswordForm', 'Guest')
    ->post('/forgotPassword', 'AuthController', 'forgotPassword', 'Guest')
    ->get('/forgotPassword/[a:token]', 'AuthController', 'changePasswordForm', 'Guest')
    ->post('/forgotPassword/[a:token]', 'AuthController', 'changePassword', 'Guest')
    ->get('/login/[a:token]', 'AuthController', 'validEmail')
    ->get('/logout', 'AuthController', 'logout', 'User')
    
    ->get('/post', 'PostController', 'listPosts')
    ->get('/post/[i:id]', 'PostController', 'getPost')
    ->post('/post/[i:id]', 'CommentController', 'postComment', 'User')

    ->get('/profil/[a:profil]', 'UserController', 'modifyProfilForm', 'User')
    ->post('/profil/[a:profil]/email', 'UserController', 'changeEmail', 'User')
    ->post('/profil/[a:profil]/password', 'UserController', 'changePassword', 'User')
    ->post('/profil/[a:profil]/remove', 'UserController', 'removeProfil', 'User')

    ->get('/admin', 'HomeController', 'adminHome', 'Writer')

    ->get('/admin/post', 'PostController', 'listPostsAdmin', 'Writer')
    ->get('/admin/post/add', 'PostController', 'addPostForm', 'Writer')
    ->post('/admin/post/add', 'PostController', 'addPost', 'Writer')
    ->get('/admin/post/[i:id]', 'PostController', 'modifyPostForm', 'Writer')
    ->post('/admin/post/[i:id]', 'PostController', 'modifyPost', 'Writer')
    ->post('/admin/post/[i:id]', 'PostController', 'removePostForm', 'Writer')
    ->post('/admin/post/remove/[i:id]', 'PostController', 'removePost', 'Writer')

    ->get('/admin/post/[i:post]/comment', 'CommentController', 'adminListComment', 'Admin')
    ->post('/admin/post/[i:post]/comment/[i:comment]/approuve', 'CommentController', 'validComment', 'Admin')
    ->post('/admin/post/[i:post]/comment/[i:comment]/remove', 'CommentController', 'removeComment', 'Admin')

    ->get('/admin/profil', 'UserController', 'adminListProfil', 'Admin')
    ->get('/admin/profil/[a:profil]', 'UserController', 'adminModifyProfilForm', 'Admin')
    ->post('/admin/profil/[a:profil]/email', 'UserController', 'adminModifyEmail', 'Admin')
    ->post('/admin/profil/[a:profil]/role', 'UserController', 'adminModifyRole', 'Admin')
    ->get('/admin/profil/[a:profil]/remove', 'UserController', 'adminRemoveProfilForm', 'Admin')
    ->post('/admin/profil/[a:profil]/remove', 'UserController', 'adminRemoveProfil', 'Admin')
    ->run();
