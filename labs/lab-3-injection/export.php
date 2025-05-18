<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$output = "";
if (isset($_GET['host'])) {
    $host = $_GET['host'];

    // VULNERABLE: unsanitized input in shell command
    $cmd = "ping -c 1 " . $host;
    $output = shell_exec($cmd);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export Logs - EvilCorp CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <img src="/images/logo.png" width="80">
        <h2 class="mt-3">EvilCorp Log Exporter</h2>
        <p class="text-muted">Check server connectivity before exporting.</p>
    </div>
    <form method="GET" class="mb-4">
        <label for="host" class="form-label">Target Host</label>
        <div class="input-group">
            <input type="text" name="host" class="form-control" placeholder="e.g. 8.8.8.8 or localhost" required>
            <button class="btn btn-outline-primary">Ping</button>
        </div>
    </form>
    <?php if (!empty($output)): ?>
        <div class="card p-3">
            <pre><?= htmlspecialchars($output) ?></pre>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
