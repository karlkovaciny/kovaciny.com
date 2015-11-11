<?php
require_once('access_token.php'); //in lieu of a real one
require_once('dbconnect.php');
if (empty($_POST['username'])) {
    http_error_response(401, "no username supplied (type: string)");
}

if (empty($_POST['convIds'])) { http_error_response(400, "No conversation ids supplied (type: array of int");}
if (empty($_POST['readDates'])) { http_error_response(400, "No read dates supplied (type: array of MySql datetimes");}
if (count($readdates) != count($convIds)) { http_error_response(400, "Each conversation needs to have a corresponding mark as read date."); }

$username = mysql_real_escape_string($_POST['username']);
$convIds = $_POST['convIds'];
$readdates = $_POST['readDates'];
$queries = [];
foreach ($convIds as $convId) {
    $convId = (int) $convId;
    foreach($readdates as $readdate) {
        $queries[] = "UPDATE `comments` SET `readby_$username` = (`changedate` <= '$readdate') WHERE `conid` = '$convId'";
    }
} 

foreach($queries as $query) {
    $success = mysql_query($query);
    if (!$success) http_error_response(400, "Mysql error on query $query" . mysql_error());
}
?>