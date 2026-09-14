<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$stmt = $pdo->query("SELECT products.*, categories.name as category_name FROM products LEFT JOIN categories ON products.category_id = categories.id");
$products = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($products);
?>