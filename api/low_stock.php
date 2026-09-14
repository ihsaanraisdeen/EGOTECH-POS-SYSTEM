<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$stmt = $pdo->query("SELECT * FROM products WHERE stock_qty <= reorder_level ORDER BY stock_qty ASC");
$lowStock = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($lowStock);
?>