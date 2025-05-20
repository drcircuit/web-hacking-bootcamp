<?php
session_start();

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function parse_jwt($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    $payload = json_decode(base64url_decode($parts[1]), true);
    return $payload;
}

if ($_SESSION['role'] !== 'vault_console_operator') {
    echo "🔐 Clearance required. Return to inbox.";
    exit;
}

$flag = null;
$error = null;

if (isset($_COOKIE['token'])) {
    $payload = parse_jwt($_COOKIE['token']);
    if ($payload && ($payload['level'] ?? '') === 'ultra') {
        $flag = 'WCH{n0ne_is_too_many}';
    } else {
        $error = '❌ Only ultra special levels accepted.';
    }
} else {
    $error = '❌ Missing JWT.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Vault Console</title></head>
<body>
<h1>Vault Console</h1>
<?php if ($flag): ?>
    <h3>✅ Challenge 3 Complete</h3>
    <p>Flag: <?= $flag ?></p>
    <p>More recon for next step...
<?php else: ?>
    <p><?= $error ?></p>
<?php endif; ?>
</body>
</html>