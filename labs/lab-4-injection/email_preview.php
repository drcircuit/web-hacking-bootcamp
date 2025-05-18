<?php
session_start();
$db = new SQLite3(__DIR__ . '/db/evilcorp_crm.sqlite');
$template = $_GET['template'] ?? 'templates/default_email.php';

$leakPath = __DIR__ . '/templates/leaked_config.php';

// Create the file only if it's missing AND user is admin
if (!file_exists($leakPath)) {

    if (isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT role FROM users WHERE id = :id");
        $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user && $user['role'] === 'admin') {
            file_put_contents($leakPath, "<?php echo 'FLAG: WCH{tpl_secret_leak_detected}'; ?>");
            chmod($leakPath, 0644);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Template Preview – EvilCorp CRM</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="p-4">
    <h1>Email Template Preview</h1>
    <p>Use this tool to preview different email templates stored on disk.</p>

    <form method="get" class="mb-4">
        <label for="template">Template Path:</label>
        <input type="text" id="template" name="template" value="<?php echo htmlspecialchars($template); ?>" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Load Template</button>
    </form>

    <hr>

    <h3>Preview:</h3>
    <div class="email-preview bg-dark text-white p-4">
        <?php
        if (file_exists($template)) {
            include($template);
        } else {
            echo "<p class='text-danger'>Template not found: " . htmlspecialchars($template) . "</p>";
        }
        ?>
    </div>
</body>
</html>
