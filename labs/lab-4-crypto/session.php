<?php
require_once 'jwt_lib.php';
$key = 'supersecret'; // reused key

if (isset($_COOKIE['jwt'])) {
    $payload = JWT::decode($_COOKIE['jwt'], $key);
    if ($payload && isset($payload['username'])) {
        $user = $payload['username'];
        $role = $payload['role'];
    }
}
?>
