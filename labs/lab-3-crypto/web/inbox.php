<?php
// inbox.php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Send custom vault key header
header("X-Vault-Key: " . base64_encode("vault_s3cr3t_key"));

$db = get_db();
$stmt = $db->prepare('SELECT subject, body FROM emails WHERE user_id = :user_id');
$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$results = $stmt->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox - Evil Corp Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Welcome, <?= htmlspecialchars($username) ?></h1>
    <h3>Inbox</h3>
    <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
        <div class="card mb-3">
            <div class="card-header">
                <?= htmlspecialchars($row['subject']) ?>
            </div>
            <div class="card-body">
                <pre><?= htmlspecialchars($row['body']) ?></pre>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
