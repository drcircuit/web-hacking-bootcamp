<?php
$user = "admin";
$time = time();
$token = base64_encode($user . ":" . $time);
echo "<h1>🔁 Reset Token Generator</h1>";
echo "<p>Generated token: <code>$token</code></p>";
echo "<p><strong>Flag:</strong> WCH{token_predicted_pw_resetted}</p>";
?>