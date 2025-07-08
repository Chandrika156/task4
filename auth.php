<?php
session_start();
require_once 'db.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_user() {
    global $pdo;
    if (is_logged_in()) {
        $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

function require_role($roles = []) {
    if (!is_logged_in() || !in_array($_SESSION['role'], $roles)) {
        die("Access denied.");
    }
}
