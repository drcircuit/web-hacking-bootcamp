<?php
session_start();
$db = new SQLite3('evilcorp_crm.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // VULNERABLE: SQL Injection
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $db->query($query);

    if ($row = $result->fetchArray()) {
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: search.php");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - EvilCorp CRM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <div class="text-center">
        <img src="/images/logo.png" width="100" />
        <h1 class="display-5 mt-3">EvilCorp CRM</h1>
        <p class="text-muted">Trusted. Secure. Totally not broken.</p>
    </div>
    <div class="card p-4 mx-auto" style="max-width: 400px;">
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input class="form-control" type="text" name="username" id="username" required />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input class="form-control" type="password" name="password" id="password" required />
            </div>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
