<?php
// db.php - handles database connection
function get_db() {
    $db_path = __DIR__ . '/db/evilcorp_crm.sqlite';
    $db = new SQLite3($db_path);
    return $db;
}
