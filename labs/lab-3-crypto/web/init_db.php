<?php
$db = new SQLite3('db/evilcorp_crm.sqlite');
$db->exec("DROP TABLE IF EXISTS legacy_users");
$db->exec("CREATE TABLE legacy_users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
$db->exec("INSERT INTO legacy_users (username, password) VALUES ('legacy_admin', '" . md5('letmein123') . "')");
echo "Database initialized.";
?>