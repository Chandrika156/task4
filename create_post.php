<?php
require 'auth.php';
require_login(); // This allows any logged-in user

// Optional: If you want to prevent "viewer" from posting, uncomment this line
// require_role(['admin', 'editor']);

$title = $content = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!$title || !$content) {
        $errors[] = "Title and content are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $content]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h2>Create New Post</h2>
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <form method="post">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" class="form-control" rows="6" required></textarea>
        </div>
        <button class="btn btn-success">Create Post</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
