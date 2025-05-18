<?php
session_start();
$db = new SQLite3('vault.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // WEAK

    $query = "SELECT * FROM users WHERE username = '$username' AND password_md5 = '$password'";
    $result = $db->query($query);

    if ($user = $result->fetchArray()) {
        $_SESSION['user'] = $user['username'];
        header("Location: vault.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – EvilCorp Vault</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container">
    <h1>EvilCorp Vault</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required /><br>
        <input type="password" name="password" placeholder="Password" required /><br>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>
</body>
</html>
