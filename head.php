<?php
// db Connection
	require_once('config.php');
	$db = mysql_connect(SQL_HOST, DATABASE, DB_PASSWORD); //no error, yes connected
	if (!$db) {
		die('Not connected : ' . mysql_error());
	}

	$db_selected = mysql_select_db (DATABASE, $db);
	if (!$db_selected) {
		die ('Can\'t select db : ' . mysql_error());
	}
	$tz = -12;

require_once('functions.php');

// Log in
	$username = "";
	if (isset($_GET['user'])) {
		$login = strtolower($_GET['user']);
		$pass = $_GET['pass'];
		if ($login == "jon" && $pass == "2i7GEkf4w") {$username = $login;}
		elseif ($login == "rae" && $pass == "3DP4t2u7H") {$username = $login;}
		elseif ($login == "roger" && $pass == "YLxi3I249") {$username = $login;}
		elseif ($login == "ruth" && $pass == "7694jyJVp") {$username = $login;}
		elseif ($login == "karl" && $pass == "93Lmevb23") {$username = $login;}
		elseif ($login == "larry" && $pass == "dFy83tQ7r") {$username = $login;}
		elseif ($login == "rachel" && $pass == "NJpbqskGw") {$username = $login;}
		elseif ($login == "anna" && $pass == "VX9UkcCjy") {$username = $login;}
		elseif ($login == "monica" && $pass == "6RF35H5hw") {$username = $login;}
		elseif ($login == "john" && $pass == "6RF35H5hw") {$username = $login;}
		//elseif ($login == "nate" && $pass == "G87uJy73g") {$username = $login;}
		if (strlen($username)) {
			setcookie("user", md5("hello this is $username"), time()+14400, "/", "kovaciny.com", 0);
			if (isset($_COOKIE['user']) === false) {
				session_start();
				$_SESSION['user'] = md5("hello this is $username");
			}
		}
	} else {
		if (isset($_GET['logout'])) {
			if (isset($_COOKIE['user'])) {
				setcookie("user", md5("hello this is $username"), time()-3600, "/", "kovaciny.com", 0);
			} elseif (isset($_SESSION['user'])) {
				$_SESSION['user'] = "";
				session_destroy();
			}
			header("Location: " . HOST_NAME . "/login.php");
		} else {
			if (!empty($_COOKIE['user'])) {
				$usertoken = $_COOKIE['user'];
			} else {
				session_start();
				$usertoken = !empty($_SESSION['user']) ? $_SESSION['user'] : "";
			}
			if ($usertoken == md5("hello this is jon")) {$username = "jon";}
			elseif ($usertoken == md5("hello this is rae")) {$username = "rae";}
			elseif ($usertoken == md5("hello this is roger")) {$username = "roger";}
			elseif ($usertoken == md5("hello this is ruth")) {$username = "ruth";}
			elseif ($usertoken == md5("hello this is karl")) {$username = "karl";}
			elseif ($usertoken == md5("hello this is larry")) {$username = "larry";}
			elseif ($usertoken == md5("hello this is rachel")) {$username = "rachel";}
			elseif ($usertoken == md5("hello this is anna")) {$username = "anna";}
			elseif ($usertoken == md5("hello this is monica")) {$username = "monica";}
			elseif ($usertoken == md5("hello this is john")) {$username = "john";}
			else {$usernotfound = "usernotfound";}
			// elseif ($usertoken == md5("hello this is nate")) {$username = "nate";}
		}
	}

// Log in OK, show header
	if ($username) {
		if ($username == "jon") {$userid = 1;} elseif ($username == "rae") {$userid = 2;}
		elseif ($username == "karl") {$userid = 3;} elseif ($username == "rachel") {$userid = 4;}
		elseif ($username == "larry") {$userid = 5;} elseif ($username == "nate") {$userid = 6;}
		elseif ($username == "anna") {$userid = 7;} elseif ($username == "monica") {$userid = 8;}
		elseif ($username == "roger") {$userid = 10;} elseif ($username == "ruth") {$userid = 9;}
		elseif ($username == "john") {$userid = 11;}
		$me = ucfirst($username);
		?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
		<title>Kovaciny.com</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<?php 
		if (DEBUG) {
			$rand = floor(rand() * 100);
			$css_source = "kovaciny.css?dev=$rand";	//always refresh
			$hosted_jquery = "http://code.jquery.com/jquery-2.1.4.js";
			$js_source = "scripts/kovaciny.js?dev=$rand";
		} else {
			$css_source = "kovaciny.css?1";	//version number forces refresh
			$hosted_jquery = "http://code.jquery.com/jquery-2.1.4.min.js";
			$js_source = "scripts/kovaciny.js?13";
			echo "<script>console.log = function(){}</script>"; //turn logging off
		}
		echo "<link href=\"$css_source\" rel=\"stylesheet\" type=\"text/css\">\n\t\t";
		echo "<script src=\"$hosted_jquery\" type=\"text/javascript\"></script>\n\t\t";
		echo "<script src=\"$js_source\"></script>\n\t\t";
		?>
		
		</head>
		
		<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
		<table width="100%" border=0 cellpadding=0 cellspacing=0 bgcolor="#6699CC" class="medium white">
			<tr>
				<td width=219><a href="index.php"><img src="gfx/kovaciny.gif" border=0 width=199 height=60 hspace=10></a></td>
				<td align="center">
					<?php
						echo "Welcome <b>$me</b>!";
						if (DEBUG) {
							echo "<BR>Now visiting the DEVELOPMENT VERSION of k.com";
						}
					?></td>
				<td width=10>&nbsp;</td>
				<td><form id="headerSearchForm" name="headerSearchForm" method="GET" action="conversationsearch.php">
					<input id="headerSearchBox" name="q" class="copy headerSearchBox" type="text" title="Search" placeholder="Search" tabindex="10">
					<input id="headerSearchButton" type="submit" class="copy searchbutton" title="Click to search" value="">
					<input type="hidden" name="q_searchConversations" value="true">
				</form></td>
				<td width=10>&nbsp;</td>
			</tr>
		</table>
		<table width="100%" border=0 cellpadding=0 cellspacing=0>
			<tr valign="top" height=800>
				<td id="leftnavmenu">
					<p class="leftnavmenu"><a href="index.php?logout=true">Log out</a></p>
					<p class="b" style="padding-top: 10px">Links</p>
					<ul style="padding-left: 10px">
						<li class="small leftnavmenu"><a href="http://brain.kovaciny.com/">Brain</a>
						<li class="small leftnavmenu"><a href="https://twitter.com/Noumenon72">Karl's Twitter</a></li>
						<li class="small leftnavmenu"><a href="http://octavo-dia.blogspot.com/">Larry's blog</a></li>
						<li class="small leftnavmenu"><a href="http://hamlette.blogspot.com/">Rachel's blog</a></li>
						<li class="small leftnavmenu"><a href="http://pinterest.com/hamlettethedame/">Rachel's Pinterest</a></li>			
						<li class="small leftnavmenu"><a href="http://www.youtube.com/user/Stingrae57?feature=watch">Rae's YouTube</a></li>			
					</ul>
					<p><font color="#CCCCCC"><small>***REMOVED***41</small></font></p></td>
				<td id="spacer-10px"><div style="display:inline-block; width:10;">&nbsp;</div></td>
				<td id="bodyContent" class="copy">
		<?php
	} else {
		header("Location: " . HOST_NAME . "/login.php");
	}
?>
