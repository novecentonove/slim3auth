<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$app->get('/', 'PagesController:home')->setName('home');


$app->group('', function(){
    $this->get('/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/signup', 'AuthController:postSignUp');
    $this->get('/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function(){
    $this->get('/change-password', 'AuthController:getChangePassword')->setName('auth.password');
    $this->post('/change-password', 'AuthController:postChangePassword');
    $this->get('/signout', 'AuthController:signOut')->setName('auth.signout');
})->add(new AuthMiddleware($container));

