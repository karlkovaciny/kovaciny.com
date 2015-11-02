<?php
// db Connection
require_once('config.php');
$db = mysql_connect(SQL_HOST, DATABASE, DB_PASSWORD); //no error, yes connected
if (!$db) {
    die('Not connected : ' . mysql_error());
}

$db_selected = mysql_select_db (DATABASE, $db);
if (!$db_selected) {
    die ('Can\'t select db : ' . mysql_error());
}

mysql_query("SET time_zone='" . date('P', time()) . "'");   //synchronize Mysql and PHP
?>