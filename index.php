<?php
require 'auth.php';
require_login();

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];
$search_sql = '';
$role = $_SESSION['role'];

// 🔐 Editor/Admin can see all posts; viewer only their own
$where_sql = '';
if ($role === 'viewer') {
    $where_sql = "WHERE posts.user_id = ?";
    $params[] = $_SESSION['user_id'];
} else {
    $where_sql = "WHERE 1=1";
}

// 🔍 Search logic
if ($search) {
    $search_sql = " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Count total posts
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts $where_sql $search_sql");
$count_stmt->execute($params);
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Fetch posts with user info
$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        $where_sql $search_sql 
        ORDER BY posts.created_at DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title>All Posts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <form method="GET" class="d-flex mb-4">
    <input type="text" name="search" class="form-control me-2" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-outline-primary">Search</button>
  </form>

  <?php if (in_array($role, ['admin', 'editor'])): ?>
    <a href="create_post.php" class="btn btn-success mb-3">+ New Post</a>
  <?php endif; ?>

  <?php foreach ($posts as $post): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5><?= htmlspecialchars($post['title']) ?></h5>
        <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?></p>
        <small class="text-muted">By <?= htmlspecialchars($post['username']) ?> on <?= $post['created_at'] ?></small>
        <div class="mt-2">
          <?php
          // Allow edit/delete if owner or editor/admin
          $canEdit = ($post['user_id'] == $_SESSION['user_id']) || in_array($role, ['editor', 'admin']);
          ?>
          <?php if ($canEdit): ?>
            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <?php if ($page > 1): ?>
        <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Previous</a></li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $total_pages): ?>
        <li class="page-item"><a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>
</body>
</html>
