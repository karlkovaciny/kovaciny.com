<?php
/*	// db Connection
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
	echo json_encode($userlist);*/
$jsontable = '{"cols": [
	{"id":"0", "label":"User ID", "type":"string"},
	{"id":"1", "label":"Username", "type":"string"}
],
"rows": [
	{"c":[{"v":"69d", "f":"c1"}, {"v":"c2", "f":null}]},
	{"c":[{"v":"2", "f":"f2-1"}, {"v":"c2-2", "f":null}]}
]

}';
echo $jsontable;
?>