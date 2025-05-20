<?php
$blob = $_GET['blob'] ?? '';
$input = base64_decode($_GET['blob'] ?? '');

echo "<pre>";
echo "Blob: $blob\n";
echo "Input: $input\n";
echo "</pre>";
$verified_portion = "access :::: thanos";
if (strpos($input, $verified_portion) !== false) {
    echo "✅ Flag: WCH{xor_marks_the_flaw}";
    echo "<br>";
    echo "step5_challenge.zip is up next...";
} else {
    echo "❌ Invalid blob.";
}
