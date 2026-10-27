<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');

header('Content-Type: application/json');

if ($name === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Category name is required']);
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE LOWER(name) = LOWER(?)");
$check->execute([$name]);
if ($check->fetchColumn() > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Category already exists']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
$stmt->execute([$name]);

echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
?>