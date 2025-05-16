<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Evil Corp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap @5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

<?php
$pageTitle = "About Us";
include 'header.php';
?>

<!-- About Content -->
<section class="mission py-5 bg-dark text-white">
    <div class="container">
        <h2 class="text-center mb-4">Who We Are</h2>
        <p class="lead text-center">We're a next-gen fintech company building secure, decentralized finance solutions.</p>

        <div class="row mt-5">
            <div class="col-md-8 offset-md-2">
                <div class="card bg-black border-success text-white p-3">
                    <h3>Mission Statement</h3>
                    <p>"To dominate global finance through innovation, disruption, and strategic acquisitions."</p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Contact Info</h3>
                <ul>
                    <li>Email: contact@evilcorp.local</li>
                    <li>Admin: admin@company.local</li>
                    <li>Dev: dev@evilcorp.local</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Internal Note</h3>
                <small class="text-muted">
                    Old dev file path: /admin.old<br>
                    Backup config path: /backup/config.php.bak
                </small>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-center py-3 mt-5 text-muted small">
    <p>&copy; 2025 Evil Corp. All rights reversed.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap @5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>