<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new SQLite3('vault.db');
$user = $_SESSION['user'];

$query = "SELECT * FROM vault WHERE owner = '$user'";
$results = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Vault</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container">
    <h1><?= htmlspecialchars($user) ?>'s Vault</h1>
    <ul>
        <?php while ($row = $results->fetchArray()) { ?>
            <li><b><?= htmlspecialchars($row['title']) ?></b>: <?= htmlspecialchars($row['encrypted_data']) ?></li>
        <?php } ?>
    </ul>
</div>
</body>
</html>
