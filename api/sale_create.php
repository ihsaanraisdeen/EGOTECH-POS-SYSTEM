<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart']; // array of {product_id, quantity, unit_price}
$payment_method = $data['payment_method'];
$cashier_id = $_SESSION['user']['id'];

$total = 0;
foreach ($cart as $item) {
    $total += $item['quantity'] * $item['unit_price'];
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO sales (cashier_id, total_amount, payment_method) VALUES (?, ?, ?)");
    $stmt->execute([$cashier_id, $total, $payment_method]);
    $sale_id = $pdo->lastInsertId();

    foreach ($cart as $item) {
        $subtotal = $item['quantity'] * $item['unit_price'];

        $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sale_id, $item['product_id'], $item['quantity'], $item['unit_price'], $subtotal]);

        $stmt = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }

    $pdo->commit();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'sale_id' => $sale_id, 'total' => $total]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>