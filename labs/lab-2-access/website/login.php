<?php
require 'db.php';
require 'jwt.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray();
    if ($user && password_verify($password, $user['password'])) {
        $jwt = new JWT('evilcorp');
        $token = $jwt->encode(['user_id' => $user['id'], 'role' => $user['role']]);
        setcookie('token', $token, time() + 3600, '/', '', false, true);
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Evil Corp Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Login</h1>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <?php if (isset($error)): ?>
            <p class="text-danger"><?php echo $error; ?></p>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
</body>
</html>