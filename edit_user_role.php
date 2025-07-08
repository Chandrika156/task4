<?php
require 'auth.php';
require_login();
require_role(['admin']);

if (!isset($_GET['id'])) {
  die("Missing user ID.");
}

$userId = $_GET['id'];
$errors = [];

// Fetch current user info
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
  die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newRole = $_POST['role'];
  if (!in_array($newRole, ['admin', 'editor', 'viewer'])) {
    $errors[] = "Invalid role selected.";
  } else {
    $update = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $update->execute([$newRole, $userId]);
    header("Location: user_management.php");
    exit;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Role</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width: 500px;">
  <h4>Edit Role for <strong><?= htmlspecialchars($user['username']) ?></strong></h4>

  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <form method="POST">
    <div class="mb-3">
      <label for="role" class="form-label">Select New Role</label>
      <select name="role" id="role" class="form-select">
        <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>Viewer</option>
        <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>
    <button class="btn btn-primary">Update Role</button>
    <a href="user_management.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
