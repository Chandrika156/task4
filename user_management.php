<?php
require 'auth.php';
require_login();
require_role(['admin']); // Only admins can access

$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id != ?");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin – Manage Users</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
  <h3 class="mb-4">All Registered Users (excluding you)</h3>
  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['id']) ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><span class="badge bg-primary text-uppercase"><?= $u['role'] ?></span></td>
          <td>
            <a href="edit_role.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Edit Role</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>
</body>
</html>
