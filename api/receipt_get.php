<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$sale_id = $_GET['sale_id'];

$saleStmt = $pdo->prepare("
    SELECT sales.*, users.name as cashier_name
    FROM sales JOIN users ON sales.cashier_id = users.id
    WHERE sales.id = ?
");
$saleStmt->execute([$sale_id]);
$sale = $saleStmt->fetch(PDO::FETCH_ASSOC);

$itemsStmt = $pdo->prepare("
    SELECT sale_items.*, products.name as product_name
    FROM sale_items JOIN products ON sale_items.product_id = products.id
    WHERE sale_id = ?
");
$itemsStmt->execute([$sale_id]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['sale' => $sale, 'items' => $items]);
?>