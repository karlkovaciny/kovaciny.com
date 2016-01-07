<?php 
	require_once ("head.php");
    ini_set("display_errors", 1); 
try {
    $host = SQL_SERVER;
    $init = "mysql:host=$host;dbname=" . DATABASE . "; charset=latin1";
    $user = SERVER_USERNAME;
    $dbh = new PDO($init, $user, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    foreach($dbh->query('SELECT * FROM users') as $row) {
        echo $row['username'] . "'s token is " . $row['usertoken'] . "<BR>";
    }
}
catch(PDOException $e) {
    echo $e->getMessage();
}
   	
# close the connection
    $dbh = null;

/**
*   Content
*
*/
    echo "<div width=\"100%\" id=\"content\">content</div>";
    what();
    echoTableIndexes();
    
    
    echo "date.timezone: " . ini_get('date.timezone') . "<BR><BR>";
    
    
?>
</td></tr></table>
</body>
</html>
