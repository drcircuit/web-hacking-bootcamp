<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Evil Corp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap @5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

<?php
$pageTitle = "Contact Us";
include 'header.php';
?>

<!-- Contact Content -->
<section class="py-5 bg-black text-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 offset-md-3">
                <h2 class="text-center">Contact Evil Corp</h2>
                <ul>
                    <li>Email: contact@evilcorp.local</li>
                    <li>Phone: +42 (666) 666-6666</li>
                    <li>Address: 666 Binary Lane, Hacker City</li>
                </ul>

                <h3>Need Support?</h3>
                <p>Try our internal support portal at <code>/admin.old</code> — but don't tell anyone we left it there.</p>

                <a href="/admin.old" class="btn btn-outline-success">Access Admin Portal</a>
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