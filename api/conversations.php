<?php
function http_error_response($code, $message) {
    header('X-PHP-Response-Code: $code', true, $code);
    echo "{\"code\":$code, \"message\":\"$message\"}";
    echo "\n";
    var_dump($_POST);
    die();
}

//authentication of sorts
if (!empty($_COOKIE['user'])) {
    $usertoken = $_COOKIE['user'];
} else {
    session_start();
    $usertoken = !empty($_SESSION['user']) ? $_SESSION['user'] : "";
}

if (empty($usertoken)) {
    $msg = '{"code":401, "message":"no usertoken supplied"}';
    var_dump($_POST); 
    http_error_response(401, $msg);
}   
         
//	ini_set('display_errors', 1);
// db Connection
$db = mysql_connect('localhost', '***REMOVED***', '***REMOVED***'); //no error, yes connected
if (!$db) {
    die('Not connected : ' . mysql_error());
}

$db_selected = mysql_select_db ("***REMOVED***", $db);
if (!$db_selected) {
    die ('Can\'t select db : ' . mysql_error());
}
$tz = -12;

if (empty($_POST['username'])) {
    $msg = '{"code":401, "message":"no username supplied"}' . '<BR><PRE>' . var_dump($_POST) . '</PRE>'; 
    http_error_response(401, $msg);
}

$username = mysql_real_escape_string($_POST['username']);
$readdate = !empty($_POST['readdate']) ? mysql_real_escape_string($_POST['readdate']) : NULL;
$queries = [];

if (isset($_POST['convIds'])) { 
    $markasread = $_POST['convIds'];
    if (is_array($markasread)) {
        foreach ($markasread as $markas) {
            $markas = mysql_real_escape_string($markas);
            $queries[] = "UPDATE `comments` SET `readby_$username` = 1 WHERE `conid` = '$markas' and `changedate` <= '" . date('Y-m-d H:i:s', $readdate) . "'";
        }
    }
} elseif (isset($_POST['markasread']) and isset($_POST['readdate'])) { 
    $markasread = mysql_real_escape_string($_POST['markasread']);
    $queries[] = "UPDATE `comments` SET `readby_$username` = 1 WHERE `conid` = '$markasread' and `changedate` <= '" . date('Y-m-d H:i:s', $readdate) . "'";
} else {
    $msg = "Bad POST request"; 
    http_error_response(400, $msg);
}

foreach($queries as $query) {
    $success = mysql_query($query);
    if (!$success) http_error_response(400, "Mysql error on query $query" . mysql_error());
}
?>
