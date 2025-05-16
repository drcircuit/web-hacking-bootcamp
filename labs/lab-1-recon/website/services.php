<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap @5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

<?php
$pageTitle = "Our Services";
include 'header.php';
?>

<!-- Services Section -->
<section class="services py-5 bg-dark text-white">
    <div class="container">
        <h2 class="text-center mb-4">Our Services</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card bg-black border-success text-white p-3">
                    <h3>Decentralized Banking</h3>
                    <p>We're redefining finance without central banks. Or trust.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-black border-success text-white p-3">
                    <h3>Dark Web Integration</h3>
                    <p>We integrate with dark web markets for true decentralization.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-black border-success text-white p-3">
                    <h3>Password Policy Enforcement</h3>
                    <p>We enforce strong passwords. Like 'password123'.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Investor Quote -->
<section class="investor-quote py-5 text-center">
    <div class="container">
        <blockquote class="blockquote">
            <p>"They're unhackable. Probably."</p>
            <footer class="blockquote-footer">– Anonymous Security Auditor</footer>
        </blockquote>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-center py-3 mt-5 text-muted small">
    <p>&copy; 2025 Evil Corp. All rights reversed.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap @5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>