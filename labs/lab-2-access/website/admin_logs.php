<?php
require 'jwt.php';

$jwt = new JWT('evilcorp');
$token = $_COOKIE['token'] ?? '';
$payload = $jwt->decode($token);
if (!$payload || $payload['role'] !== 'mail_server_admin') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Service Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Service Logs</h1>
<?php
if (isset($_GET['logfile'])) {
    $file = $_GET['logfile'];
    echo "<pre>";
    include($file);
    echo "</pre>";
} else {
    echo "<p>No log file specified.</p>";
}
?>
    <p>Flag: WCH{i_am_your_admin_now}</p>
    <a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>