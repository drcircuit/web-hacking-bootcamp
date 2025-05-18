<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$template = '';
$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template = $_POST['template'];

    // VULNERABLE: Twig eval without sandboxing
    require_once 'vendor/autoload.php';
    $loader = new \Twig\Loader\ArrayLoader([
        'preview' => $template
    ]);
    $twig = new \Twig\Environment($loader, ['debug' => true]);

    try {
        $output = $twig->render('preview');
    } catch (Exception $e) {
        $output = 'Template error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Preview - EvilCorp CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <img src="/images/logo.png" width="80">
        <h2 class="mt-3">EvilCorp Email Previewer</h2>
        <p class="text-muted">Interns made this... what could go wrong?</p>
    </div>
    <form method="POST">
        <div class="mb-3">
            <label for="template" class="form-label">Email Template (Twig)</label>
            <textarea class="form-control" name="template" rows="5" required><?= htmlspecialchars($template) ?></textarea>
        </div>
        <button class="btn btn-danger">Render</button>
    </form>

    <?php if ($output): ?>
        <div class="card mt-4 p-3">
            <h5>Rendered Output:</h5>
            <div><?= $output ?></div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
