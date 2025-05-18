<?php
session_start();
$db = new SQLite3(__DIR__ . '/../db/evilcorp_crm.sqlite');

// Access control: only allow admin users
if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$stmt = $db->prepare("SELECT role FROM users WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    die("Access denied.");
}

// Run ping or injected command
$host = $_GET['host'] ?? '127.0.0.1';
$output = shell_exec("ping -c 1 $host");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Ping Utility</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="p-4">
    <h1>Network Diagnostic – EvilCorp Internal</h1>
    <form method="get" class="mb-4">
        <label>Target Host/IP:</label>
        <input type="text" name="host" value="<?php echo htmlspecialchars($host); ?>" class="form-control">
        <button type="submit" class="btn btn-primary mt-2">Ping</button>
    </form>

    <h3>Output:</h3>
    <pre class="bg-dark text-white p-3"><?php echo htmlspecialchars($output); ?></pre>
</body>
</html>
