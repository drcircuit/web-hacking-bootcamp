<?php
// admin.php
require_once 'db.php';
session_start();

$error = null;
$token = null;
$role = null;

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function generate_jwt($payload, $secret) {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $header_enc = base64url_encode(json_encode($header));
    $payload_enc = base64url_encode(json_encode($payload));
    $signature = base64url_encode(hash_hmac('sha256', "$header_enc.$payload_enc", $secret, true));
    return "$header_enc.$payload_enc.$signature";
}

function verify_jwt($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    list($header_enc, $payload_enc, $signature_enc) = $parts;
    $expected_sig = base64url_encode(hash_hmac('sha256', "$header_enc.$payload_enc", $secret, true));
    if (!hash_equals($expected_sig, $signature_enc)) return false;
    return json_decode(base64url_decode($payload_enc), true);
}

$jwt_secret = 'letmein'; // Weak secret students must crack with john

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = get_db();
    $stmt = $db->prepare('SELECT id, password FROM legacy_users WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && md5($password) === $user['password']) {
        // User authenticated — issue JWT with 'user' role
        $payload = [
            'user_id' => $user['id'],
            'username' => $username,
            'role' => 'user'
        ];
        $token = generate_jwt($payload, $jwt_secret);
        setcookie('token', $token);
        header('Location: admin.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
} elseif (isset($_COOKIE['token'])) {
    $payload = verify_jwt($_COOKIE['token'], $jwt_secret);
    if ($payload) {
        $role = $payload['role'] ?? 'user';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - EvilCorp</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<h1 class="mb-4">EvilCorp Admin Console</h1>

<?php if (!isset($_COOKIE['token'])): ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <?php if ($error): ?>
            <p class="text-danger mt-2"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </form>
    <?php elseif ($role === 'sysadmin'): ?>
    <div class="alert alert-success">
        <h4 class="alert-heading">Welcome sysadmin!</h4>
        <p>Flag: <strong>WCH{jwt_key_cracked_with_john}</strong></p>
        <p class="mt-3">New Vault Tools Available: <a href="vault_console.php">Access Vault Console</a></p>
    </div>
<?php else: ?>
    <p class="text-warning">Access denied. Only sysadmin role can view this panel.</p>
    <p><strong>Your Token:</strong> <code><?= htmlspecialchars($_COOKIE['token']) ?></code></p>
<?php endif; ?>

</body>
</html>
