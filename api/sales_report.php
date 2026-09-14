<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$date = $_GET['date'] ?? date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT sales.*, users.name as cashier_name
    FROM sales
    JOIN users ON sales.cashier_id = users.id
    WHERE DATE(sales.created_at) = ?
    ORDER BY sales.created_at DESC
");
$stmt->execute([$date]);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalStmt = $pdo->prepare("SELECT SUM(total_amount) as total, COUNT(*) as count FROM sales WHERE DATE(created_at) = ?");
$totalStmt->execute([$date]);
$totals = $totalStmt->fetch(PDO::FETCH_ASSOC);

$bestSellersStmt = $pdo->prepare("
    SELECT products.name, SUM(sale_items.quantity) as total_qty, SUM(sale_items.subtotal) as total_revenue
    FROM sale_items
    JOIN sales ON sale_items.sale_id = sales.id
    JOIN products ON sale_items.product_id = products.id
    WHERE DATE(sales.created_at) = ?
    GROUP BY products.id
    ORDER BY total_qty DESC
    LIMIT 5
");
$bestSellersStmt->execute([$date]);
$bestSellers = $bestSellersStmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'sales' => $sales,
    'total_revenue' => $totals['total'] ?? 0,
    'total_transactions' => $totals['count'] ?? 0,
    'best_sellers' => $bestSellers
]);
?>