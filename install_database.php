<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/global.php';

use Nicolasps\UsersAPI\Database\Connection;

$pdo = Connection::init();

$pdo->query('CREATE DATABASE IF NOT EXISTS bar;');
$pdo->query('USE bar');

$pdo->query('CREATE TABLE IF NOT EXISTS `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;');

$pdo->query("CREATE TABLE IF NOT EXISTS `session` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `session_id` varchar(45) DEFAULT NULL,
  `expires_in` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_session_user_idx` (`user_id`),
  CONSTRAINT `session_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;");

$pdo->query("CREATE TABLE IF NOT EXISTS `drink` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;");

$pdo->query("INSERT into drink (name) values ('water');");
$pdo->query("INSERT into drink (name) values ('juice');");
$pdo->query("INSERT into drink (name) values ('coca-cola');");
$pdo->query("INSERT into drink (name) values ('coffee');");
$pdo->query("INSERT into drink (name) values ('tea');");

$pdo->query("CREATE TABLE IF NOT EXISTS `consummation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `drink_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `consummation_FK` (`user_id`),
  KEY `consummation_FK_1` (`drink_id`),
  CONSTRAINT `consummation_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `consummation_FK_1` FOREIGN KEY (`drink_id`) REFERENCES `drink` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;");