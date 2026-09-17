<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'];
$payment_method = $data['payment_method'];
$paid_amount = $data['paid_amount'] ?? null;
$discount_amount = $data['discount_amount'] ?? 0;
$cashier_id = $_SESSION['user']['id'];

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['quantity'] * $item['unit_price'];
}
$total = max(0, $subtotal - $discount_amount);

try {
    $pdo->beginTransaction();

    // Check stock availability BEFORE making any changes
    foreach ($cart as $item) {
        $checkStmt = $pdo->prepare("SELECT name, stock_qty FROM products WHERE id = ?");
        $checkStmt->execute([$item['product_id']]);
        $product = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$product || $product['stock_qty'] < $item['quantity']) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Insufficient stock for "' . ($product['name'] ?? 'unknown product') . '". Available: ' . ($product['stock_qty'] ?? 0)
            ]);
            exit;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO sales (cashier_id, total_amount, payment_method, paid_amount, discount_amount) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$cashier_id, $total, $payment_method, $paid_amount, $discount_amount]);
    $sale_id = $pdo->lastInsertId();

    foreach ($cart as $item) {
        $subtotal = $item['quantity'] * $item['unit_price'];

        $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sale_id, $item['product_id'], $item['quantity'], $item['unit_price'], $subtotal]);

        $stmt = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }

    $pdo->commit();

    $change = $paid_amount !== null ? $paid_amount - $total : null;

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'sale_id' => $sale_id, 'total' => $total, 'change' => $change]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>