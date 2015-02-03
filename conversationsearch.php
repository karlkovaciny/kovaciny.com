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
echo "<p class=\"copy\">These conversation titles matched your search terms.</P><p class=\"copy\"><a class=\"content\" href=\"" . HOST_NAME . "/search.php?q=$q_safe\" tabindex=\"15\">Search for comments instead</a></p>";
echo "<table border=0 cellpadding=0 cellspacing=0 class=\"indent medium\">";
echo "<tr class=\"small\"><td>Title</td><td>Most recent post</td></tr>";
echo "<tr bgcolor=\"#6699CC\"><td colspan=2><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
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
	if ($rc == true) {$rowcolor = ""; $rc = false;} else {$rowcolor = " bgcolor=\"#F6F6F6\""; $rc = true;}
//	if ($unread == 1) {
//		echo "<tr$rowcolor><td><img src=\"gfx/new.gif\" border=0 width=31 height=12 hspace=8></td><td class=\"rowpad\"><a href=\"conversations.php?id=$convid\">$contitle</a> ($numcomm)</td><td nowrap class=\"small rowpad sidepad\">$convdate ago by <a href=\"?user=$lastpostuserid\">$lastpostusername</a></td>";
//				echo "<td align=\"center\"><input type=\"checkbox\" onclick=\"document.forms.markasread.markasread.value='$convid';document.forms.markasread.submit();\" title=\"Check this box to mark conversation as read\"></td></tr>";
//		echo "<td align=\"center\"><input type=\"checkbox\" name=\"m[]\" value=\"$convid\"></td></tr>";
//	} else {
		echo "<tr$rowcolor><td class=\"rowpad sidepad\" ><a href=\"conversations.php?id=$convid\" tabindex=\"$tabindex\" >$contitle</a> ($numcomm)</td><td nowrap class=\"small rowpad sidepad\">$convdate ago</td></tr>";
}
echo "</td></tr></table>
</body>
</html>";
?>