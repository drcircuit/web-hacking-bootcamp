<?php
require 'db.php';
require 'jwt.php';

$jwt = new JWT('evilcorp');
$token = $_COOKIE['token'] ?? '';
$payload = $jwt->decode($token);
if (!$payload || !in_array($payload['role'], ['group_admin', 'mail_server_admin'])) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied';
    exit;
}

$db = get_db();
$stmt = $db->prepare('SELECT username, role FROM users');
$result = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Users List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Users List</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchArray()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <p>Flag: WCH{role_upgrade_juicy_style}</p>
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>