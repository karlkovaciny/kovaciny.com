<?php 
if (isset($_GET['action'])) {
	if ($_GET['action'] == "new" || $_GET['action'] == "update") {
		if (isset($_POST['inreplyto'])) { //admin is setting date
			$newinreplyto = $_POST['inreplyto'];
			} else if (isset($_GET['irtid'])){ //for edits by regular users
			$newinreplyto = $_GET['irtid'];
			} else {$newinreplyto = 0;}
		$newcomm = ltrim(rtrim($_POST['comment']));
		if (!isset($_POST['comment']) ) { 
			die ("error: tried to post blank post"); 
		}
		
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
					$newcomm_posttime = "'" . date('Y-m-d H:i:s', $newcomm_posttime) . "'";
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
	}
	if ($_GET['action'] == "new") {
		//delete duplicate posts, apparently
		$res= mysql_query("DELETE FROM `comments` WHERE `authorid` = '$newcomm_authorid' AND `conid` = '$conv_id' AND `comment` = '$newcomm'") or die("Could not update database: " . mysql_error()); 
		
		$res= mysql_query("INSERT INTO `comments` (`comid`, `inreplyto`, `conid`, `authorid`, `comment`, `createdate`, `changedate`) VALUES ('', '$newinreplyto', '$conv_id', '$newcomm_authorid', '$newcomm', $newcomm_posttime , $newcomm_posttime);") or die("Could not update comment database.");
		if ($newcomm_posttime == "NOW()") {$res= mysql_query("UPDATE `conversations` SET `changedate` = NOW( ), `lastpostuserid` = '$newcomm_authorid', `lastpostusername` = '$newcomm_authorname' WHERE `authorid` = '$userid' AND `conid` = '$conv_id' LIMIT 1") or die("Could not update conversation database.");}
		$updatecommentcount = true;
	} elseif ($_GET['action'] == "delete") {
		if (isset($_GET['comid'])) {
			$editcomid = $_GET['comid'];
			if ($userid != 1) $reqauthor = " AND `authorid` = '$userid'";
			$res = mysql_query("SELECT comid FROM `comments` WHERE `conid` = '$conv_id' AND `comid` = '$editcomid'$reqauthor",$db);
			if (mysql_num_rows($res)==1) {
				mysql_query("UPDATE `comments` SET `visible` = 'N' WHERE `conid` = '$conv_id' AND `comid` = '$editcomid';") or die("Could not delete comment");
			}
		}
	} elseif ($_GET['action'] == "edit") {
		if (isset($_GET['comid'])) {
			$editcomid = $_GET['comid'];
			if ($userid != 1) $reqauthor = " AND `authorid` = '$userid'";
			$res = mysql_query("SELECT * FROM `comments` WHERE `conid` = '$conv_id' AND `comid` = '$editcomid'$reqauthor",$db);
			if (mysql_num_rows($res)==1) {
				$hideallexcept = $editcomid;
			}
		}
	} elseif ($_GET['action'] == "reply") {
		if (isset($_GET['comid'])) {
			$replytoid = $_GET['comid'];
			$res = mysql_query("SELECT * FROM `comments` WHERE `conid` = '$conv_id' AND `comid` = '$replytoid'",$db);
			if (mysql_num_rows($res)==1) {
				$hideallexcept = $replytoid;
			}
		}
	} elseif ($_GET['action'] == "update") {
		if (isset($_GET['comid'])) {
			$editcomid = $_GET['comid'];
			if ($userid != 1) $reqauthor = " AND `authorid` = '$userid'";
			$res = mysql_query("SELECT * FROM `comments` WHERE `conid` = '$conv_id' AND `comid` = '$editcomid'$reqauthor",$db);
			if (mysql_num_rows($res)==1) {
				mysql_query("UPDATE `comments` SET `comment` = '$newcomm'$createtimeadminedit, `changedate` = $newcomm_posttime, `authorid` = '$newcomm_authorid', `inreplyto` = '$newinreplyto' WHERE `conid` = '$conv_id' AND `comid` = '$editcomid';") or die("Could not update comment");
				$updatecommentcount = true;
			}
		}
	}
	if ($updatecommentcount == true) {
		$res = mysql_query("SELECT count(*) AS CommCount FROM `comments` WHERE `conid` = '$conv_id' AND `visible` = 'Y'",$db);
		if (mysql_num_rows($res)==1) {
			$conv_obj= mysql_fetch_object($res);
			$comcount= $conv_obj->CommCount;
			mysql_query("UPDATE `conversations` SET `numcomm` = '$comcount' WHERE `conid` = '$conv_id';") or die("Could not update comment count");
		}
		$res = mysql_query("SELECT `createdate` FROM `comments` WHERE `conid` = '$conv_id' AND `visible` = 'Y' ORDER BY `createdate` DESC LIMIT 1;",$db);
		if (mysql_num_rows($res)==1) {
			$conv_obj= mysql_fetch_object($res);
			$finalpost= $conv_obj->createdate;
			$res = mysql_query("SELECT c.createdate, c.authorid, u.username FROM comments AS c, users AS u WHERE u.userid = c.authorid AND c.conid = '$conv_id' AND c.visible = 'Y' AND c.createdate = '$finalpost';",$db);
			if (mysql_num_rows($res)==1) {
				$conv_obj= mysql_fetch_object($res);
				$lastposttime = $conv_obj->createdate;
				$lastpostid= $conv_obj->authorid;
				$lastpostname= $conv_obj->username;
				mysql_query("UPDATE `conversations` SET `changedate` = '$lastposttime', `lastpostuserid` = '$lastpostid', `lastpostusername` = '$lastpostname' WHERE `conid` = '$conv_id';") or die("Could not update last post info.");
			}
		}
	}
}
?>
