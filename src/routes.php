<?php

use Nicolasps\UsersAPI\Controllers\LoginController;
use Nicolasps\UsersAPI\Controllers\UserController;

$routes = [
    '/users' => ['POST', UserController::class, 'create'],
    '/login' => ['POST', LoginController::class, 'login'],
    '/users/{id}' => ['GET', UserController::class, 'find'],
    '/users/' => ['GET', UserController::class, 'all']
];

return $routes;