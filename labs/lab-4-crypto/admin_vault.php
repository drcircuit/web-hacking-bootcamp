<?php
require 'session.php';
if (!isset($user) || $role !== 'admin') {
    http_response_code(403);
    die("Forbidden - admins only");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Vault - EvilCorp</title>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container">
  <h1>Welcome, <?= htmlspecialchars($user) ?>!</h1>
  <p class="alert alert-success">You've cracked the admin token!</p>
  <pre>WCH{cookie_monster_admin}</pre>
</div>
</body>
</html>
