<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require 'config.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$base = '/task-management-api/api';
$endpoint = str_replace($base, '', $uri);
$endpoint = trim($endpoint, '/');

if ($endpoint === 'auth/register') {
    require 'register.php';
} elseif ($endpoint === 'auth/login') {
    require 'login.php';
} elseif (strpos($endpoint, 'tasks') === 0) {
    require 'tasks.php';
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
