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
$user_id = $payload['user_id'];
$role = $payload['role'];

$db = get_db();
$stmt = $db->prepare('SELECT id, subject FROM emails WHERE user_id = :user_id');
$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Evil Corp Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Your Emails</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchArray()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><a href="email_view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php if (in_array($role, ['group_admin', 'mail_server_admin'])): ?>
        <a href="admin_users.php" class="btn btn-warning mt-3">Manage Users</a>
    <?php endif; ?>
    <?php if (in_array($role, ['mail_server_admin'])): ?>
        <a href="admin_logs.php?logfile=system.log" class="btn btn-warning mt-3">System Log</a>
    <?php endif; ?>
    <a href="index.php" class="btn btn-secondary mt-3">Logout</a>
</div>

</body>
</html>