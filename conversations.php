<?php
require_once ('head.php');
if ($username) {
	$tdspacer = "<td width=12>&nbsp;</td>";
	$hideallexcept = 0;
	if (isset($_GET['id'])) {
		$conv_id = $_GET['id'];
		if (is_numeric($conv_id)) {
			require ('convedit.php');
			// Get Conversation details
			$res = mysql_query("SELECT c.*, u.userid, u.username FROM conversations AS c, users AS u WHERE u.userid = c.authorid AND c.conid = $conv_id AND (c.visible = 'Y' OR c.authorid = '$userid' OR c.privatewith = '$userid') ORDER BY c.createdate DESC",$db);
			if (mysql_num_rows($res)==1) {
				$conv_obj= mysql_fetch_object($res);
				$contitle= $conv_obj->contitle;
				$comcount= $conv_obj->numcomm;
				$concreated = $conv_obj->createdate;
				$conchanged = $conv_obj->changedate;
				$concreated = strtotime($concreated);
				$lastposttime = time() - strtotime($conchanged);
				//if ($lastposttime <= 604800 && $comcount > 0 && $hideallexcept == 0) 
				$lastposttime = format_interval(time() - strtotime($conchanged));
				$concreated = date('M d, Y, g:i a', $concreated - ((0 + $tz) * 3600));
				$authorname= $conv_obj->username;
				$authorid= $conv_obj->userid;
				$lastpostname= $conv_obj->lastpostusername;
				$lastpostid= $conv_obj->lastpostuserid;
				$privatewith= $conv_obj->privatewith;
				$spacer = "<td width=2><img src=\"gfx/-.gif\" border=0 width=1 height=1></td>";
				if ($hideallexcept == 0) {
					echo "<form name=\"markasread\" action=\"index.php\" method=\"POST\"><table border=0 cellpadding=0 cellspacing=0 class=\"small\"><tr><td><h1>$contitle</h1></td>";
					echo "<td class=\"sidepad\"><input type=\"hidden\" name=\"markasread\" value=\"$conv_id\"><input type=\"hidden\" name=\"readdate\" value=\"" . time() . "\"><input type=\"submit\" value=\"Mark as read\" title=\"Mark all comments in this conversation as read.\"></td>";
					if ($userid == $authorid && $comcount == 1) {
						echo "<td class=\"sidepad\"><input type=\"button\" onclick=\"if(confirm('Are you sure you want to delete this conversation?')) {document.location.href='newconv.php?deleteconversation=$conv_id';}\" value=\"Delete conversation\" style=\"color: red\"></td>";
					} else {
						echo "<td><div id=\"ncb1\"><input type=\"button\" onclick=\"hide('ncb1');show('ncb2');habtop();\" value=\"Show new comments only\"></div><div id=\"ncb2\" class=\"hide\"><input type=\"button\" onclick=\"hide('ncb2');show('ncb1');commenttoggle(1);\" value=\"Show all comments\"></div></td>";
					}
					echo "</tr>";
					if ($comcount == 1) {
						echo "<tr><td colspan=3 class=\"small\">1 comment (by <a href=\"?user=$lastpostid\">$lastpostname</a>, $lastposttime ago)</td></tr>";
					} else {
						echo "<tr><td colspan=3 class=\"small\">$comcount comments (most recent by <a href=\"?user=$lastpostid\">$lastpostname</a>, $lastposttime ago)</td></tr>";
					}
					echo "</table></form><br>";
					} else {
						echo "<h1>$contitle</h1>";
					}
				$comcount += 1;
			// Get comments
				$res = mysql_query("SELECT c.*, u.userid, u.username FROM comments AS c, users AS u WHERE u.userid = c.authorid AND c.conid = $conv_id AND c.visible = 'Y' ORDER BY c.createdate",$db);
				$allcomments = "";
				$unreadcomments = "";
				$cb = 0;
				$cb_id = array(); //initializing so it won't choke implode() if empty
				while($comments = mysql_fetch_array($res)) {
					$commentid = $comments["comid"];
					if ($hideallexcept == 0 || $commentid == $hideallexcept) {
						$comment = $comments["comment"];
						$commentdate = $comments["createdate"];
						$commentage = time() - strtotime($commentdate);
						if ($commentage > 604800) {$commentage = "1w+";} else {$commentage = "-1w";}
						$changedate = $comments["changedate"];
						$inreplyto = $comments["inreplyto"];
						/*if ($commentdate == $changedate) {$changenotice = "";} else {
							$postmodified = strtotime($commentdate) - strtotime($changedate);
							if ($postmodified > 420) {
								$postmodified = format_interval(time() - strtotime($changedate));
								$changenotice = "<span class=\"small green\">Note: This post was modified $postmodified ago.<br></span>";
							}
						}*/
						$authorname = $comments["username"];
						$authorid = $comments["userid"];
						$markedasred = $comments["readby_$username"];
						$commentintv = format_interval(time() - strtotime($commentdate)); //$commentdate = date('M d, g:i a', $commentdate);
						$allcomments .= "$commentid:";
						if ($userid == 1) {$commentinfo = "<td class=\"small\">$commentid ($inreplyto) &nbsp;</td>";}
						if ($markedasred == "0") {
							$isnew = "<td width=35><div style=\"padding-top:2px\"><img src=\"gfx/new.gif\" border=0 width=31 height=12 hspace=4></div></td>$tdspacer";
						} else {
							$unreadcomments .= "$commentid:";
							$isnew = "";
						}
						//set up the action bar
						$ccc = "<tr bgcolor=\"#DDDDDD\"><td class=\"sidepad\" id=\"cc_$commentid\"><a name=\"comment_$commentid\"></a><div style=\"width:inherit\"><table border=0 cellpadding=0 cellspacing=0 class=\"small\" style=\"table-layout:fixed; width:100%\"><tr valign=\"middle\">";
						$ccc .= "$commentinfo<td width=60 class=\"large b\"><a href=\"?user=$authorid\">$authorname</a></td>$tdspacer";
						$ccc .= "$isnew<td width=120>$commentintv ago</td>$tdspacer";
						if ($hideallexcept == 0) {
							//Add show comment/hide comment links, with only the relevant one being visible
							$ccc .= "<td><div id=\"c_h_$commentid\" class=\"hide\"><a href=\"javascript:hide('c_h_$commentid');show('c_s_$commentid');show('c_$commentid');\">Show comment</a></div>";
							$ccc .= "<div id=\"c_s_$commentid\"><a href=\"javascript:show('c_h_$commentid');hide('c_s_$commentid');hide('c_$commentid');\">Hide comment</a>";

							if ($userid == 1 || $userid == $authorid) {
								$ccc .= " &nbsp; &nbsp;<a href=\"conversations.php?id=$conv_id&comid=$commentid&action=edit\">Edit</a>";
								$ccc .= " &nbsp; &nbsp;<a href=\"javascript:if(confirm('Permanently Delete this Comment?')){document.location.href='conversations.php?id=$conv_id&comid=$commentid&action=delete';}//\">Delete</a>";
							}
							if ($userid == 1) {
								$ccc .= " &nbsp; &nbsp;($commentage) <span style=\"cursor: pointer; text-decoration: underline\" onclick=\"makeRequest('c.xml');\">Load comment</span>";
							}
							if ($userid == $authorid) {
								$hilite = " hilite";
								if ($privatewith == 0) $ccc .= " &nbsp; &nbsp;<a href=\"conversations.php?id=$conv_id&comid=$commentid&action=reply\">Post follow-up</a>";
							} else {
								$hilite = "";
								if ($privatewith == 0) $ccc .= " &nbsp; &nbsp;<a href=\"conversations.php?id=$conv_id&comid=$commentid&action=reply\">Reply to this</a>";
								$ccc .= " &nbsp; &nbsp;<a href=\"javascript://\" onmousedown=\"quoteme('$authorname');\">Quote selected</a>";
							}	
							$ccc .= "</div></td>";
						} elseif (isset($replytoid)) {
							$ccc .= "<td><a href=\"javascript://\" onmousedown=\"quoteme('$authorname');\">Quote selected</a></td>"; // " &nbsp; &nbsp;<a href=\"javascript://\" onclick=\"quoteentire('$authorname');\">Quote entire post</a>"
						}
						$ccc .= "</tr></table></td></tr>"; //end of the action bar
						$q1 = "<table border=0 cellpadding=4 cellspacing=0 class=\"border\"><tr valign=\"top\"><td class=\"green small\" bgcolor=\"#F6F6F6\">";
						$q2 = "</td><td class=\"gray sidepad medium\" bgcolor=\"#FFFFFF\">";
						$c13 = chr(13);
						$c10 = chr(10);
						$replacefrom = array(chr(10),"[quote]","[/quote]","</table>$c13<br>", "<a href=", "</a>");
						$replaceto = array("<br>","$q1 quote$q2", "</td></tr></table>","</table>", "<u><a href=", "</a></u>");
						$htmlcomment = str_replace($replacefrom, $replaceto, $comment);
						$replacefrom = array("[quote=Anna]", "[quote=Jon]", "[quote=Karl]", "[quote=Larry]", "[quote=Monica]", "[quote=Nate]", "[quote=Rachel]", "[quote=Rae]", "[quote=Roger]", "[quote=Ruth]", "[quote=John]", "[quote=john]");
						$replaceto = array("$q1 Anna$q2", "$q1 Jon$q2", "$q1 Karl$q2", "$q1 Larry$q2", "$q1 Monica$q2", "$q1 Nate$q2", "$q1 Rachel$q2", "$q1 Rae$q2", "$q1 Roger$q2", "$q1 Ruth$q2", "$q1 John$q2", "$q1 John$q2");
						$htmlcomment = str_replace($replacefrom, $replaceto, $htmlcomment);
						if ($authorname == "Roger") {
							//note: must be lower case
							$anger = array ("liberal", "liberals", "democrat", "democrats", "evolutionists", "limbaugh", "obama", "pelosi", "feingold", "leftist", "leftists", "communist", "communists", "feminist", "feminists", "cuts");
							$present = FALSE;
							foreach ($anger as $value) {
								if (stripos($htmlcomment, $value) !== FALSE ) {
									$present = TRUE;
									break;
								}
							}
							if ($present == TRUE) {	
							// Remove punctuation (web characters preceded or followed by a space)
							$urlbrackets    = '\[\]\(\)';
							$urlspacebefore = ':;\'_\*%@&?!' . $urlbrackets;
							$urlspaceafter  = '\.,:;\'\-_\*@&\/\\\\\?!#' . $urlbrackets;
							$urlall         = '\.,:;\'\-_\*%@&\/\\\\\?!#' . $urlbrackets;
							$htmlcomment = preg_replace( '/((?<= )|^)[' . $urlspacebefore . ']+/u', ' ', $htmlcomment );
							$htmlcomment = preg_replace( '/[' . $urlspaceafter . ']+((?= )|$)/u', ' ', $htmlcomment );							
							$babysissy = explode (" ", $htmlcomment);
							foreach ($babysissy as &$value)	{
								if (in_array(strtolower($value), $anger) == FALSE) {
										$value = "blah";
								}
								else $value = "<B>" . strtoupper ($value) . "</B>";
							}						
							$stringgg = implode (" ", $babysissy) . " (" . count($babysissy) . " words)";
							$htmlcomment = $stringgg;
							}
						}
						//add in the user's graphic
						if ($authorname == "Jon" || $authorname == "Rae" || $authorname == "Karl" || $authorname == "Monica" || $authorname == "Rachel" || $authorname == "Larry") {
							$htmlcomment = "<img src=\"/gfx/" . strtolower($authorname) . ".jpg\" border=0 width=85 height=85 style=\"float:left; margin-right: 8px; margin-bottom: 8px\">" . $htmlcomment;
						}
						//display the comment itself
						$ccc .= "<tr><td class=\"copy sidepad border$hilite\"><div id=\"c_$commentid\"$hidecomment>$changenotice&nbsp;<br>$htmlcomment<br>&nbsp;</div></td></tr>";
						$cb_id[] = $commentid;
						$cb_irt[] = $inreplyto;
						$cb_ccc[] = $ccc;
						$cb_cd[] = 0;
						$cb += 1;
					}
				}
				//thread and display comments
				require ('inc_commthreader.php');
				
				//display footer
				if ($hideallexcept == 0) echo "<hr noshade size=1><form name=\"markread\" action=\"index.php\" method=\"POST\"><input type=\"hidden\" name=\"markasread\" value=\"$conv_id\"><input type=\"hidden\" name=\"readdate\" value=\"" . time() . "\"><input type=\"submit\" value=\"Mark as read\" title=\"Mark all comments in this conversation as read.\"></form><hr noshade size=1><br>";
				echo "<form name=\"commentform\" method=\"post\" action=\"conversations.php";
				if ($hideallexcept == 0) {
					$postcomm = "Post a comment:";
					$postbutt = "add comment";
					echo "?id=$conv_id&action=new";
					if ($userid == 1) {$hiddenitems ="In reply to: <input type=\"text\" name=\"inreplyto\" class=\"small\" size=3 value=0 onfocus=\"this.select();\"> &nbsp;";}
					} else {
					if (isset($replytoid)) {
						if ($authorid == $userid) {
							$postcomm = "Post a follow-up comment:";
							$postbutt = "add comment";
						} else {
							$postcomm = "Reply to $authorname's comment:";
							$postbutt = "post reply";
						}
						$canceledit = "<input type=\"button\" value=\"Cancel\" onclick=\"if (confirm('Cancel this reply and lose all changes?')) document.location.href='conversations.php?id=$conv_id';\" class=\"small gray\">&nbsp; ";
						$comment = "[quote=$authorname]" . addslashes($comment) . "[/quote]";
						if ($userid == 1) {
							$hiddenitems = "<input type=\"text\" name=\"inreplyto\" value=\"$replytoid\" class=\"small\" size=3 onfocus=\"this.select();\"> &nbsp;<input type=\"hidden\" name=\"qe\" value=\"$comment\">";
						} else {
							$hiddenitems = "<input type=\"hidden\" name=\"inreplyto\" value=\"$replytoid\"><input type=\"hidden\" name=\"qe\" value=\"$comment\">";
						}
						echo "?id=$conv_id&comid=$hideallexcept&action=new";
					} else {
						$postcomm = "Edit this comment:";
						$postbutt = "save changes";
						$canceledit = "<input type=\"button\" value=\"Cancel\" onclick=\"if (confirm('Cancel editing and lose all changes?')) document.location.href='conversations.php?id=$conv_id';\" class=\"small gray\">&nbsp; ";
						$posttime = strtotime($commentdate) + ($tz * 3600);
						$posttime = date('M d, Y g:i a', $posttime);
						$posttime = " value=\"$posttime\"";
						echo "?id=$conv_id&comid=$hideallexcept&irtid=$inreplyto&action=update";
						$editcomment = $comment;
						$replacefrom = array("\'",'\"');
						$replaceto = array("'",'"');
						$editcomment = str_replace($replacefrom, $replaceto, $editcomment);
						if ($userid == 1) {$hiddenitems ="In reply to: <input type=\"text\" name=\"inreplyto\" value=\"$inreplyto\" class=\"small\" size=3 onfocus=\"this.select();\"> &nbsp;";}
					}
				}
				echo "\"><table border=0 cellpadding=2 cellspacing=0 class=\"medium\">";
				if ($userid == 1) {
					echo "<tr><td colspan=2><table border=0 cellpadding=0 cellspacing=0 class=\"medium\"><tr><td><h2>$postcomm</h2></td>$tdspacer<td>as&nbsp;</td><td><select name=\"postingas\" class=\"small\">";
					$res = mysql_query("SELECT * FROM users ORDER BY username",$db);
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
					echo "</select></td><td>&nbsp;at time&nbsp;</td><td><input type=\"text\" name=\"postingat\" width=13 class=\"small\"$posttime></td><td class=\"small\">&nbsp;(<a href=\"javascript://\" onclick=\"document.forms.commentform.postingat.value='';\">now</a>)</td></tr></table></td></tr>";
				} else {
					echo "<tr><td colspan=2><h2>$postcomm</h2></td></tr>";
				}
				$allcomments = rtrim($allcomments, ":");
				$unreadcomments = rtrim($unreadcomments, ":");
				echo "<tr><td colspan=2><input type=\"hidden\" name=\"ac\" value=\"$allcomments\"><input type=\"hidden\" name=\"ntc\" value=\"$unreadcomments\"><textarea name=\"comment\" cols=65 rows=9 class=\"medium\">$editcomment</textarea></td></tr>";
				echo "<tr><td class=\"small blue\">$hiddenitems<input type=\"submit\" value=\"$postbutt\"></td><td align=\"right\">$canceledit</td></tr>";
				echo "</table></form>";
			}
		}
	}
?>

</td></tr></table>
<script type="text/javascript">
window.onload=hide('ncb1');show('ncb2');habtop();
</script>
</body>
</html>
<?php
}
?>