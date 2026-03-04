<?php
require 'config.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (strpos($_SERVER['REQUEST_URI'], '/auth/login') !== false && $method === 'POST') {
    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing email or password']);
        exit;
    }
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($data['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['success' => true, 'message' => 'Logged in']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
} elseif (strpos($_SERVER['REQUEST_URI'], '/auth/register') !== false && $method === 'POST') {
    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing email or password']);
        exit;
    }
    $pdo = getDB();
    $hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    try {
        $stmt->execute([$data['email'], $hash]);
        echo json_encode(['success' => true, 'message' => 'Registered']);
    } catch (PDOException $e) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already taken']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
