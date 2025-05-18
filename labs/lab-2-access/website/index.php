<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evil Corp – Secret Encrypted Mail Archive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="container-fluid p-0">
    <!-- Hero Header -->
    <div class="hero-header d-flex align-items-center justify-content-center text-white text-center p-5"
         style="background-image: url('/images/image.png'); background-size: cover; height: 400px;">
        <div class="hero-overlay d-flex flex-column align-items-center">
            <img src="/images/logo.png" alt="Evil Corp Logo" width="100" class="mb-3">
            <h1 class="display-4">EVIL CORP</h1>
            <p class="lead">Secret Encrypted Mail Archive™</p>
        </div>
    </div>

    <!-- Mission Statement -->
    <section class="mission py-5 bg-dark text-white">
        <div class="container">
            <h2 class="text-center mb-4">Our Service</h2>
            <blockquote class="blockquote text-center">
                <p>"Your secrets are safe with us — until they aren’t."</p>
                <footer class="blockquote-footer">— The Evil Corp Security Team</footer>
            </blockquote>
        </div>
    </section>

    <!-- Access Section -->
    <section class="services py-5">
        <div class="container">
            <h2 class="text-center mb-4">Access Your Mail</h2>
            <div class="row justify-content-center g-4">
                <div class="col-md-4">
                    <div class="card bg-black border-success text-white p-3 text-center">
                        <h3>New User?</h3>
                        <p>Register to join the Evil Corp mail network.</p>
                        <a href="register.php" class="btn btn-outline-success">Register Now</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-black border-success text-white p-3 text-center">
                        <h3>Existing User?</h3>
                        <p>Log in to access your encrypted mail archive.</p>
                        <a href="login.php" class="btn btn-outline-success">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-center py-3 mt-5 text-muted small">
        <p>© 2025 Evil Corp. All rights reversed.</p>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
echo exec('whoami');
?>