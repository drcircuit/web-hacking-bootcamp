<?php
function get_db() {
    $db = new SQLite3(__DIR__ . '/db/maildb.sqlite');
    return $db;
}
?>