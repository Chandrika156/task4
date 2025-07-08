<?php
require_once 'auth.php';
require_login();
$user = current_user();

$pid = $_GET['id'] ?? null;
if (!$pid) {
    header('Location: index.php');
    exit;
}

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$pid]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post not found.");
}

// ✅ Permission check: owner or editor/admin
$isOwner = ($post['user_id'] == $user['id']);
$isEditor = in_array($_SESSION['role'], ['editor', 'admin']);

if (!$isOwner && !$isEditor) {
    die("Not authorized to edit this post.");
}

$title = $post['title'];
$content = $post['content'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (!$title || !$content) {
        $errors[] = "Title & content are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
        $stmt->execute([$title, $content, $pid]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4" style="max-width: 720px;">
  <h2>Edit Post</h2>
  
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach($errors as $e) echo "<div>$e</div>"; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label>Title</label>
      <input name="title" value="<?= htmlspecialchars($title) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Content</label>
      <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($content) ?></textarea>
    </div>

    <button class="btn btn-primary" type="submit">Save</button>
    <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
</body>
</html>
