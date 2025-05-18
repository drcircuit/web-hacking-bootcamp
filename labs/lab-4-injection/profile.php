<?php
session_start();
$db = new SQLite3(__DIR__ . '/db/evilcorp_crm.sqlite');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$msg = $_GET['msg'] ?? 'Welcome back!';
$output = '';

try {
    eval("\$output = \"$msg\";");
} catch (Throwable $e) {
    $output = "Error parsing template.";
}
$info = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // VULNERABLE: Blind SQLi
    $query = "SELECT username, role FROM users WHERE id = $id";
    $result = $db->query($query);
    $info = $result->fetchArray(SQLITE3_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - EvilCorp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <img src="/images/logo.png" width="80">
        <h2 class="mt-3">EvilCorp CRM – User Profile</h2>
        <p><?= htmlspecialchars($output) ?></p>
    </div>
    <form method="GET" class="mb-3">
        <label for="id" class="form-label">Lookup User by ID</label>
        <div class="input-group">
            <input type="number" name="id" class="form-control" placeholder="e.g. 1" required>
            <button class="btn btn-outline-secondary">Fetch</button>
        </div>
    </form>
    <?php if ($info): ?>
        <div class="card p-3">
            <h5>User Details</h5>
            <p><strong>Username:</strong> <?= htmlspecialchars($info['username']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($info['role']) ?></p>
        </div>
    <?php elseif (isset($_GET['id'])): ?>
        <p class="text-danger">User not found.</p>
    <?php endif; ?>
</div>
</body>
</html>
