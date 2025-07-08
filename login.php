<?php
require 'db.php';
session_start();
$errors = [];

// If already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  // Server-side validation
  if (!$username || !$password) {
    $errors[] = "Both fields are required.";
  } else {
    // Use prepared statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];  // Save user role for access control
      header('Location: index.php');
      exit;
    } else {
      $errors[] = "Invalid username or password.";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
  <h2 class="mb-4 text-center">Login</h2>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <form method="POST" novalidate>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input name="username" id="username" class="form-control" placeholder="Enter username" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
    </div>

    <div class="d-grid">
      <button class="btn btn-primary">Login</button>
    </div>

    <div class="mt-3 text-center">
      <a href="register.php" class="btn btn-link">Don't have an account? Register</a>
    </div>
  </form>
</div>
</body>
</html>
