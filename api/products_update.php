<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("UPDATE products SET name=?, price=?, cost_price=?, stock_qty=?, reorder_level=?, barcode=? WHERE id=?");
$stmt->execute([
    $data['name'],
    $data['price'],
    $data['cost_price'],
    $data['stock_qty'],
    $data['reorder_level'],
    $data['barcode'],
    $data['id']
]);

header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>