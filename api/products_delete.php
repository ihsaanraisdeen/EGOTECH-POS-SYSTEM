<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Check if this product has sale history before deleting
$check = $pdo->prepare("SELECT COUNT(*) as c FROM sale_items WHERE product_id = ?");
$check->execute([$id]);
$count = $check->fetch(PDO::FETCH_ASSOC)['c'];

header('Content-Type: application/json');

if ($count > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Cannot delete — this product has sales history.']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);
echo json_encode(['success' => true]);
?>