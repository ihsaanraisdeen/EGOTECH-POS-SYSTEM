<?php
require 'config/db.php';

$name = 'Admin User';
$username = 'admin';
$password = 'admin123';
$role = 'admin';

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name, username, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $username, $password_hash, $role]);

echo "Admin user created! Login: admin / admin123";
?>