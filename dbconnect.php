<?php
// db Connection
require_once('config.php');
$db = mysql_connect(SQL_SERVER, SERVER_USERNAME, DB_PASSWORD); 
if (!$db) {
    die('Not connected : ' . mysql_error());
}

$db_selected = mysql_select_db (DATABASE, $db) or die ('Can\'t select db : ' . mysql_error());

//synchronize Mysql and PHP
mysql_query("SET time_zone='" . date('P', time()) . "'");   
?>