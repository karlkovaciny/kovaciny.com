<?php
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/functions.php');

//authentication of sorts
if (!empty($_COOKIE['user'])) {
    $usertoken = $_COOKIE['user'];
} else {
    session_start();
    $usertoken = !empty($_SESSION['user']) ? $_SESSION['user'] : "";
}

if (empty($usertoken)) {
    http_error_response(401, "no usertoken supplied");
}   

require_once(dirname(__FILE__) . '/../dbconnect.php');

if (empty($_POST['username'])) {
    http_error_response(401, "no username supplied (type: string)");
}

if (empty($_POST['convIds'])) { http_error_response(400, "No conversation ids supplied (type: array of int");}

if (!isset($_POST['markasread']) || $_POST['markasread'] == null) { http_error_response(400, "No mark as read flag supplied (type: int, value: 1 or 0)");}

$username = mysql_real_escape_string($_POST['username']);
$readdate = !empty($_POST['readdate']) ? date(MYSQL_DATETIME_FORMAT, $_POST['readdate']) : date("MYSQL_DATETIME_FORMAT");   //converting from Unix timestamp
$convIds = $_POST['convIds'];    //array
$markAsRead = (int) $_POST['markasread']; //converting from string

$queries = [];

$convIds = $_POST['convIds'];
foreach ($convIds as $convId) {
    $convId = (int) $convId;
    $queries[] = "UPDATE `comments` SET `readby_$username` = $markAsRead WHERE `conid` = '$convId' and `changedate` <= '$readdate'";
} 

foreach($queries as $query) {
    $success = mysql_query($query);
    if (!$success) http_error_response(400, "Mysql error on query $query" . mysql_error());
}
?>
