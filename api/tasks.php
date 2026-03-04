<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$pdo = getDB();
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$task_id = isset($segments[2]) ? (int)$segments[2] : null;

if ($method === 'GET') {
    $status = $_GET['status'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $where = "user_id = :user_id AND deleted_at IS NULL";
    $params = [':user_id' => $user_id];

    if ($status) {
        $where .= " AND status = :status";
        $params[':status'] = $status;
    }

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE $where LIMIT :limit OFFSET :offset");
    $params[':limit'] = $limit;
    $params[':offset'] = $offset;
    $stmt->execute($params);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE $where");
    unset($params[':limit'], $params[':offset']);
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    echo json_encode([
        'tasks' => $tasks,
        'page' => $page,
        'total_pages' => ceil($total / $limit)
    ]);
} elseif ($method === 'POST' && !$task_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['title'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing title']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $data['title'], $data['description'] ?? '', $data['status'] ?? 'pending']);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} elseif ($method === 'PUT' && $task_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $updates = [];
    $params = [];
    if (isset($data['title'])) {
        $updates[] = "title = :title";
        $params[':title'] = $data['title'];
    }
    if (isset($data['description'])) {
        $updates[] = "description = :description";
        $params[':description'] = $data['description'];
    }
    if (isset($data['status'])) {
        $updates[] = "status = :status";
        $params[':status'] = $data['status'];
    }
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No updates provided']);
        exit;
    }
    $updateStr = implode(', ', $updates);
    $stmt = $pdo->prepare("UPDATE tasks SET $updateStr WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL");
    $params[':id'] = $task_id;
    $params[':user_id'] = $user_id;
    $stmt->execute($params);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
    }
} elseif ($method === 'DELETE' && $task_id) {
    $stmt = $pdo->prepare("UPDATE tasks SET deleted_at = NOW() WHERE id = ? AND user_id = ? AND deleted_at IS NULL");
    $stmt->execute([$task_id, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
