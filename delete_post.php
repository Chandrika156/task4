<?php
require 'auth.php';
require_login();

if (!isset($_GET['id'])) {
    die("Missing post ID.");
}

$id = $_GET['id'];

// Fetch the post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found.");
}

// Role-based delete permission
$canDelete = (
    $post['user_id'] == $_SESSION['user_id'] || 
    in_array($_SESSION['role'], ['editor', 'admin'])
);

if (!$canDelete) {
    die("You do not have permission to delete this post.");
}

// Perform deletion
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit;
