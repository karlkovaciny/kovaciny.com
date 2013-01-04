<?php
require_once ('head.php');
echo "<font size=\"-2\">You are now on the development branch of the kovacinycom</FONT>";
if ($username) {
	if (isset($_POST['m'])) {
		$markasread = $_POST['m'];
		if (is_array($markasread)) {
			foreach ($markasread as $markas) {
				mysql_query("UPDATE `comments` SET `readby_$username` = 1 WHERE `conid` = '$markas' and `changedate` <= '" . date('Y-m-d H:i:s', $_POST['readdate']) . "'");
			}
		}
	} elseif (isset($_POST['markasread']) and isset($_POST['readdate'])) {
		$markasread = $_POST['markasread'];
		mysql_query("UPDATE `comments` SET `readby_$username` = 1 WHERE `conid` = '$markasread' and `changedate` <= '" . date('Y-m-d H:i:s', $_POST['readdate']) . "'");
	}
	if (isset($_GET['showall'])) {$showall = "";} else {$showall = "LIMIT 25";}
	$unread = 0;
	while ($unread != 2) {
		$tdspacer = "****";
		if ($unread == 0) {
			$unread = 1;
			$norepeats = "";
			$trspacer = "<tr height=6><td colspan=3><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
			$res = mysql_query("SELECT DISTINCT con.* FROM conversations AS con, comments AS com WHERE con.visible = 'Y' AND con.conid = com.conid AND com.readby_$username = 0 ORDER BY changedate DESC",$db);
			$num_rows = mysql_num_rows($res);
			if ($num_rows == 0) {
				echo "<h1 style=\"padding-top: 7px\">New</h1><p class=\"indent\"><i>No new conversations</i></p>";			
			} else {
				echo "<h1 style=\"padding-top: 7px\">New</h1>";
				echo "<form name=\"markasread\" action=\"index.php\" method=\"POST\"><input type=\"hidden\" name=\"markasread\"><input type=\"hidden\" name=\"readdate\" value=\"" . time() . "\"><table border=0 cellpadding=0 cellspacing=0 class=\"indent medium\">";
				echo "<tr class=\"small\"><td>&nbsp;</td><td>Title (# of comments)</td><td>Most recent post</td><td class=\"small\">Mark read</td></tr>";
				echo "<tr bgcolor=\"#6699CC\"><td colspan=4><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
			}
		} else {
			$unread = 2;
			$trspacer = "<tr height=6><td colspan=2><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
			$res = mysql_query("SELECT DISTINCT con.* FROM conversations AS con, comments AS com WHERE con.visible = 'Y' AND con.conid = com.conid AND com.readby_$username = 1 $norepeats ORDER BY changedate DESC $showall",$db);
			$num_rows = 1;
			echo "<h1 style=\"padding-top: 7px\">Unchanged</h1>";
			echo "<table border=0 cellpadding=0 cellspacing=0 class=\"indent medium\">";
			echo "<tr class=\"small\"><td>Title (# of comments)</td><td>Most recent post</td></tr>";
			echo "<tr bgcolor=\"#6699CC\"><td colspan=2><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
		}
		$rc = true;
		while($convs = mysql_fetch_array($res)) {
			$convdate = $convs["changedate"];
			$convid = $convs["conid"];
			$norepeats .= " AND con.conid != '$convid'";
			$contitle = $convs["contitle"];
			$conauth = $convs["authorid"];
			$convis = $convs["visible"];
			$numcomm = $convs["numcomm"];
			if ($conauth == $userid && ($convis == "N" || $numcomm == 0) && $showarchives == false) {$contitle .= " (<a href=\"newconv.php?id=$convid\">edit</a>)";}
			$lastpostuserid = $convs["lastpostuserid"];
			$lastpostusername = $convs["lastpostusername"];
			$convdate = format_interval(time() - strtotime($convdate));
			$convdate = str_replace(array(" months", " month", " weeks", " week", " days", " day", " hrs", " hr", " min", " sec"),array("mo", "mo", "w", "w", "d", "d", "h", "h", "m", "s"),$convdate);
			if ($rc == true) {$rowcolor = ""; $rc = false;} else {$rowcolor = " bgcolor=\"#F6F6F6\""; $rc = true;}
			if ($unread == 1) {
				echo "<tr$rowcolor><td><img src=\"gfx/new.gif\" border=0 width=31 height=12 hspace=8></td><td class=\"rowpad\"><a href=\"conversations.php?id=$convid\">$contitle</a> ($numcomm)</td><td nowrap class=\"small rowpad sidepad\">$convdate ago by <a href=\"?user=$lastpostuserid\">$lastpostusername</a></td>";
//				echo "<td align=\"center\"><input type=\"checkbox\" onclick=\"document.forms.markasread.markasread.value='$convid';document.forms.markasread.submit();\" title=\"Check this box to mark conversation as read\"></td></tr>";
				echo "<td align=\"center\"><input type=\"checkbox\" name=\"m[]\" value=\"$convid\"></td></tr>";
			} else {
				echo "<tr$rowcolor><td class=\"rowpad sidepad\" ><a href=\"conversations.php?id=$convid\">$contitle</a> ($numcomm)</td><td nowrap class=\"small rowpad sidepad\">$convdate ago by <a href=\"?user=$lastpostuserid\">$lastpostusername</a></td></tr>";
			}
		}
		if ($num_rows > 0) {
			if ($unread == 1) echo "<tr><td colspan=4 align=\"right\"><input type=\"submit\" value=\"Mark as read\"></tr>";
			echo "</table>";
			if ($unread == 1) echo "</form>";
		}
	}
		if (strlen($showall)) {
			echo "<br><p>Show <a href=\"index.php?showall=true\">all</a> old conversations.</p>";
		} else {
			echo "<br><p>Show <a href=\"index.php\">only the most recent 25</a></b> unchanged conversations.</p>";
		}
	echo "<p style=\"padding-bottom: 100px\">&nbsp;</p>";
?>
</td></tr></table>
</body>
</html>
<?php
}
?>