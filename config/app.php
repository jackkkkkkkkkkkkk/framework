<?php

define('APP_TITLE', 'framework');
define('BASE_URL', 'http://localhost:8000/');
define('BASE_DIR', realpath(__DIR__ . '/../'));


$temp = trim($_SERVER['REQUEST_URI'], '/');
$temp = explode('?', $temp)[0];
define('CURRENT_URI', $temp);

global $routes;
$routes = [
    'get' => [],
    'post' => [],
    'put' => [],
    'delete' => []
];