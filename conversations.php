<?php
require_once ('head.php');
if ($username) {
	$tdspacer = "<td width=12>&nbsp;</td>";
	$hideallexcept = 0; //not in edit or reply mode
						//convedit.php sets this to id of the comment to show
	if (isset($_GET['id'])) {
		$conv_id = $_GET['id'];
		if (is_numeric($conv_id)) {
			require ('convedit.php');
			// Get Conversation details
            $res = mysql_query("SELECT c.*, u.userid, u.username FROM conversations AS c, users AS u WHERE u.userid = c.authorid AND c.conid = $conv_id AND (c.visible = 'Y' OR c.authorid = '$userid' OR c.privatewith = '$userid') ORDER BY c.createdate DESC",$db);
            $res2 = mysql_query("SELECT MAX(changedate) - INTERVAL 1 SECOND AS lastread FROM comments WHERE readby_$username = 0 AND conid = $conv_id");
            $convlastread = mysql_fetch_object($res2)->lastread;
            if (mysql_num_rows($res)==1) {
				$conv_obj= mysql_fetch_object($res);
				$contitle= $conv_obj->contitle;
				$comcount= $conv_obj->numcomm;
				$concreated = $conv_obj->createdate;
				$conchanged = $conv_obj->changedate;
				$concreated = strtotime($concreated);
				$lastposttime = time() - strtotime($conchanged);
				$lastposttime = format_interval(time() - strtotime($conchanged));
				$concreated = date('M d, Y, g:i a', $concreated - ((0 + $tz) * 3600));
				$authorname= $conv_obj->username;
				$authorid= $conv_obj->userid;
				$lastpostname= $conv_obj->lastpostusername;
				$lastpostid= $conv_obj->lastpostuserid;
				$privatewith= $conv_obj->privatewith;
				$spacer = "<td width=2><img src=\"gfx/-.gif\" border=0 width=1 height=1></td>";
                $markasreadbutton = 
                    "<form name=\"markread\" class=\"markread\" action=\"\" method=\"POST\">"
                    . "<input type=\"hidden\" name=\"markasread\" value=\"1\">" 
                    . "<input type=\"hidden\" name=\"conchangedate\" value=\"$convlastread\">" 
                    . "<input type=\"hidden\" name=\"username\" value=\"$username\">" 
                    . "<input type=\"hidden\" name=\"convIds\" value=\"$conv_id\">"
                    . "<input type=\"hidden\" name=\"readdate\" value=\"" . date(MYSQL_DATETIME_FORMAT) . "\">"
                    . "<input type=\"submit\" class=\"markAsReadSubmit\" value=\"Mark as read\" title=\"Mark all comments in this conversation as read.\">"
                    . "</form>";
				if ($hideallexcept == 0) {	//not editing or replying
					echo "<table border=0 cellpadding=0 cellspacing=0 class=\"small\">"
                    . "<tr><td><h1>$contitle</h1></td>"
                    . "<td class=\"sidepad\">$markasreadbutton</td>";
					
					if ($userid == $authorid && $comcount == 1) {
						echo "<td class=\"sidepad\"><form class=\"markread\"><input type=\"button\" onclick=\"if(confirm('Are you sure you want to delete this conversation?')) {document.location.href='newconv.php?deleteconversation=$conv_id';}\" value=\"Delete conversation\" style=\"color: red\"></form></td>";
					} else {
						echo "<td><div id=\"ncb1\"><input type=\"button\" onclick=\"hide('ncb1');show('ncb2');habtop();\" value=\"Show new comments only\"></div><div id=\"ncb2\" class=\"hide\"><input type=\"button\" onclick=\"hide('ncb2');show('ncb1');commentToggle(1);\" value=\"Show all comments\"></div></td>";
					}
					echo "</tr>";
					if ($comcount == 1) {
						echo "<tr><td colspan=3 class=\"small\">1 comment (by <a href=\"?user=$lastpostid\">$lastpostname</a>, $lastposttime ago)</td></tr>";
					} else {
						echo "<tr><td colspan=3 class=\"small\">$comcount comments (most recent by <a href=\"?user=$lastpostid\">$lastpostname</a>, $lastposttime ago)</td></tr>";
					}
					echo "</table><br>";
					} else {
						echo "<h1>$contitle</h1>";
					}
				$comcount += 1;
				echo "<div class=\"allCommentsContainer\">";
			// Get comments
				$res = mysql_query("SELECT c.*, u.userid, u.username FROM comments AS c, users AS u WHERE u.userid = c.authorid AND c.conid = $conv_id AND c.visible = 'Y' ORDER BY c.inreplyto, c.createdate", $db);
                $allcomments = "";
				$unreadcomments = "";
				$commentsretrieved = 0;	
				$cb_id = array();
				$topnewid = 0;
				$topnewparentid = 0;
				while($comments = mysql_fetch_array($res)) {
					$commentid = $comments["comid"];
					$comment_text_id = "c_" . $commentid . "_text"; //for adding a div later
					if ($hideallexcept == 0 || $commentid == $hideallexcept) {	//We're not editing or this is the one we're editing.
						//So go ahead and load them all.
						$comment = $comments["comment"];
						$commentdate = $comments["createdate"];
						$commentage = time() - strtotime($commentdate);
						if ($commentage > 604800) {$commentage = "1w+";} else {$commentage = "-1w";}
						$changedate = $comments["changedate"];
						$inreplyto = $comments["inreplyto"];
						$authorname = $comments["username"];
						$authorid = $comments["userid"];
						$markedasred = $comments["readby_$username"];
						$commentintv = format_interval(time() - strtotime($commentdate)); 
                        $allcomments .= "$commentid:";
						$commentanchor = getCommentAnchor($commentid);
						if ($userid == 1) {$commentinfo = "<td class=\"small\">$commentid ($inreplyto) &nbsp;</td>";} else $commentinfo = "";
						if ($markedasred == "0") {
							$isnew = "<td width=35><div style=\"padding-top:2px\"><img src=\"gfx/new.gif\" border=0 width=31 height=12 hspace=4></div></td>$tdspacer";
						} else {
							$unreadcomments .= "$commentid:"; //variable name is a misnomer, this is a list of *already read* comments
							$isnew = "";
						}
						$ccc = "<tr bgcolor=\"#DDDDDD\"><td class=\"sidepad\" id=\"cc_$commentid\">"
							. "<a name=\"$commentanchor\"></a>";
						
						//set up the action bar links							
						$ccc .= "<div style=\"width:inherit\">" 
							. "<table border=0 cellpadding=0 cellspacing=0 class=\"small\" style=\"table-layout:fixed; width:100%\">"
							. "<tr valign=\"middle\">";
						$ccc .= "$commentinfo<td width=" . 15*strlen($authorname) . " class=\"large b\"><a href=\"?user=$authorid\">$authorname</a></td>$tdspacer";
						$ccc .= "$isnew<td width=120>$commentintv ago</td>$tdspacer";
						$hilite = "";
						if ($hideallexcept == 0) {	//not edit or reply mode
							//Add show comment/hide comment links, with only the relevant one being visible
							$ccc .= "<td><div id=\"c_h_$commentid\" class=\"hide\"><a href=\"javascript:hide('c_h_$commentid');show('c_s_$commentid');show('c_$commentid');\">Show comment</a></div>";
							$ccc .= "<div id=\"c_s_$commentid\"><a href=\"javascript:show('c_h_$commentid');hide('c_s_$commentid');hide('c_$commentid');\">Hide comment</a>";
							if ($userid == 1 || $userid == $authorid) {
								$ccc .= " &nbsp; &nbsp;<a href=\"conversations.php?id=$conv_id&comid=$commentid&action=edit\">Edit</a>";
								$ccc .= " &nbsp; &nbsp;<a href=\"#\" class=\"deleteCommentLink\" data-convid=\"$conv_id\" data-commentid=\"$commentid\">Delete</a>";
							}
							if ($userid == 1) {
								$ccc .= " &nbsp; &nbsp;($commentage) <span style=\"cursor: pointer; text-decoration: underline\" onclick=\"makeRequest('c.xml');\">Load comment</span>";
							}
							if ($userid == $authorid) {
								$hilite = " hilite";
								if ($privatewith == 0) $ccc .= " &nbsp; &nbsp;<a href=\"conversations.php?id=$conv_id&comid=$commentid&action=reply\">Post follow-up</a>";
							} else {
								if ($privatewith == 0) $ccc .= " &nbsp; &nbsp;<a href=\"conversations.php?id=$conv_id&comid=$commentid&action=reply\">Reply to this</a>";
								$ccc .= " &nbsp; &nbsp;<a href=\"javascript://\" onmousedown=\"quoteme('$authorname', '$comment_text_id');\">Quote selected</a>";
							}	
							$ccc .= "</div></td>";
						} elseif (isset($replytoid)) {
							$ccc .= "<td><a href=\"javascript://\" onmousedown=\"quoteme('$authorname', '$comment_text_id');\">Quote selected</a></td>";
						} else { //then we're editing one post and don't want the action bar to stretch out
							$ccc .= "<td>";
						}
						$ccc .= "</tr></table></td></tr>"; //end of the action bar
						
						//Format quotes in comment
						$q1 = "<table border=0 cellpadding=4 cellspacing=0 class=\"border\"><tr valign=\"top\"><td class=\"green small\" bgcolor=\"#F6F6F6\">";
						$q2 = "</td><td class=\"quote\" bgcolor=\"#FFFFFF\">";
						$c13 = chr(13);
						$c10 = chr(10);
						$replacefrom = array(chr(10),"[quote]","[/quote]","</table>$c13<br>", "<a href=", "</a>");
						$replaceto = array("<br>","$q1 quote$q2", "</td></tr></table>","</table>", "<u><a href=", "</a></u>");
						$htmlcomment = str_replace($replacefrom, $replaceto, $comment);
						$replacefrom = array("[quote=Anna]", "[quote=Jon]", "[quote=Karl]", "[quote=Larry]", "[quote=Monica]", "[quote=Nate]", "[quote=Rachel]", "[quote=Rae]", "[quote=Roger]", "[quote=Ruth]", "[quote=John]", "[quote=john]");
						$replaceto = array("$q1 Anna$q2", "$q1 Jon$q2", "$q1 Karl$q2", "$q1 Larry$q2", "$q1 Monica$q2", "$q1 Nate$q2", "$q1 Rachel$q2", "$q1 Rae$q2", "$q1 Roger$q2", "$q1 Ruth$q2", "$q1 John$q2", "$q1 John$q2");
						$htmlcomment = str_replace($replacefrom, $replaceto, $htmlcomment);
						
						//Keep URLs that don't start with "http" from turning into relative links (does not alter the original comment)
						$htmlcomment = turnRelativeLinksAbsolute($htmlcomment);
						
						//add a span to allow us to access the comment text
						$htmlcomment = "\n\n<span id=\"$comment_text_id\" class=\"commentContents\">" . $htmlcomment . "</span>\n";
						
						//add in the user's graphic
						if ($authorname == "Jon" || $authorname == "Rae" || $authorname == "Karl" || $authorname == "Monica" || $authorname == "Rachel" || $authorname == "Larry") {
							$htmlcomment = "<img src=\"/gfx/" . strtolower($authorname) . ".jpg\" border=0 width=85 height=85 style=\"float:left; margin-right: 8px; margin-bottom: 8px\">" . $htmlcomment;
						}
						
						//add the comment itself
						$ccc .= "<tr><td class=\"copy sidepad border$hilite\"><div id=\"c_$commentid\">&nbsp;<br>$htmlcomment<br>&nbsp;</div></td></tr>";
						$cb_id[] = $commentid;
						$cb_inReplyTo[] = $inreplyto;
						$cb_isnew[] = $isnew;
						$cb_commentHTML[] = $ccc;
						$commentsretrieved += 1;
					}
				}
				//thread and display comments
				require ('inc_commthreader.php');
				echo "</div>";	//allCommentsContainer
				
				if ($hideallexcept == 0) {
					echo "<div class=\"hrnoshade\"></div>";
                    echo $markasreadbutton;
					echo "<div class=\"hrnoshade\"></div><br>";
				}
				echo "<form name=\"commentform\" id=\"commentform\" method=\"post\" action=\"conversations.php";
				
				//configure comment editor
				$hiddenitems = "";	//used for admin
				$editcomment = "";	//used in edit mode
				if ($hideallexcept == 0) {
					$modal_user_prompt = "Post a comment:";
					$postbutt = "add comment";
					echo "?id=$conv_id&action=new";
					if ($userid == 1) {$hiddenitems ="In reply to: <input type=\"text\" name=\"inreplyto\" class=\"small\" size=3 value=0 onfocus=\"this.select();\"> &nbsp;";}
				} else {	
					if (isset($replytoid)) {	
						//reply mode
						if ($authorid == $userid) {
							$modal_user_prompt = "Post a follow-up comment:";
							$postbutt = "add comment";
						} else {
							$modal_user_prompt = "Reply to $authorname's comment:";
							$postbutt = "post reply";
						}
						$comment = "[quote=$authorname]" . addslashes($comment) . "[/quote]";
						if ($userid == 1) {
							$hiddenitems = "<input type=\"text\" name=\"inreplyto\" value=\"$replytoid\" class=\"small\" size=3 onfocus=\"this.select();\">";
						} else {
							$hiddenitems = "<input type=\"hidden\" name=\"inreplyto\" value=\"$replytoid\">";
						}
						echo "?id=$conv_id&comid=$hideallexcept&action=new";
					} else {	
						//edit mode
						$modal_user_prompt = "Edit this comment:";
						$postbutt = "save changes";
						$posttime = strtotime($commentdate) + ($tz * 3600);
						$posttime = date('M d, Y g:i a', $posttime);
						$posttime = " value=\"$posttime\"";
                        echo "?id=$conv_id&comid=$hideallexcept&irtid=$inreplyto&action=update"; // the action for the edit form submit
						$editcomment = $comment;
						$replacefrom = array("\'",'\"');
						$replaceto = array("'",'"');
						$editcomment = str_replace($replacefrom, $replaceto, $editcomment);
						if ($userid == 1) {$hiddenitems ="In reply to: <input type=\"text\" name=\"inreplyto\" value=\"$inreplyto\" class=\"small\" size=3 onfocus=\"this.select();\"> &nbsp;";}
					}
				}
				echo "\">";
				
				//display comment editor
				echo "<table border=0 cellpadding=2 cellspacing=0 class=\"medium\">";
				if ($userid == 1) {
					echo "<tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0 class=\"medium\"><tr><td><h2>$modal_user_prompt</h2></td>$tdspacer<td>as&nbsp;</td><td><select name=\"postingas\" class=\"small\">";
					$res = mysql_query("SELECT * FROM users ORDER BY username", $db);
					while($users = mysql_fetch_array($res)) {
						$u_userid = $users["userid"];
						$u_username = $users["username"];
						if (isset($replytoid)) {
							if ($u_username == "Jon") {$optselected = " selected";} else {$optselected = "";}
						} else {
							if ($authorid == $u_userid) {$optselected = " selected";} else {$optselected = "";}
						}
						echo "<option value=\"$u_userid:$u_username\"$optselected>$u_username</option>";
					}
					echo "</select></td><td>&nbsp;at time&nbsp;</td>";
                    //$posttime is never set, but Jon hasn't complained
                    echo "<td><input type=\"text\" name=\"postingat\" width=13 class=\"small\"$posttime></td>";
					echo "<td class=\"small\">&nbsp;(" .
						"<a href=\"javascript://\" onclick=\"document.forms.commentform.postingat.value='';\">now</a>" . //defaults to now()
						")</td></tr></table></td></tr>";
				} else { //not logged in as admin
					echo "<tr><td colspan=2><h2>$modal_user_prompt</h2></td></tr>";
				}
				$allcomments = rtrim($allcomments, ":");
				$unreadcomments = rtrim($unreadcomments, ":");
				echo "<tr><td colspan=2><input type=\"hidden\" name=\"ac\" value=\"$allcomments\"><input type=\"hidden\" name=\"ntc\" value=\"$unreadcomments\"><textarea name=\"comment\" cols=65 rows=9 class=\"medium comment\" tabindex=\"14\">$editcomment</textarea></td></tr>";
				echo "<tr><td class=\"small blue\">$hiddenitems<input type=\"submit\" value=\"$postbutt\" tabindex=\"17\"></td><td align=\"right\"></td></tr>";
				echo "</table></form>";
			}
		}
	}
?>
</td></tr></table>

<?php	//scripts to run after page loads
	if ( DEBUG ) {
		$rand = floor(rand() * 100);
		$jquery_source = "scripts/conv_jquery.js?dev=$rand";
	} else {
		$jquery_source = "scripts/conv_jquery.js?" . RELEASE_VERSION;
	}
	echo "<script src=\"$jquery_source\" type=\"text/javascript\"></script>";
	
	if ( wantNewPosts($topnewid) ) {	
		$jumpto = getCommentAnchor($topnewid);
		echo "<script type=\"text/javascript\">
			window.onload=function(){ 
			console.log('We want to only show new posts, starting with $topnewid');
			autoHideOldComments( function(){";
		if (!empty($topnewparentid)) {
			echo "var com = new kcom.Comment($topnewparentid);
			console.log('we are trying to display parent post ' + com.getId());
			com.show();";
		}
		echo "jumpToAnchor('$jumpto');
		} );
		}";
		echo "</script>";
	} 
}
?>
</body>
</html>
