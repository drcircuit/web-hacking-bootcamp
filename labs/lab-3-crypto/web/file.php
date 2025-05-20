<?php
$blob = shell_exec("python3 /scripts/gen_blob.py");
$blob = trim($blob); // remove trailing newline

header('Content-Type: application/json');
echo json_encode([
    "blob" => $blob,
    "hint" => "The message contains a name you know. veryfy it with the verify.php?blob=blob script.",
]);
