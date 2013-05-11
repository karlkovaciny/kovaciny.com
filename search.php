<?php require_once ('head.php');
	if ($username) {
		//get a list of the usernames and ids
		$res_users = mysql_query ("SELECT `userid`, `username` FROM `users`") or die ("Error getting usernames: " . mysql_error() . "<br />");
		while ($row=mysql_fetch_array($res_users)) {
			$key = $row["userid"];
			$userlist[$key] = $row["username"];
		}
		
		//Show the search form if we don't have a search string yet
		if (!isset($_REQUEST['q'])) {			
			?>
			<h1>Search</h1>
			<form name="search" method="GET" action="search.php">
				<table border=0 cellpadding=0 cellspacing=0 class="medium">
					<tr>
						<td style="padding:5px" class="large">Find comments with...&nbsp;</td>
						<td style="padding:5px"></td></tr>
					<tr>
						<td style="padding:5px">these words:&nbsp;</td>
						<td style="padding:5px"><input class="copy" type="text" size=20 name="q"></td></tr>
					<tr>
						<td style="padding:5px">by this author:&nbsp;</td>
						<td style="padding:5px">
							<select class="copy" name="q_author" style="width:85%">
								<option value = "" selected></option>
								<?php 
								foreach ($userlist as $key=>$value) {
									echo "<option value = \"" . $key . "\">" . $value . "</option><br />";
								} ?>
							</select></td></tr>
					<tr>
						<td style="padding:5px">and thread title contains:&nbsp;</td>
						<td style="padding:5px"><input class="copy" type="text" size=20 name="q_title"></td></tr>
					<tr>
						<td style="padding:5px">within the last:&nbsp;</td>
						<td style="padding:5px">
							<select class="copy" name="q_timeframe" style="width:85%">
								<option value = "" selected></option>
								<option value = "week">week</option>
								<option value = "month">month</option>
								<option value = "year">year</option>
							</select></td></tr>
					<tr>
						<td style="padding:5px" ><input type="submit" value="Search"></td>
						<td style="padding:5px"><input type="checkbox" name="q_oldestfirst" value="oldestfirst">&nbsp;Show older posts first&nbsp;&nbsp;<br>
						<input type="checkbox" name="q_matchAllComments" value="matchall">&nbsp;Show all comments in matching conversations&nbsp;&nbsp;</td>
						</tr>	
				</table>
			</form>
			<script language='javascript' type='text/javascript'>
				function SetFocus()	{
					var txtMyTextBox = document.forms.search.q;
					txtMyTextBox.focus();
				}
				SetFocus();
			</script>	
			<?php
					
		// In this case we do have a search string, so let's pull it into variables.
		} else {
			$q = stripslashes($_REQUEST['q']); 
			$q_searchstring = preprocessForSqlBoolean($q);
			$q_author = $_REQUEST['q_author'];
			$q_title = stripslashes($_REQUEST['q_title']);
			$q_oldestfirst = $_REQUEST['q_oldestfirst'];
			$q_matchAllComments = $_REQUEST['q_matchAllComments'];
			$q_timeframe = $_REQUEST['q_timeframe'];
			if (isset($_POST['resultcount'])) { //means we have navigated off the first page of results
				$resultcount = $_POST['resultcount'];
				$currentpage = $_POST['p'];
			} else {
				$resultcount = 0; 
				$currentpage = 0;
			}
								
			if ($q_matchAllComments == TRUE) {
				$rpp = 100; //more results per page when seeking big batches
				$maxallowed = 500;
			} else {
				$rpp = 10; 
				$maxallowed = 50;
			}
			$searchquery = 
				"SELECT `c`.`comid`, `c`.`conid`, `c`.`comment`, `c`.`createdate`, `c`.`authorid`, `c`.`visible`, " .
					"`conversations`.`contitle`, `users`.`userid`, `users`.`username` " .
				"FROM `comments` `c` " .
				"JOIN `conversations` ON `c`.`conid` = `conversations`.`conid` " .
				"JOIN `users` ON `c`.`authorid` = `users`.`userid` " .				
				"WHERE `c`.`visible` = 'Y' ";
				if ($q_matchAllComments == TRUE) {					
				} else {
					$searchquery .= "AND MATCH (`c`.`comment`) AGAINST ('$q_searchstring' IN BOOLEAN MODE) ";
				}				
				if ($q_author != "") {
					$searchquery .= "AND `c`.`authorid` = $q_author ";
				}
				if ($q_title != "") {
					$searchquery .= "AND MATCH (`conversations`.`contitle`) AGAINST ('$q_title' IN BOOLEAN MODE) ";
				}
				if ($q_timeframe != "") {
					switch($q_timeframe){
					case "week":
						$searchquery .= "AND DATEDIFF(CURDATE(), `c`.`createdate`) <= 7 ";
						break;
					case "month":
						$searchquery .= "AND DATEDIFF(CURDATE(), `c`.`createdate`) <= 31 ";
						break;
					case "year":
						$searchquery .= "AND DATEDIFF(CURDATE(), `c`.`createdate`) <= 365 ";
						break;
					}					
				}
				$searchquery .= "ORDER BY `c`.`createdate`";
				if ($q_oldestfirst == FALSE) {
					$searchquery .= "DESC "; //ie, newest threads first
				}
				if ($resultcount > 0) { //limit the search except for the first time when you need the record count
					$startpoint = $currentpage * $rpp;
					$searchquery .= "LIMIT $startpoint, " . min($rpp, $resultcount - $startpoint) . " ";
				} else $searchquery .= "LIMIT 0, $maxallowed ";
			
			$res = mysql_query($searchquery, $db) or die(mysql_error());
			if ($resultcount == 0) $numhits = mysql_num_rows($res); //you need the record count this first time
			else $numhits = $resultcount;
			
			//paginate results
			if ($numhits>$rpp) {
				$maxPage = floor($numhits/$rpp);
				if (($numhits % $rpp) == 0) $maxPage--;
				$pageid = $_POST['p'];
				if ($pageid == "") {$pageid = 0;} else {$pageid -= 0;}
				$upid = $pageid + 1; $downid = $pageid - 1; $maxup = $maxPage + 1;
				?>
									
					<script language="JavaScript" type="text/JavaScript">
					function pnav(p,resultcount,q_author,q_oldestfirst,q_matchAllComments,q_timeframe) { //pass the search results to the next page
						document.forms.AdvancedSearch.p.value=p; 
						document.forms.AdvancedSearch.resultcount.value=resultcount;
						document.forms.AdvancedSearch.q_author.value=q_author;
						document.forms.AdvancedSearch.q_oldestfirst.value=q_oldestfirst;
						document.forms.AdvancedSearch.q_matchAllComments.value=q_matchAllComments;
						document.forms.AdvancedSearch.q_timeframe=q_timeframe;
						//because we can't pass q or q_title as an argument when it has double quotes:
						document.forms.AdvancedSearch.q.value=document.getElementById("qqq").innerHTML; 
						document.forms.AdvancedSearch.q_title.value=document.getElementById("qqq_title").innerHTML; 
						document.forms.AdvancedSearch.submit();}
					</script>
					<form method="post" name="AdvancedSearch" action="search.php">
						<input type="hidden" name="p">
						<input type="hidden" name="resultcount">
						<input type="hidden" name="q">
						<input type="hidden" name="q_author">
						<input type="hidden" name="q_title">
						<input type="hidden" name="q_oldestfirst">
						<input type="hidden" name="q_matchAllComments">
						<input type="hidden" name="q_timeframe">
					</form>
				<?php
				//passing double quotes through the argument of onclick=pnav(0,$numhits,'$q') didn't work, so we are 
				//going to set $q here in a hidden span and access it by an element ID.
				echo "<span id='qqq' style='display:none'>" . $q . "</span>";
				echo "<span id='qqq_title' style='display:none'>" . $q_title . "</span>";
				
				$pagenav = "<table border=0 cellpadding=3 cellspacing=0 class=\"medium\" align=\"center\"><tr>";
				if ($pageid > 0) {
					$pagenav .= "<td>[<a href=\"javascript://\" onclick=\"pnav(0,$numhits,'$q_author','$q_oldestfirst', '$q_matchAllComments', '$q_timeframe');\">First</a>]</td><td>[<a href=\"javascript://\" onclick=\"pnav($downid,$numhits,'$q_author','$q_oldestfirst', '$q_matchAllComments', '$q_timeframe');\">Prev</a>]</td>";
				} else {
					$pagenav .= "<td>[<span class=\"gray\">First</span>]</td><td>[<span class=\"gray\">Prev</span>]</td>";
				}
				$pagenav .= "<td><a href=\"javascript://\" title=\"Go to page ...\" onclick=\"var p = prompt('Go to page: (1 - $maxup allowed)'); TestRegExp = /[0-9]+/g; if(TestRegExp.test(p)) {if(p>=1 && p<=$maxup) {p--; pnav(p,$numhits,'$q_author','$q_oldestfirst', '$q_matchAllComments', '$q_timeframe');} else {confirm(p + ' is outside the valid page range. Please try again.');}} else {p = $pageid; pnav(p,$numhits,'$q_author','$q_oldestfirst', '$q_matchAllComments', '$q_timeframe');}\"><b>$upid</b> of <b>$maxup</b></a></td>";
				if ($pageid < $maxPage) {
					$pagenav .= "<td>[<a href=\"javascript://\" onclick=\"pnav($upid,$numhits,'$q_author','$q_oldestfirst', '$q_matchAllComments', '$q_timeframe');\">Next</a>]</td><td>[<a href=\"javascript://\" onclick=\"pnav($maxPage,$numhits,'$q_author','$q_oldestfirst', '$q_matchAllComments', '$q_timeframe');\">Last</a>]</td>";
				} else {
					$pagenav .= "<td>[<span class=\"gray\">Next</span>]</td><td>[<span class=\"gray\">Last</span>]</td>";
				}
				$pagenav .= "</tr></table><br>";
			}
			
			//show the user what was searched for and how many results
			$searchparams = "<b>" . $q . "</b>";
			if ($q_author != "") {
				$thisuser = $userlist[$q_author];
				$searchparams .= " by <b>". $thisuser . "</b>";
			}
			if ($q_title != "") {
				$searchparams .= " in threads containing <b>" . $q_title . "</b>";
			}
			
			if ($numhits == 0) {
				if (strlen($searchmod)==0) $searchmod = "<p>Please try alternative terms, check your spelling, or give up.</p>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"100%\"><tr valign=\"top\"><td><h1>Search Results</h1><p>No conversations were found containing $searchparams.</p>$searchmod</td><td width=10>&nbsp;</td><td align=\"right\">$searchbox</td></tr></table>";
			} else {
				if ($numhits == $maxallowed) {
					$searchmod = "more than $maxallowed results";
				} else {
					$searchmod = "$numhits " . pluralize($numhits, "result");
				}
				
				//start building the table of results
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"100%\"><tr><td><h1>Search Results</h1><p class=\"copy\">Your search for $searchparams returned $searchmod.</p><p class=\"copy\"><a class=\"content\" tabindex=\"15\" href=\"search.php\">Refine search</a></p></td><td width=10>&nbsp;</td><td align=\"right\">$searchbox</td></tr></table><br />";
				echo "$pagenav";
								
				echo "<table border=0 cellpadding=0 cellspacing=0 class=\"medium\" width=\"100%\">";
				$i = 0;
				$tabindex = 10;
				while ($searchresults = mysql_fetch_array($res) AND ($i < $rpp)) {					
					$tabindex += 10;
					$conid = $searchresults["conid"];
					$contitle = $searchresults["contitle"];
					$comid = $searchresults["comid"];
					$x_username = $searchresults["username"];
					$comment = $searchresults["comment"];
						//mark quotes (this should be a function)
						$q1 = "<table border=0 cellpadding=4 cellspacing=0 class=\"border\"><tr valign=\"top\"><td class=\"green small\" bgcolor=\"#F6F6F6\">";
						$q2 = "</td><td class=\"gray sidepad medium\" bgcolor=\"#FFFFFF\">";
						$c13 = chr(13);
						$c10 = chr(10);
						$replacefrom = array(chr(10),"[quote]","[/quote]","</table>$c13<br>", "<a href=", "</a>");
						$replaceto = array("<br>","$q1 quote$q2", "</td></tr></table>","</table>", "<u><a href=", "</a></u>");
						$comment = str_replace($replacefrom, $replaceto, $comment);
						unset($replacefrom,$replaceto);
						foreach($userlist as $value) {
							$replacefrom[] = "[quote=$value]";
							$replaceto[] = "$q1 $value$q2";
						}
						$comment = str_replace($replacefrom, $replaceto, $comment);
						unset($replacefrom,$replaceto,$value);
						
						//replace line breaks
						$replacefrom = array(chr(10));
	      				$replaceto = array(" &middot; ",);
	       				$comment = str_replace($replacefrom, $replaceto, $comment);
						
						$comment = "<div id=\"c_$comid\">" . $comment . "</div>"; //so we can access it with Javascript
					
					//actually display the results
					echo "<tr><td class=\"tdot\" style = \"width:20%\"><span class=\"nou\">$x_username<br />in </span><span class=\"b\"><a href=\"conversations.php?id=$conid#comment_$comid\" tabindex=\"$tabindex\">$contitle</a></span></td>";
					echo "<td class=\"tdot\"></td><td class=\"tdot sidepad\">$comment</td></tr>";
					
					//highlight the search term (but not the operators and quotation marks)
					echo "<script>";
					$operators = "+-~<>";
					$q_phrases = explodePhrases($q, $operators);
					foreach($q_phrases as $value) {
						$value = str_replace("\"","", $value);
						if ( strpbrk(substr($value,0,1), $operators) ) {
							$value = substr($value,1);
						}
						$value = addslashes($value);
						echo "highlightInnerHTML('c_$comid', '$value');";
					}
					echo "</script>";
					$i++;
                }
                echo "</table><br>$pagenav<br>";
            }			
        }
	}

?></td></tr></table>
</body>
</html>
