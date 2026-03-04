<?php
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing fields']);
    exit;
}

$pdo = getDB();

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already exists']);
    exit;
}

$hash = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->execute([$data['email'], $hash]);

echo json_encode(['success' => true]);
