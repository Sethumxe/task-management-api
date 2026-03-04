<?php
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if ($method === 'GET') {

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode(['tasks' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

if ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $data['title'],
        $data['description'],
        $data['status']
    ]);

    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {

    $id = basename($uri);

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);

    echo json_encode(['success' => true]);
}
