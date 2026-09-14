<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR barcode = ? LIMIT 10");
$stmt->execute(["%$q%", $q]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($products);
?>