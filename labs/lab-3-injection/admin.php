<?php
session_start();
$db = new SQLite3(__DIR__ . '/db/evilcorp_crm.sqlite');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$stmt = $db->prepare("SELECT role FROM users WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if (!$row || $row['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel – EvilCorp</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="p-4">
    <h1>Admin Control Panel</h1>
    <p>Welcome, administrator. You may now perform sensitive internal diagnostics.</p>
    <a href="/admin/ping.php" class="btn btn-danger mt-3">Launch Ping Interface</a>
    <a href="/profile.php?msg=Welcome,%20admin!" class="btn btn-secondary mt-3">Open Profile Preview Tool</a>
    <a href="/email_preview.php" class="btn btn-primary mt-3">Email Template Preview</a>
</body>
</html>
