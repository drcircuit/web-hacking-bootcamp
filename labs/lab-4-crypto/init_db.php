<?php
$db = new SQLite3('vault.db');

// Create users table
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT,
    password_md5 TEXT,
    email TEXT
)");

// Create encrypted vault data table
$db->exec("CREATE TABLE IF NOT EXISTS vault (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    owner TEXT,
    title TEXT,
    encrypted_data TEXT
)");

// Add test users (password: password123)
$db->exec("INSERT INTO users (username, password_md5, email) VALUES (
    'alice', md5('password123'), 'alice@evilcorp.local'
)");
$db->exec("INSERT INTO users (username, password_md5, email) VALUES (
    'bob', md5('hunter2'), 'bob@evilcorp.local'
)");

// Add vault entry (ECB encrypted dummy)
$key = 'EVILCORPSTATICKEY';
$plaintext = 'TopSecretVaultFlag:WCH{evilcorp_vault_root}';
$encrypted = bin2hex(openssl_encrypt($plaintext, 'AES-128-ECB', $key, OPENSSL_RAW_DATA));
$db->exec("INSERT INTO vault (owner, title, encrypted_data) VALUES (
    'alice', 'encrypted_flag', '$encrypted'
)");

echo "Vault DB initialized.";
?>
