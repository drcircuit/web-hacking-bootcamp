<?php
// Internal config
define('AES_KEY', 'supersecretkey123456');
define('JWT_SECRET', 'supersecretkey123456'); // 🔥 key reuse

// Flag from reused secret
echo "WCH{hardcoded_and_hearted}";
?>