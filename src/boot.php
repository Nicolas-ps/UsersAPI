<?php

use Nicolasps\UsersAPI\Database\Connection;
use Nicolasps\UsersAPI\Meta\Request;

require_once __DIR__ . '/global.php';

$GLOBALS['REQUEST'] = new Request();
$GLOBALS['PDO'] = Connection::init();
