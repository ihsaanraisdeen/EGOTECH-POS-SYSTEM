<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}

$q = $_GET['q'] ?? '';
$category = $_GET['category_id'] ?? '';

$sql = "SELECT * FROM products WHERE (name LIKE ? OR barcode = ?)";
$params = ["%$q%", $q];

if ($category !== '') {
    $sql .= " AND category_id = ?";
    $params[] = $category;
}

$sql .= " ORDER BY name LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>