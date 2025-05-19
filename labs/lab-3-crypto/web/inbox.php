<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle clearance code submission
$challenge2_flag = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    if ($_POST['code'] === 'ULTRA-7QZ49') {
        $_SESSION['role'] = 'vault_console_operator';
        $challenge2_flag = 'WCH{iv_got_the_power}';
    } else {
        $error = 'Incorrect clearance code.';
    }
}

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
</head>
<body>
<div class="container mt-5">
    <h1>Welcome, <?= htmlspecialchars($username) ?></h1>
    <h3>Inbox</h3>
    <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
        <div>
            <strong><?= htmlspecialchars($row['subject']) ?></strong><br>
            <pre><?= htmlspecialchars($row['body']) ?></pre>
        </div>
    <?php endwhile; ?>

    <hr>
    <h3>Enter Clearance Code</h3>
    <form method="POST">
        <input type="text" name="code" placeholder="Enter code">
        <button type="submit">Submit</button>
    </form>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($challenge2_flag): ?>
        <div style="margin-top: 20px;">
            <h4>✅ Challenge 2 Complete</h4>
            <p>Flag: <?= $challenge2_flag ?></p>
            <p><a href="vault_console.php">Go to Vault Console</a></p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>