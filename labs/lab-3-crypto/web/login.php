<?php
session_start();
$db = new SQLite3('db/evilcorp_crm.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // 🔓 Weak hashing

    $stmt = $db->prepare("SELECT * FROM legacy_users WHERE username = :username AND password = :password");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user) {
        $_SESSION['user'] = $username;
        header("Location: inbox.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html><html><body>
<h1>EvilCorp Legacy Login</h1>
<form method="post">
<input name="username" placeholder="Username"><br>
<input type="password" name="password" placeholder="Password"><br>
<button type="submit">Login</button>
</form>
<?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
</body></html>