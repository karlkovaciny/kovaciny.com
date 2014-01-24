<?php
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
	
	//get a list of the usernames and ids 
	$res_users = mysql_query ("SELECT `userid`, `username` FROM `users`") or die ("Error getting usernames: " . mysql_error() . "<br />");
	while ($row=mysql_fetch_array($res_users)) {
		$key = $row["userid"];
		$userlist[$key] = $row["username"];
	}
	
	$timeframe = (int) $_GET['timeframe']; //data sanitization shortcut
	if ($timeframe <= 0) {
	    header('HTTP/1.1 500 Internal Server Booboo');
        header('Content-Type: application/json');
        die('ERROR');
	}
	
	$sql = "SELECT COUNT(1) AS theCommentCount, `authorid`, `visible`, `createdate` FROM `comments`
		WHERE `visible`='Y' AND DATEDIFF(CURDATE(), `createdate`) <= $timeframe 
		GROUP BY `authorid` ORDER BY theCommentCount DESC";
	$res_comment_count = mysql_query($sql) or die ("Error getting comment count: " . mysql_error() . "<br/>");
	$table = array();
	$table['cols'] = array(
			array('id'=>"", 'label'=>"Username", 'type'=>"string"),
			array('id'=>"", 'label'=>"Searchlink", 'type'=>"string"), 
			array('id'=>"", 'label'=>"Posts", 'type'=>"number")
		);
	
	$rows = array();
	while ($row2 = mysql_fetch_array($res_comment_count)) {
		$username = $userlist[$row2['authorid']];
		$cells = array();
		$cells[] = array('v'=>$username);
		$cells[] = array('v'=>"search.php?q_author=" . $row2['authorid'] . "&q_timeframe=$timeframe&q_title=&q_matchAllComments=matchall");
		$cells[] = array('v'=>(int)$row2['theCommentCount']);
		$rows[] = array('c'=>$cells);
	}
	$table['rows'] = $rows;
	
	$jsontable = json_encode($table);
	echo $jsontable;
?>