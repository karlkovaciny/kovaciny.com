<?php
ini_set('display_errors', 1);
define('HOME_DIR', dirname(__FILE__) . '/../');
require_once(HOME_DIR . 'config.php');
require_once(HOME_DIR . 'functions.php');
require_once(HOME_DIR . 'functions.php');
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
echo "<html><head><link rel='stylesheet' type='text/css' href='" . HOST_NAME . "/kovaciny.css'><body>";

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

require_once(HOME_DIR . 'config.php');
require_once(HOME_DIR . 'dbconnect.php');

//TODO: con should be on changedate for the index and createdate for conversations. currently has changedate and contitle, which should be dropped.
//com on createdate for the search and conversations (with inreplyto :( ) . currently has two fulltext on comment, i should get rid of index comment_2

// table names, number of rows, etc
//echo_mysql("SHOW TABLE STATUS FROM " . DATABASE);

//indexes etc
//echo_mysql("SELECT * FROM information_schema.statistics");

//field types, nullable, etc. 'Key: MUL' = indexed.
//echo_mysql("DESCRIBE conversations;");
//echo_mysql("UPDATE `comments` SET `readby_karl` = (`changedate` <= '2015-11-01 12:24:24') WHERE `conid` = '1655'");
//echo_mysql("SELECT '2015-11-05 12:24:24' <= '2015-11-01 12:24:24'");
$conv = '2037';
echo_mysql("SELECT * lastread FROM comments WHERE readby_karl = 0 AND conid = $conv");
//echo_mysql("SHOW CREATE TABLE users;");

//echo_mysql("SHOW GRANTS FOR CURRENT_USER();", 'scalar');
//echo_mysql("SELECT * FROM comments PROCEDURE ANALYSE();");
//echo_mysql("SELECT version();", 'scalar');
//echo_mysql("SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP)");
//echo_mysql("SELECT @@global.time_zone", 'scalar');

//echo_mysql("SHOW VARIABLES LIKE 'myisam_sort_buffer_size';");

//echo_mysql("ALTER TABLE conversations ADD INDEX (`changedate`);");
$queryorig = "SELECT con.conid FROM conversations AS con JOIN comments AS com ON con.conid = com.conid AND com.changedate >= con.changedate WHERE con.visible = 'Y' AND com.readby_karl = 1 AND con.conid != '1630' AND con.conid != '1715' AND con.conid != '1720' AND con.conid != '1921' AND con.conid != '2014' AND con.conid != '1856' ORDER BY con.changedate DESC";
$subquery = "SELECT com.conid, MIN(com.readby_karl) AS allread FROM comments AS com WHERE com.visible = 'Y' GROUP BY com.conid HAVING allread = 1";
$query = "SELECT comments.* FROM comments WHERE comments.conid = '589'";
//$query = "EXPLAIN SELECT con.* FROM conversations AS con JOIN ($subquery) AS readthreads ON con.conid = readthreads.conid WHERE con.visible = 'Y' ORDER BY con.changedate DESC"; //1905
/*$difference = "SELECT newq.conid FROM ($query) AS newq LEFT JOIN ($queryorig) AS orig ON newq.conid = orig.conid WHERE orig.conid IS NULL"; //missing 1984 Lib, 1715 Testing, who cares, that's the orig!
$query = "SELECT conversations.* FROM conversations JOIN ($difference) AS diff ON conversations.conid = diff.conid";
*/
//$query = "SELECT quer.contitle FROM ($query) AS quer WHERE quer.contitle NOT IN ($queryorig)";

// not matching "Automobile life expectancy" or "Videos for David to watch"
//$query = "SELECT COUNT(*) FROM ($query) AS whatever;"; //1920, minus DISTINCT 127226, 1.747 sec com.changedate >= con.changedate -> 1977, com.changedate > con.changedate 242, 

//echo_mysql("CREATE TABLE monica2 LIKE users;");
//echo_mysql("DESCRIBE monica2");
//echo_mysql("INSERT INTO monica (_id, nickname, naughtiness) VALUES ('100', 'sissybutt', '9');"); 
//echo_mysql("SELECT * FROM monica ORDER BY naughtiness");
// connect to FTP server
$ftp_server = "***REMOVED***";
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$ftp_username = DATABASE;
$ftp_userpass = "***REMOVED***";
$filename = "ftp://$ftp_username:$ftp_userpass@localhost/login.php";
/*$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);

echo $contents;
*/
// login
/*
if (ftp_login($ftp_conn, $ftp_username, $ftp_userpass))
  {
  echo "Connection established.<BR>";
  }
else
  {
  echo "Couldn't establish a connection.";
  }

*/
// do something...

/*$dir = '/var/lib/mysql/';
echo "dir = $dir<BR>";
$files = scandir($dir);
if ($files) var_dump($files); else echo "scandir failed<BR>";*/
echo "</body></html>";   
?>