<?php
$db_path = __DIR__ . '/db/maildb.sqlite';
$db = new SQLite3($db_path);

// Create users table
$db->exec('CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL
)');

// Create emails table
$db->exec('CREATE TABLE IF NOT EXISTS emails (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    subject TEXT,
    body TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
)');

// Insert sample users
$password_hash = password_hash('password', PASSWORD_DEFAULT);
$db->exec("INSERT OR IGNORE INTO users (username, password, role) VALUES ('alice', '$password_hash', 'user')");
$db->exec("INSERT OR IGNORE INTO users (username, password, role) VALUES ('bob', '$password_hash', 'user')");
$db->exec("INSERT OR IGNORE INTO users (username, password, role) VALUES ('admin', '$password_hash', 'group_admin')");
$db->exec("INSERT OR IGNORE INTO users (username, password, role) VALUES ('thanos', '$password_hash', 'mail_server_admin')");

// Insert sample emails
$db->exec("INSERT OR IGNORE INTO emails (user_id, subject, body) VALUES (1, 'Welcome', 'Welcome to EvilCorp, Alice! Flag: WCH{you_got_mail_from_the_dark_side}')");
$db->exec("INSERT OR IGNORE INTO emails (user_id, subject, body) VALUES (2, 'Role Assignment', 'You have been assigned the role: group_admin. Our JWT secret is as weak as our security. Flag: WCH{message_read_bypass_granted}')");
$db->exec("INSERT OR IGNORE INTO emails (user_id, subject, body) VALUES (3, 'Admin Notice', 'Admin tasks pending. Flag: WCH{admin_flag}')");
$db->exec("INSERT OR IGNORE INTO emails (user_id, subject, body) VALUES (4, 'System Security Alert', 'Secret for JWT is very weak... think if Google just used \"google\" as their secret. Flag: WCH{evilcorp_signing_key_found}')");

$db->close();
?>