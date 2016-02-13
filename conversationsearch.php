<?php
$db = mysql_connect('***REMOVED***', '***REMOVED***', '***REMOVED***');
mysql_select_db ("db286662785");
$tz = -12;

require_once('functions.php');

$q = stripslashes($_GET['q']);

$q_phrases = explodePhrases($q);

//OR creates multiple search strings, this way at least one of the OR words will be present, unlike Boolean default

$OR_locations = array_keys($q_phrases, "OR");
$OR_locations = implode($OR_locations);

if (!empty($OR_locations) ) {
	//Ignore ORs that don't have two words around them
	function isOrValid($array, $key){
		$keys = array_keys($array);
		$cur = $key;
		$prev = $key - 1;
		$next = $key + 1;
		if ($array[$keys[$prev]] == NULL || $array[$keys[next]] == NULL) return false;
		if ($array[$keys[$next]] == "OR") return false;		
	}
	
	function removeBadOrs ($array) {
		$found = false;
		while (!$found) {
			foreach($array as $key=>$value) {
				if ($value == "OR") {
					if (!isOrValid($array, $key)) {
						unset($array[$key]);
						$found = true;
					}
				}
			}
		}
	}
	
	
	
	
	if ($OR_locations[0] === 0) {
		unset($OR_locations[0]);
	}
	if ( ( $end = end($OR_locations) ) == (sizeof($q_phrases) - 1) ) {
		echo "end is $end<br> and the last in qphrases is " . sizeof($q_phrases) . "<BR>"; //debug
		echo "or end is " . current($OR_locations) . "<br>";
		var_dump (current($OR_locations));
		echo "<br>";
		array_pop($OR_locations);
	}
	$keys = array_keys($OR_locations);
	for ($i = 0; $i < sizeof($keys); $i++) {
		$cur = $OR_locations[$keys[$i]];
		$next = $OR_locations[$keys[$i + 1]];
		if ($next == $cur + 1) {
			unset($OR_locations[$keys[$i]]);
		}
	}
	reset($OR_locations);
	
	
	echo "OR locations is now ";
	var_dump($OR_locations);
	echo "<BR>";
	
	$keys = array_keys($q_phrases);
	$cur = $q_phrases[$keys[0]];
	$next = $q_phrases[$keys[1];
	while ($next == "OR") {
		$OR_group_start[] = $cur;
		$cur = $q_phrases[$keys[0]];
		$next = $q_phrases[$keys[1];
	}
	
	
	while (key($OR_locations) !== null) { //note this happens on all normal searches
	//for (reset($OR_locations); key($OR_locations) !== null; next($OR_locations)) {
		
		$key = key($OR_locations); //debug
		$value = current($OR_locations);
		echo "current value is ";
		echo "$value";
		echo "<BR>";
		$OR_groups[] = $value;
		while ( $OR_locations[key($OR_locations) + 1] == ($value + 2) ) {
			/*echo "this code is running, key is $key<BR>";
			echo "this code is running, value is $value<BR>";
			$value = current($OR_locations);
			next($OR_locations);		
			echo "this code is done running, key is $key<BR>";
			echo "this code is done running, value is $value<BR>";
	*/
			//$OR_groups[] = $value;
			next($OR_locations);
			echo current($OR_locations) . "is current locations and is a group<BR>";
		}
		next($OR_locations);
		//echo "value is now $value -- end next loop<BR><BR>";
	}

	reset($OR_locations);
	reset($OR_groups);
	echo "or groups start at "; //debug
	foreach ($OR_groups as $value) {
		echo "$value: \"$q_phrases[$value]\"<BR>"; //debug
		
	}
	$q_alternatives = array();//build one search string for each combination of phrases
	foreach ($q_phrases as $key=>$value) {
/*		echo "<BR> current or locations is ";
		$sww = current($OR_locations);
		var_dump($sww);
		echo "<BR> ";*/
		
		//branch the search string for each OR group.
		//branch each branch for each OR in the group.
		for ($i = 0; $i < ( sizeof($OR_groups) * sizeof($OR_locations) ); $i++) {
			$q_alternatives[] = $q_phrases;
		}
		
		for ($i = 0; $i < sizeof($q_alternatives); $i++) {
			//in each branch, pick one of the OR words in each group and eliminate the others and the ORs.
			foreach ($OR_groups as $value) {
				$or_loc = current($OR_locations);
				/*while ($or_loc < $value) {
					$q_alternatives[$i][$or_loc-1]
				}*/
			}
		}
		
		if ( ($key + 1) == current($OR_groups) ) { //if an OR is upcoming
			echo "OR after value $value<BR>"; //debug
			//is it the start of a group? Then generate 
		}
	}

	for ($i=0; $i<sizeof($q_phrases); $i++) {
		//create a new search string for each group of ORs
	}
	/*
	while ( $or_loc = array_search("OR", $q_phrases) ) { //purposely not running loop if or_loc == 0
		$q_alternatives[] = $q_phrases;
		echo "end(qaltern) = ";
		echo count($q_alternatives)-1;
		echo "<br>";
		array_splice($q_alternatives[count($q_alternatives)-1], $or_loc, 2); //store a search string with OR and following phrase removed
		array_splice($q_phrases, $or_loc-1, 2); //main string now only has the second phrase
	}
	$q_alternatives[] = $q_phrases;*/
	/*echo "<h1>Done running loop.</h1>q_alternatives is now an array of arrays of phrases, each of which will become a search string:<br><br><div style=\"margin:25px\">";
	foreach($q_alternatives as $value) {
		echo "Phrase " . $i++ . ":<BR>";
		var_dump($value);
		echo "<BR><BR>";
	}
	echo "<br><br></div>"; //debug*/
		//search $searchPhrases for operators: OR for now.
		//parse $searchPhrases into separate query groups: OR MATCH, ORDER BY, AGAINST conversations, author = '$authorname'
		//for only the remaining ones, and the ones with OR, explode and prep for Boolean search.
}

$q_searchstring = preprocessForSqlBoolean($q);
$sql = "SELECT `conid`, `contitle`, `changedate`, `createdate`, `numcomm`, `visible` FROM `conversations` 
		WHERE `visible`='Y' AND MATCH `contitle` AGAINST ('$q_searchstring' IN BOOLEAN MODE) ORDER BY `changedate` DESC";
$res = mysql_query($sql) or die (mysql_error());
if( mysql_num_rows ($res) == 0 ) {
	$redirect = "Location:http://www.kovaciny.com/k/search.php?q=$q";
	header($redirect);
}
require_once("head.php");
$q_safe = htmlentities($q);
echo "<h1 style=\"padding-top: 7px\">Search Results</h1>";
echo "<p class=\"copy\">These conversation titles matched your search terms. ";
echo "Click <a class=\"content\" href=\"http://www.kovaciny.com/k/search.php?q=$q_safe\">here</a> to search comments instead.</p>";
echo "<table border=0 cellpadding=0 cellspacing=0 class=\"indent medium\">";
echo "<tr class=\"small\"><td>Title</td><td>Most recent post</td></tr>";
echo "<tr bgcolor=\"#6699CC\"><td colspan=2><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
while($convs = mysql_fetch_array($res)) {
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
		echo "<tr$rowcolor><td class=\"rowpad sidepad\" ><a href=\"conversations.php?id=$convid\">$contitle</a> ($numcomm)</td><td nowrap class=\"small rowpad sidepad\">$convdate ago</td></tr>";
}
echo "</td></tr></table>
</body>
</html>";
?>