<?php
session_start();
$db = new SQLite3(__DIR__ . '/db/evilcorp_crm.sqlite');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['shown_flag'])) {
    echo '<div class="alert alert-success text-center mt-3">Welcome to EvilCorp CRM!<br>Flag: <strong>WCH{bypass_login_access_granted}</strong></div>';
    $_SESSION['shown_flag'] = true;
}
$results = [];
if (isset($_GET['q'])) {
    $q = $_GET['q'];
    // VULNERABLE: Direct query injection
    $query = "SELECT id, username, role FROM users WHERE username LIKE '%$q%'";
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $results[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRM Search - EvilCorp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <div class="mb-4 text-center">
        <img src="/images/logo.png" width="100">
        <h2 class="mt-3">EvilCorp CRM – User Lookup</h2>
        <p class="text-muted">Search users across the CRM system. Absolutely no injection here.</p>
    </div>
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search username..." required>
            <button class="btn btn-outline-success">Search</button>
        </div>
    </form>

    <?php if (!empty($results)): ?>
    <div class="card p-3">
        <h5>Results</h5>
        <table class="table table-bordered table-sm">
            <thead>
                <tr><th>ID</th><th>Username</th><th>Role</th></tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif (isset($_GET['q'])): ?>
        <p class="text-danger">No users found for "<?= htmlspecialchars($_GET['q']) ?>"</p>
    <?php endif; ?>
</div>
</body>
</html>
