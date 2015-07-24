<?php
require_once ('head.php');
if ($username) {
	if (isset($_GET["deleteconversation"])) {
		$deleteme = $_GET['deleteconversation'];
		if ($userid != 1) $deleteauth = "AND `authorid` = '$userid'";
		mysql_query("DELETE FROM `conversations` WHERE `conid` = '$deleteme' $deleteauth AND `numcomm` < 2 LIMIT 1;") or die("Could not delete conversation.");
		mysql_query("DELETE FROM `comments` WHERE `conid` = '$deleteme' $deleteauth LIMIT 1;") or die("Could not delete comments.");
		echo "<br><p>The conversation has been deleted.</p><form name=\"conadded\" action=\"index.php\" method=\"POST\"><input class=\"sidepad\" type=\"submit\" value=\"Return to the main page\"></form>";
	} else {
		if (isset($_GET["action"])) {
			$newconv = trim(addslashes($_POST['convtitle']));
			$newcomm = trim($_POST['comment']);
			if ($newconv == "") echo "<p>You need to enter a title.</p>";
			if ($newcomm == "") echo "<p>You need to enter the first comment.</p>";
			if ($newconv != "" && $newcomm != "") {
				$newtopic = $_POST['topic'];
				if (isset($_POST['privatewith'])) {$privatewith = $_POST['privatewith']; $visible = 'N';} else {$privatewith = 0; $visible = 'Y';}
				$replacefrom = array("\\'","\\'",'\\"','\\"');
				$replaceto = array("\'","\'",'\"','\"');
				$newcomm = str_replace($replacefrom, $replaceto, $newcomm);
				$newcomm = addslashes($newcomm);
				if ($userid == 1) {
					$newcomm_author= explode (":", $_POST['postingas']);
					$newcomm_authorid = $newcomm_author[0];
					$newcomm_authorname = $newcomm_author[1];
					if (isset($_POST['postingat'])) {
						$newcomm_posttime = ltrim(rtrim($_POST['postingat']));
						$replacefrom = array(" - ","am.","pm.");
						$replaceto = array(" ","am","pm");
						$newcomm_posttime = strtotime(str_replace($replacefrom, $replaceto, $newcomm_posttime));
						if (($timestamp = $newcomm_posttime) === -1 || $_POST['postingat'] == '') {
							$newcomm_posttime = "NOW()";
						} else {
							$newcomm_posttime = $newcomm_posttime - ((0 + $tz) * 3600);
							$newcomm_posttime = "'" . toDBtime($newcomm_posttime) . "'";
							$createtimeadminedit = ", `createdate` = $newcomm_posttime";
						}
					} else {
						$newcomm_posttime = "NOW()";
					}
				} else {
					$newcomm_authorid = $userid;
					$newcomm_authorname = $username;
					$newcomm_posttime = "NOW()";
				}
				$res= mysql_query("INSERT INTO `conversations` (`conid`, `topicid`, `contitle`, `authorid`, `createdate`, `changedate`, `numcomm`, `lastpostuserid`, `lastpostusername`, `visible`, `privatewith`) VALUES ('', '$newtopic', '$newconv', '$newcomm_authorid', $newcomm_posttime , $newcomm_posttime , '1', '$newcomm_authorid', '$newcomm_authorname', '$visible', '$privatewith');") or die("Could not add new conversation.");
				$res= mysql_query("SELECT `conid` FROM `conversations` WHERE `contitle` = '$newconv' ORDER BY `createdate` DESC LIMIT 1;");
				if (mysql_num_rows($res)==1) {
					$conv_obj= mysql_fetch_object($res);
					$conv_id= $conv_obj->conid;
					$res= mysql_query("INSERT INTO `comments` (`comid`, `conid`, `authorid`, `comment`, `createdate`, `changedate`) VALUES ('', '$conv_id', '$newcomm_authorid', '$newcomm', $newcomm_posttime , $newcomm_posttime);") or die("Could not add first comment: " . mysql_error());
					$postsuccessful = true;
				} else {
					echo "<p>Could not add new conversation</p>";
				}
				if (isset($_POST['dm'])) {
					$deleteme = $_POST['dm'];
					mysql_query("DELETE FROM `conversations` WHERE `conid` = '$deleteme' AND `authorid` = '$userid' AND `numcomm` < 2 LIMIT 1;") or die("Could not delete conversation.");
					mysql_query("DELETE FROM `comments` WHERE `conid` = '$deleteme' AND `authorid` = '$userid' LIMIT 1;") or die("Could not delete comments.");
				}
			}
			if ($postsuccessful == true) {
				if ($privatewith == 0) {
					echo "<br>Your conversation has been added!</p><form name=\"conadded\" action=\"conversations.php?id=$conv_id\" method=\"POST\"><table border=0 cellpadding=0 cellspacing=0><tr><td><input type=\"submit\" value=\"View conversation\"></td><td class=\"sidepad\"><input type=\"button\" onclick=\"document.location.href='index.php';\" value=\"Return to main page\"></td></tr></table></form>";
				} else {
					header("Location: " . HOST_NAME . "/index.php?private=true");
				}
				exit;
			}
		} else {
			echo "<form name=\"newconv\" method=\"post\" action=\"newconv.php?action=newconv\">";
			if (isset($_GET['id'])) {
				$convid = $_GET['id'];
				echo "<h1>Edit Conversation</h1><p>You can edit or delete a conversation or dialogue you have created, provided that it is under seven days old and contains only your initial comment.</p>";
				$res= mysql_query("SELECT * FROM `conversations` WHERE `conid` = '$convid' AND `authorid` = '$userid' LIMIT 1;");
				if (mysql_num_rows($res)==1) {
					$conv_obj= mysql_fetch_object($res);
					$conv_id = $conv_obj->conid;
					$contitle = " value=\"" . $conv_obj->contitle . "\"";
					$privatewith = $conv_obj->privatewith;
					$topic = $conv_obj->topic;
				}
				$res= mysql_query("SELECT `comment` FROM `comments` WHERE `conid` = '$convid' and `authorid` = '$userid' LIMIT 1;");
				if (mysql_num_rows($res)==1) {
					$conv_obj= mysql_fetch_object($res);
					$editcomment = $conv_obj->comment;
				}
				echo "<input type=\"hidden\" name=\"dm\" value=$conv_id><table border=0 cellpadding=2 cellspacing=0><tr><td class=\"small blue\" colspan=2>";
			}
			if (isset($_GET['private']) || $privatewith != 0) {
				if (isset($conv_id) == false) echo "<h1>New Dialogue</h1><p>A dialogue is a private conversation between two users.</p><table border=0 cellpadding=2 cellspacing=0><tr><td class=\"small blue\" colspan=2>";
				echo "With:<br><input type=\"hidden\" name=\"topic\" value=0><select name=\"privatewith\">";
				$res = mysql_query("SELECT * FROM users ORDER BY username",$db);
				while($users = mysql_fetch_array($res)) {
					$u_userid = $users["userid"];
					$u_username = $users["username"];
					if ($privatewith == $u_userid) {$optselected = " selected";} else {$optselected = "";}
					if ($u_userid != $userid) echo "<option value=\"$u_userid:$u_username\"$optselected>$u_username</option>";
				}
			} else {
				if (isset($conv_id) == false) echo "<h1>New Conversation</h1><table border=0 cellpadding=2 cellspacing=0>";
			}
			echo "<tr><td class=\"small blue\" colspan=2>Title:<br><input type=\"text\" name=\"convtitle\" size=60 maxlength=255 class=\"h2\"$contitle></td></tr>";
			echo "<tr><td class=\"small blue\" colspan=2>First comment:<br><textarea name=\"comment\" cols=65 rows=11 class=\"medium comment\">$editcomment</textarea></td></tr>";
			if ($userid == 1) {
				echo "<tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0 class=\"medium\"><tr><td>Posting as:&nbsp;</td><td><select name=\"postingas\" class=\"small\">";
				$res = mysql_query("SELECT * FROM users ORDER BY username",$db);
				while($users = mysql_fetch_array($res)) {
					$u_userid = $users["userid"];
					$u_username = $users["username"];
					if ($u_userid == $userid) {$optselected = " selected";} else {$optselected = "";}
					echo "<option value=\"$u_userid:$u_username\"$optselected>$u_username</option>";
				}
				echo "</select></td><td>&nbsp;at time:&nbsp;</td><td><input type=\"text\" name=\"postingat\" width=13 class=\"small\"></td></tr></table></td></tr>";
			}
			if (isset($conv_id)) $convbutt = "Save Changes";
			if (isset($_GET['private'])) {$convbutt = "Add Dialogue";} else {$convbutt = "Add Conversation";}
			echo "<tr><td><input type=\"submit\" value=\"$convbutt\"></td><td align=\"right\"><input type=\"button\" value=\"Cancel\" onclick=\"if (confirm('Cancel and lose all changes?')) document.location.href='index.php';\" class=\"small gray\">&nbsp; </td></tr>";
			echo "</table></form>";
			if (isset($conv_id)) echo "<p><br>&nbsp;<br>&nbsp;<br><a href=\"javascript://\" onclick=\"if (confirm('Delete this conversation and its initial comment permanently?')) document.location.href='newconv.php?deleteconversation=$conv_id';\" class=\"small\">Delete this conversation</a></p>";
		}
	}
?>
</td></tr></table>
</body>
</html>
<?php
}
?>