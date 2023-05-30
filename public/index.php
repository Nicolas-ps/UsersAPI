<?php

use Nicolasps\UsersAPI\Controllers\DrinkController;
use Nicolasps\UsersAPI\Controllers\LoginController;
use Nicolasps\UsersAPI\Controllers\UserController;
use Nicolasps\UsersAPI\Meta\Router;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/boot.php';

Router::add('/users', 'POST', UserController::class, 'create');
Router::add('/login', 'POST', LoginController::class, 'login');
Router::add('/users/{id}', 'GET', UserController::class, 'find');
Router::add('/users', 'GET', UserController::class, 'all');
Router::add('/users/{id}/drink', 'POST', UserController::class, 'consumes');
Router::add('/users/{id}', 'DELETE', UserController::class, 'delete');
Router::add('/users/{id}', 'PUT', UserController::class, 'edit');
Router::add('/drinks', 'GET', DrinkController::class, 'all');
Router::add('/user-registration', 'GET', UserController::class, 'userRegistration');
Router::add('/consummation-ranking', 'POST', UserController::class, 'consummationRanking');

Router::resolve($_SERVER['REDIRECT_URL']);
