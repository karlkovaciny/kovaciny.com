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
	/*
	//get a list of the usernames and ids 
	$res_users = mysql_query ("SELECT `userid`, `username` FROM `users`") or die ("Error getting usernames: " . mysql_error() . "<br />");
	while ($row=mysql_fetch_array($res_users)) {
		$key = $row["userid"];
		$userlist[$key] = $row["username"];
	}
	
	$sql = "SELECT COUNT(1) AS theCommentCount, `authorid`, `visible`, `createdate` FROM `comments`
		WHERE `visible`='Y' AND DATEDIFF(CURDATE(), `createdate`) <= 7
		GROUP BY `authorid` ORDER BY theCommentCount DESC";
	$res_comment_count = mysql_query($sql) or die ("Error getting comment count: " . mysql_error() . "<br/>");
	*/
	$cols = array();
	$cols[] = array("id"=>"", "label"=>"Username", "type"=>"string");
	$cols[] = array("id"=>"", "label"=>"Searchlink", "type"=>"string");
	$cols[] = array("id"=>"", "label"=>"Posts", "type"=>"number");
	
	$rows = array();
	$rows[] = array("c"=>array(array("v"=>"MyName"), array("v"=>"http://"), array("v"=>4)) );
	$rows[] = array("c"=>array(array("v"=>"MyName2"), array("v"=>"http://"), array("v"=>44)) );
	$table = array("cols"=>$cols, "rows"=>$rows);
	$jsontable = json_encode($table);
	/*echo "data.addRows([";
	while ($row2 = mysql_fetch_array($res_comment_count)) {
		$username = $userlist[$row2['authorid']];
		echo "['" . $username . "', " . 
			"\"search.php?q_author=" . $row2['authorid'] . "&q_timeframe=week&q_title=&q_matchAllComments=matchall\", " .
			$row2['theCommentCount'] . "],\n";
	}
	echo "['', '', 0]"; //hack because of trailing comma issue
	echo "]);";
	
	?>
	
	var view = new google.visualization.DataView(data);
	view.setColumns([0, 2]); //because we are storing the link in a hidden column
*/
echo $jsontable;
?>
