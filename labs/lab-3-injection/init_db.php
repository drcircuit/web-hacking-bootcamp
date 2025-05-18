<?php
$db = new SQLite3(__DIR__ . '/db/evilcorp_crm.sqlite');

$db->exec("DROP TABLE IF EXISTS users");
$db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, role TEXT)");

$db->exec("INSERT INTO users (username, password, role) VALUES
  ('alice', 'alicepass', 'user'),
  ('bob', 'bobpass', 'user'),
  ('crmadmin', 'WCH{user_hash_leak_sql_master}', 'crmadmin'),
  ('thanos', 'infinity', 'admin')");

$db->exec("DROP TABLE IF EXISTS emails");
$db->exec("CREATE TABLE emails (id INTEGER PRIMARY KEY, user_id INTEGER, subject TEXT, body TEXT)");

$db->exec("INSERT INTO emails (user_id, subject, body) VALUES
  (1, 'Welcome to EvilCorp Mail', 'FLAG: WCH{you_got_mail_from_the_dark_side}'),
  (2, 'Confidential', 'Meet me at the usual place. FLAG: WCH{message_read_bypass_granted}')");

$db->exec("DROP TABLE IF EXISTS logs");
$db->exec("CREATE TABLE logs (id INTEGER PRIMARY KEY, message TEXT)");
$db->exec("INSERT INTO logs (message) VALUES ('System initialized...'), ('FLAG: WCH{config_found_lfi_your_way_in}')");
$db->exec("CREATE TABLE flag_storage (id INTEGER PRIMARY KEY, flag TEXT)");
$db->exec("INSERT INTO flag_storage (flag) VALUES ('WCH{hidden_table_secretly_yours}')");

echo "Database initialized!";
?>
