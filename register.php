<?php
require 'db.php';
session_start();
$errors = [];

// Check if current user is an admin
$isAdmin = isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $role = $isAdmin && isset($_POST['role']) ? $_POST['role'] : 'viewer';

  // Server-side validation
  if (!$username || !$password) {
    $errors[] = "All fields are required.";
  } elseif (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters long.";
  } else {
    // Check for duplicate username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
      $errors[] = "Username is already taken.";
    } else {
      // Hash and insert user
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
      $stmt->execute([$username, $hash, $role]);

      if (!$isAdmin) {
        // Auto-login if self-registered
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = $role;
        header('Location: index.php');
        exit;
      } else {
        $errors[] = "User registered successfully by admin.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
  <h2 class="mb-4 text-center">Register</h2>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-<?= str_contains($e, 'successfully') ? 'success' : 'danger' ?>">
      <?= htmlspecialchars($e) ?>
    </div>
  <?php endforeach; ?>

  <form method="POST" novalidate onsubmit="return validateRegister()">
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input name="username" id="username" class="form-control" placeholder="Enter username" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
    </div>

    <?php if ($isAdmin): ?>
      <div class="mb-3">
        <label for="role" class="form-label">Select Role</label>
        <select name="role" id="role" class="form-select" required>
          <option value="viewer">Viewer</option>
          <option value="editor">Editor</option>
          <option value="admin">Admin</option>
        </select>
      </div>
    <?php endif; ?>

    <div class="d-grid">
      <button class="btn btn-success">Register</button>
    </div>

    <?php if (!$isAdmin): ?>
      <div class="mt-3 text-center">
        <a href="login.php" class="btn btn-link">Already have an account? Login</a>
      </div>
    <?php endif; ?>
  </form>
</div>

<script>
function validateRegister() {
  const password = document.getElementById("password").value;
  if (password.length < 6) {
    alert("Password must be at least 6 characters long.");
    return false;
  }
  return true;
}
</script>
</body>
</html>
