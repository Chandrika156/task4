<?php
require 'auth.php';
require_login();
require_role(['admin']); // Only admins can access
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
  <h2>Welcome to Admin Dashboard</h2>
  <ul>
    <li><a href="user_management.php">Manage Users</a></li>
    <!-- Add more admin links here -->
  </ul>
</div>
</body>
</html>
