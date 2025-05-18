<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';
    $db = get_db();

    // Enforce EvilCorp domain restriction
    if (!str_ends_with($username, '@evilcorp.local')) {
        $error = 'No outsiders allowed, evilcorp.local executives only!';
    } else {
        // Check if the username already exists
        $check_stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $check_stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $check_result = $check_stmt->execute();
        $count = $check_result->fetchArray()[0];

        if ($count > 0) {
            $error = 'Username already exists. Please choose a different username.';
        } else {
            // Insert new user
            $stmt = $db->prepare('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->bindValue(':password', $password, SQLITE3_TEXT);
            $stmt->bindValue(':role', $role, SQLITE3_TEXT);
            $result = $stmt->execute();

            if ($db->changes() > 0) {
                $user_id = $db->lastInsertRowID();
                $subject = 'Welcome to EvilCorp';
                $body = "Welcome, $username! You have been granted the role: $role. Flag: WCH{you_got_mail_from_the_dark_side}";
                $email_stmt = $db->prepare('INSERT INTO emails (user_id, subject, body) VALUES (:user_id, :subject, :body)');
                $email_stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
                $email_stmt->bindValue(':subject', $subject, SQLITE3_TEXT);
                $email_stmt->bindValue(':body', $body, SQLITE3_TEXT);
                $email_stmt->execute();
                header('Location: login.php');
                exit;
            } else {
                $error = 'Failed to insert user. Error: ' . $db->lastErrorMsg();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Evil Corp Mail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Register</h1>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Email</label>
            <input type="email" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <?php if (isset($error)): ?>
            <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
</div>
</body>
</html>
