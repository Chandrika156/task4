<?php
require 'auth.php';
require_login();
require_role(['admin']); // Only admins can access

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = (int) $_GET['id'];  // Sanitize
$errors = [];
$success = '';

// Fetch user
$stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role = $_POST['role'] ?? '';
    $valid_roles = ['viewer', 'editor', 'admin'];

    if (!in_array($new_role, $valid_roles)) {
        $errors[] = "Invalid role selected.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$new_role, $user_id]);

            // Re-fetch user to update displayed value
            $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $success = "✅ Role updated successfully.";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width: 500px;">
    <h3 class="mb-4">Edit Role: <?= htmlspecialchars($user['username']) ?></h3>

    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Select New Role</label>
            <select name="role" class="form-select" required>
                <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="admin"  <?= $user['role'] === 'admin'  ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <button class="btn btn-primary">Update Role</button>
        <a href="user_management.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
