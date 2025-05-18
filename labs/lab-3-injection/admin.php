<?php
session_start();
$db = new SQLite3('evilcorp_crm.sqlite');

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$query = "SELECT * FROM messages WHERE sender = 'admin' AND recipient = 'admin' AND subject = 'Flag'";
$result = $db->query($query);
$flag = null;

if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $flag = $row['body'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - EvilCorp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <img src="/images/logo.png" width="80">
        <h2 class="mt-3 text-danger">Admin Panel</h2>
        <p class="text-muted">Top secret data. Executives only.</p>
    </div>
    <?php if ($flag): ?>
        <div class="card p-4 text-center">
            <h4 class="mb-3">💥 Final Flag 💥</h4>
            <div class="alert alert-success fs-4"><?= htmlspecialchars($flag) ?></div>
        </div>
    <?php else: ?>
        <p class="text-danger">No flag found.</p>
    <?php endif; ?>
</div>
</body>
</html>
