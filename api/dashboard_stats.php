<?php
date_default_timezone_set('Asia/Colombo');
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$today = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-6 days'));

// Today's sales total + number of transactions
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) AS total, COUNT(*) AS cnt FROM sales WHERE DATE(created_at) = ?");
$stmt->execute([$today]);
$todaySales = $stmt->fetch(PDO::FETCH_ASSOC);

// Items sold today
$stmt = $pdo->prepare("SELECT COALESCE(SUM(si.quantity),0) FROM sale_items si JOIN sales s ON si.sale_id = s.id WHERE DATE(s.created_at) = ?");
$stmt->execute([$today]);
$itemsSold = $stmt->fetchColumn();

// Low stock count
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_qty <= reorder_level")->fetchColumn();

// Last 7 days sales
$stmt = $pdo->prepare("SELECT DATE(created_at) AS day, SUM(total_amount) AS total FROM sales WHERE DATE(created_at) >= ? GROUP BY DATE(created_at)");
$stmt->execute([$startDate]);
$rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // ['2026-10-12' => '1500.00', ...]

// Fill missing days with 0 so the chart always shows 7 days
$trend = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $trend[] = ['day' => $day, 'total' => (float)($rows[$day] ?? 0)];
}

header('Content-Type: application/json');
echo json_encode([
    'today_total' => (float)$todaySales['total'],
    'today_count' => (int)$todaySales['cnt'],
    'items_sold'  => (int)$itemsSold,
    'low_stock'   => (int)$lowStock,
    'trend'       => $trend
]);
?>