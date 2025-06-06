<?php
require_once 'db.php';

function encrypt_vault_message($plaintext, $key, $outputFile) {
    $ivlen = openssl_cipher_iv_length($cipher = "aes-256-cbc");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $encrypted = base64_encode($iv . $ciphertext);
    file_put_contents($outputFile, $encrypted);
}

$db = get_db();
$db->exec("DROP TABLE IF EXISTS legacy_users;");
$db->exec("DROP TABLE IF EXISTS emails;");

$db->exec("CREATE TABLE legacy_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL
);");

$db->exec("CREATE TABLE emails (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    subject TEXT,
    body TEXT
);");
$db->exec("CREATE TABLE config (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL
);");

$db->exec("INSERT INTO config (key, value) VALUES ('vault_key', 'vault_s3cr3t_key');");

// Create legacy admin
$username = 'legacy_admin';
$password = md5('letmein123');
$role = 'user';
$stmt = $db->prepare('INSERT INTO legacy_users (username, password, role) VALUES (:u, :p, :r)');
$stmt->bindValue(':u', $username);
$stmt->bindValue(':p', $password);
$stmt->bindValue(':r', $role);
$stmt->execute();
$user_id = $db->lastInsertRowID();

// Send welcome email
$db->exec("INSERT INTO emails (user_id, subject, body) VALUES (
    $user_id,
    'Welcome to EvilCorp CRM',
    'Thanks for signing in. Your legacy access has been restored.\n\nFLAG: WCH{md5_was_a_mistake}'
);");

// Encrypt and store vault message
$vaultMessage = file_get_contents(__DIR__ . "/vault_plain.txt");
$key = "vault_s3cr3t_key";
$outputFile = __DIR__ . "/encrypted/vault_message.enc";
@mkdir(__DIR__ . "/encrypted");
encrypt_vault_message($vaultMessage, $key, $outputFile);

$plaintext = str_repeat("ATTACK_BLOCK", 2) . "| Flag: WCH{ecb_mode_block_breach}";
$key = "ecb_secret_key123"; // 16 bytes = 128-bit key
$method = "AES-128-ECB";

$ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA);
file_put_contents(__DIR__ . '/encrypted/leak_ecb.enc', bin2hex($ciphertext));

// Send vault email with download link
$vaultURL = "http://lab3.evilcorp.local/encrypted/vault_message.enc";
$vaultBody = "Download the vault message here:\n$vaultURL";
$stmt = $db->prepare('INSERT INTO emails (user_id, subject, body) VALUES (:uid, :subject, :body)');
$stmt->bindValue(':uid', $user_id);
$stmt->bindValue(':subject', 'Encrypted Vault Note');
$stmt->bindValue(':body', $vaultBody);
$stmt->execute();

echo "✅ Database initialized. Encrypted message created at: $outputFile\n";