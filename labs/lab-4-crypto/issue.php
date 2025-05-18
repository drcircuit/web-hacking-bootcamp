<?php
require_once 'jwt_lib.php';
$key = 'supersecret';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $payload = ['username' => $username, 'role' => $role];
    $jwt = JWT::encode($payload, $key);
    setcookie('jwt', $jwt);
    header("Location: admin_vault.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forge Token</title>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container">
  <h1>JWT Token Forge (internal)</h1>
  <form method="POST">
    <input name="username" placeholder="Username" required><br>
    <input name="role" placeholder="Role" required><br>
    <button>Forge Token</button>
  </form>
</div>
</body>
</html>
