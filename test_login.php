<?php
require 'db.php';

$username = 'admin';
$password = 'admin123';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.<br>";
} else {
    echo "User found.<br>";
    echo "Stored hash: " . $user['password'] . "<br>";

    if (password_verify($password, $user['password'])) {
        echo "✅ Password matches!";
    } else {
        echo "❌ Password does NOT match.";
    }
}
