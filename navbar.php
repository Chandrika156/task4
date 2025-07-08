<?php $user = current_user(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top px-4 shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary" href="index.php">My Blog</a>

    <?php if ($user): ?>
      <div class="ms-auto d-flex align-items-center">

        <!-- ✅ Allow all roles to create posts -->
        <a href="create_post.php" class="btn btn-outline-success btn-sm me-2">+ New Post</a>

        <?php if ($_SESSION['role'] === 'admin'): ?>
          <a href="admin_dashboard.php" class="btn btn-outline-primary btn-sm me-2">Admin Dashboard</a>
          <a href="register.php" class="btn btn-outline-primary btn-sm me-2">+ Register User</a>
        <?php endif; ?>

        <span class="me-3 text-dark">
          Hello, <strong><?= htmlspecialchars($user['username']) ?></strong>
          <small class="text-muted">(<?= htmlspecialchars($_SESSION['role']) ?>)</small>
        </span>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>
    <?php endif; ?>
  </div>
</nav>
