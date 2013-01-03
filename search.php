<?php require_once ('head.php');
	if ($username) {
		if (isset($_GET['q'])) {
			$q = $_GET['q']; $maxallowed = 500; $rpp = 10;
            if (isset($_POST['nr'])) {$nr = $_POST['nr'];} else {$nr = 0;}
			if ($nr > 0 && $nr <= $maxallowed) {// prevents requerying for the record count
				$numhits = $nr;
			} else {
				$res = mysql_query("SELECT `c`.`comid`, `c`.`conid`, `c`.`comment`, `cv`.`contitle`, `c`.`authorid` FROM `comments` AS `c`, `conversations` AS `cv` WHERE MATCH (`c`.`comment`) AGAINST ('$q') AND `c`.`conid` = `cv`.`conid` LIMIT $nr , $rpp",$db);
				$numhits = mysql_num_rows($res);
			}
			if ($numhits>$rpp) {
				$maxPage = floor($numhits/$rpp);
				if ($numhits == $maxallowed) $maxPage--;
				$pageid = $_POST['p'];
				if ($pageid == "") {$pageid = 0;} else {$pageid -= 0;}
				$upid = $pageid + 1; $downid = $pageid - 1; $maxup = $maxPage + 1;
				?>
					<script language="JavaScript" type="text/JavaScript">
					function pnav(p,nr) {
						document.forms.AdvancedSearch.p.value=p; document.forms.AdvancedSearch.nr.value=nr; document.forms.AdvancedSearch.submit();}
					</script>
				<?php
				$pagenav = "<table border=0 cellpadding=3 cellspacing=0 class=\"medium\" align=\"center\"><tr>";
				if ($pageid > 0) {
					$pagenav .= "<td>[<a href=\"javascript://\" onclick=\"pnav(0,$numhits);\">First</a>]</td><td>[<a href=\"javascript://\" onclick=\"pnav($downid,$numhits);\">Prev</a>]</td>";
				} else {
					$pagenav .= "<td>[<span class=\"gray\">First</span>]</td><td>[<span class=\"gray\">Prev</span>]</td>";
				}
				$pagenav .= "<td><a href=\"javascript://\" title=\"Go to page ...\" onclick=\"var p = prompt('Go to page: (1 - $maxup allowed)'); TestRegExp = /[0-9]+/g; if(TestRegExp.test(p)) {if(p>=1 && p<=$maxup) {p--; pnav(p,$numhits);} else {confirm(p + ' is outside the valid page range. Please try again.');}} else {confirm(p + ' is not a valid number. Please try again.');}\"><b>$upid</b> of <b>$maxup</b></a></td>";
				if ($pageid < $maxPage) {
					$pagenav .= "<td>[<a href=\"javascript://\" onclick=\"pnav($upid,$numhits);\">Next</a>]</td><td>[<a href=\"javascript://\" onclick=\"pnav($maxPage,$numhits);\">Last</a>]</td>";
				} else {
					$pagenav .= "<td>[<span class=\"gray\">Next</span>]</td><td>[<span class=\"gray\">Last</span>]</td>";
				}
				$pagenav .= "</tr></table><br>";
				$offset = $pageid * $rpp; $paging = "LIMIT $offset, $rpp";
			}
			if ($numhits == 0) {
				if (strlen($searchmod)==0) $searchmod = "<p>Please try alternative terms, check your spelling, or give up.</p>";
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"100%\"><tr valign=\"top\"><td><h1>Search Results</h1><p>No conversations were found containing <b>$q</b>:</p>$searchmod</td><td width=10>&nbsp;</td><td align=\"right\">$searchbox</td></tr></table>";
			} else {
				if ($numhits == $maxallowed) {
					$numhits = "more than $maxallowed results";
				} else {
					$numhits = "$numhits " . pluralize($numhits, "result");
				}
				echo "<table border=0 cellpadding=0 cellspacing=0 width=\"100%\"><tr><td><h1>Search Results</h1><p>Your search for <b>$q</b> returned $numhits:</p>$searchmod</td><td width=10>&nbsp;</td><td align=\"right\">$searchbox</td></tr></table><br>$pagenav$searchnote<table border=0 cellpadding=0 cellspacing=0 class=\"medium\" width=\"100%\">";
				while($searchresults = mysql_fetch_array($res)) {
					$conid = $searchresults["conid"];
					$contitle = $searchresults["contitle"];
					$comid = $searchresults["comid"];
					$comment = $searchresults["comment"];
    					$replacefrom = array(chr(10));
	      				$replaceto = array(" &middot; ",);
	       				$comment = str_replace($replacefrom, $replaceto, $comment);
					$authorid = $searchresults["authorid"];
					echo "<tr><td class=\"b tdot\"><a href=\"conversations.php?id=$conid&com=$comid\">$contitle</a></td><td class=\"tdot\">$authorid</td><td class=\"tdot\">$comment</td></tr>";
                }
                echo "</table><br>$pagenav<br>";
            }
        } else {
            echo "<h1>Search</h1><form name=\"search\" method=\"GET\" action=\"search.php\"><input type=\"text\" size=20 name=\"q\">&nbsp;<input type=\"submit\" value=\"Search\"></form>";
		}
	}

?></td></tr></table>
</body>
</html>
