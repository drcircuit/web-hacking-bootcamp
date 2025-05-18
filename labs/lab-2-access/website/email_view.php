<?php
require 'db.php';
require 'jwt.php';

$jwt = new JWT('evilcorp');
$token = $_COOKIE['token'] ?? '';
$payload = $jwt->decode($token);
if (!$payload) {
    header('Location: login.php');
    exit;
}

$email_id = $_GET['id'];
$db = get_db();
$stmt = $db->prepare('SELECT subject, body FROM emails WHERE id = :id');
$stmt->bindValue(':id', $email_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$email = $result->fetchArray();
if (!$email) {
    echo 'Email not found';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email - Evil Corp Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h1>Email</h1>
    <div class="card">
        <div class="card-header">
            Subject: <?php echo htmlspecialchars($email['subject']); ?>
        </div>
        <div class="card-body">
            <p><?php echo nl2br(htmlspecialchars($email['body'])); ?></p>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>
</div>
</body>
</html>