<?php
session_start();
if ($_SESSION['user'] !== 'thanos') {
    echo "<h1>⛔ Access Denied</h1>";
    exit;
}
echo "<h1>💣 Final Payload</h1>";
echo "<pre>Backup: /etc/shadow.bak
";
echo "Hint: Try cracking thanos' password using rockyou.
";
echo "SSH is running on port 22.
";
echo "</pre>";
echo "<p><strong>Flag:</strong> WCH{evilcorp_executed_crypto}</p>";
?>