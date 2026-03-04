<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');

$segments = explode('/', $path);

if (count($segments) > 1 && $segments[0] === 'api') {
    array_shift($segments);
}

$endpoint = implode('/', $segments);

switch ($endpoint) {
    case 'auth/login':
    case 'auth/register':
        require 'auth.php';
        break;
    case 'tasks':
    case (preg_match('/^tasks\/\d+$/', $endpoint) ? true : false):
        require 'tasks.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}
?>
