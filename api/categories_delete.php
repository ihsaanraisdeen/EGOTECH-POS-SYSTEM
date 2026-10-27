<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

header('Content-Type: application/json');

$check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$check->execute([$id]);
if ($check->fetchColumn() > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Cannot delete: products are using this category.']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(['success' => true]);
?>