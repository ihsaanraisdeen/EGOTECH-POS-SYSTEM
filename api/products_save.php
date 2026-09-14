<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, cost_price, stock_qty, reorder_level, barcode) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $data['name'],
    $data['category_id'] ?: null,
    $data['price'],
    $data['cost_price'] ?: 0,
    $data['stock_qty'] ?: 0,
    $data['reorder_level'] ?: 5,
    $data['barcode'] ?: null
]);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
?>