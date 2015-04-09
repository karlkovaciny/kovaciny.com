<?php	
require_once('config.php');
$db = mysql_connect(SQL_HOST, DATABASE, DB_PASSWORD); 
mysql_select_db (DATABASE);
$tz = -12;

require_once('functions.php');

$q = stripslashes($_GET['q']);
$q_searchstring = preprocessForSqlBoolean($q);
$sql = "SELECT `conid`, `contitle`, `changedate`, `createdate`, `numcomm`, `visible` FROM `conversations` 
		WHERE `visible`='Y' AND MATCH `contitle` AGAINST ('$q_searchstring' IN BOOLEAN MODE) ORDER BY `changedate` DESC";
$res = mysql_query($sql) or die (mysql_error());
if( mysql_num_rows ($res) == 0 ) {
	$redirect = "Location:" . HOST_NAME . "/search.php?q=$q";
	header($redirect);
}
require_once("head.php");
$q_safe = htmlentities($q);
echo "<h1 style=\"padding-top: 7px\">Search Results</h1>";
echo "<p class=\"copy\">These conversation titles matched your search terms.</P>";
echo "<p id=\"newConversationLink\"><a class=\"content\" href=\"newconv.php\">Add new conversation</a>&nbsp;</p>";
echo "<table class=\"searchResults\">";
echo "<tr class=\"small\"><td>Title</td><td>Most recent post</td></tr>";
echo "<tr class=\"blueHR\"><td colspan=2 class=\"blueHR\"><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
$tabindex = 10;
while($convs = mysql_fetch_array($res)) {
	$tabindex += 10;
	$convdate = $convs["changedate"];
	$convid = $convs["conid"];
	$contitle = $convs["contitle"];
	$convis = $convs["visible"];
	$numcomm = $convs["numcomm"];
	$convdate = format_interval(time() - strtotime($convdate));
	$convdate = str_replace(array(" months", " month", " weeks", " week", " days", " day", " hrs", " hr", " min", " sec"),array("mo", "mo", "w", "w", "d", "d", "h", "h", "m", "s"),$convdate);
	echo "<tr class=\"searchResults\">
			<td class=\"searchResults\" ><a href=\"conversations.php?id=$convid\" tabindex=\"$tabindex\" >$contitle</a> ($numcomm)</td>
			<td class=\"searchResults\">$convdate ago</td></tr>";
}
echo "</td></tr></table>";
echo "<script>
</script>";

echo "<p class=\"copy\"><a class=\"content\" href=\"" . HOST_NAME . "/search.php?q=$q_safe\" tabindex=\"15\">Search for comments instead</a></p>";
echo "
</body>
</html>";
?>
