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
echo "<tr class=\"small\"><td>Conversation</td><td>Most recent post</td></tr>";
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
			<td class=\"searchResults\" >
				<div class=\"slideme\" style=\"display: visible\">
			<a href=\"conversations.php?id=$convid\" tabindex=\"$tabindex\" >$contitle</a> ($numcomm)</td>
			<td class=\"searchResults\">
				<div class=\"slideme\" style=\"display: visible\">
			$convdate ago</td></tr>";
}
echo "</td></tr>
		<tr><td colspan=2 class=\"tableExpander\"><a href=\"#\" id=\"moreConversationsLink\">Show more conversations</a></td></tr>
	</table>";
?>

<script>
	//show only the first four conversation titles, with a link to show more results
	$(document).ready(function(){
		var extraRows = $( "tr.searchResults" ).filter(function( index ){
			return index > 3;
		});
		if (extraRows.length) {
			extraRows.hide();
			$('#moreConversationsLink').show();
		} else { 
			$('#moreConversationsLink').hide();
		}
		
		$('#moreConversationsLink').click( function(){
			$('#moreConversationsLink').hide();
			extraRows.show(); 
			extraRows.find(".slideme").hide();
			extraRows.find(".slideme").slideDown();
			$('table.searchResults')[0].scrollIntoView();
			return false;
		});
	});
</script>

<?php
echo "<p class=\"copy\"><a class=\"content\" href=\"" . HOST_NAME . "/search.php?q=$q_safe\" tabindex=\"15\">Search for comments instead</a></p>";
echo "
</body>
</html>";
?>
