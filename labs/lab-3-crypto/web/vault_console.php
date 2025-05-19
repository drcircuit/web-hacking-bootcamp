<?php
// vault_console.php
session_start();

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function verify_none_jwt($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;  // Expecting 3 parts even for 'none'

    $header = json_decode(base64url_decode($parts[0]), true);
    $payload = json_decode(base64url_decode($parts[1]), true);

    if (isset($header['alg']) && $header['alg'] === 'none') {
        return $payload;
    }
    return false;
}

$flag = null;
$error = null;

if (isset($_COOKIE['vault_token'])) {
    $payload = verify_none_jwt($_COOKIE['vault_token']);
    if ($payload && ($payload['level'] ?? '') === 'ultra') {
        $flag = 'WCH{jwt_alg_none_claim_check}';
    } else {
        $error = 'Vault access denied. Only ultra-level sysadmins may proceed.';
    }
} else {
    $error = 'Missing vault_token cookie.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vault Console</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<h1>🔐 EvilCorp Vault Console</h1>

<?php if ($flag): ?>
    <div class="alert alert-success mt-4">
        <strong>Access granted:</strong> <?= $flag ?>
    </div>
<?php else: ?>
    <div class="alert alert-danger mt-4">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
</body>
</html>
