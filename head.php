<?php
// db Connection
//	$db = mysql_connect('internal-db.s7387.gridserver.com', 'db7387', '***REMOVED***');
//	mysql_select_db ("db7387_kovaciny");
	$db = mysql_connect('***REMOVED***', '***REMOVED***', '***REMOVED***');
	mysql_select_db ("db286662785");
	$tz = -12;

require_once('functions.php');

// Log in
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
		elseif ($login == "nate" && $pass == "G87uJy73g") {$username = $login;}
		elseif ($login == "monica" && $pass == "6RF35H5hw") {$username = $login;}
		elseif ($login == "john" && $pass == "6RF35H5hw") {$username = $login;}
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
			header("Location: http://www.kovaciny.com/login.php");
		} else {
			$usertoken = $_COOKIE['user'];
			if (strlen($usertoken) == 0) {
				session_start();
				$usertoken = $_SESSION['user'];
			}
			if ($usertoken == md5("hello this is jon")) {$username = "jon";}
			elseif ($usertoken == md5("hello this is rae")) {$username = "rae";}
			elseif ($usertoken == md5("hello this is roger")) {$username = "roger";}
			elseif ($usertoken == md5("hello this is ruth")) {$username = "ruth";}
			elseif ($usertoken == md5("hello this is karl")) {$username = "karl";}
			elseif ($usertoken == md5("hello this is larry")) {$username = "larry";}
			elseif ($usertoken == md5("hello this is rachel")) {$username = "rachel";}
			elseif ($usertoken == md5("hello this is anna")) {$username = "anna";}
			elseif ($usertoken == md5("hello this is nate")) {$username = "nate";}
			elseif ($usertoken == md5("hello this is monica")) {$username = "monica";}
			elseif ($usertoken == md5("hello this is john")) {$username = "john";}
			else {$usernotfound = "usernotfound";}
		}
	}

// Log in OK
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
		<link href="kovaciny.css" rel="stylesheet" type="text/css">
		<script language="JavaScript" src="kovaciny.js" name="jsinc"></script>
		</head>
		
		<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0">
		<table width="100%" border=0 cellpadding=0 cellspacing=0 bgcolor="#6699CC" class="medium white">
			<tr>
				<td width=219><a href="/"><img src="gfx/kovaciny.gif" border=0 width=199 height=60 hspace=10></a></td>
				<td align="center">
					<?php
						echo "Welcome <b>$me</b>!";
					?></td>
				<td width=10>&nbsp;</td>
				<td><form id="headerSearchForm" name="headerSearchForm" method="GET" action="conversationsearch.php">
					<input id="headerSearchBox" name="q" class="copy headerSearchBox" type="text" title="Search" placeholder="Search" tabindex="10"><input id="headerSearchButton" type="submit" class="copy searchbutton" title="Click to search" value="">
				</form></td>
				<td width=10>&nbsp;</td>
			</tr>
		</table>
		<table width="100%" border=0 cellpadding=0 cellspacing=0><tr valign="top" height=800><td bgcolor="#DDDDDD" style="padding:5px" width=130>
		<p class="b" style="padding-top: 10px">Conversations</p>
		<ul style="padding-left: 10px">
			<li style=""margin-bottom: 10px""><a href="index.php">Main page</a></li>
			<li><a href="newconv.php">Add new</a></li>
			<li><a href="search.php">Search</a></li>
			<li><a href="/w/">Kovawiki</a></li>
			<li><a href="index.php?logout=true">Log out</a></li>
		</ul>
		<p class="b" style="padding-top: 10px">Photo Gallery</p>
		<ul style="padding-left: 10px">
			<li><a href="/gallery">Main Page</a></li>
			<li class="small"><a href="/gallery/v/karl/">Karl</a></li>
			<li class="small"><a href="/gallery/v/jonrae/">Jon &amp; Rae</a></li>
			<li class="small"><a href="/gallery/v/larryrachel/">Larry &amp; Rachel</a></li>
			<li class="small"><a href="/gallery/v/annanate/">Anna &amp; Nate</a></li>
			<li class="small"><a href="/gallery/v/monica_john/">Monica &amp; John</a></li>
		</ul>
<!--<table border=0 cellpadding=0 cellspacing=0 align="center" width=120><tr><td align="center"><?php //@readfile('http://www.kovaciny.com/gallery/main.php?g2_view=imageblock.External&g2_blocks=randomImage&g2_show=none&g2_maxSize=92');
?></td></tr></table>-->
		<p class="b" style="padding-top: 10px">Blogs</p>
		<ul style="padding-left: 10px">
			<li><a href="http://www.livejournal.com/users/noumignon/">Karl</a><span class="small"> (<a href="http://www.livejournal.com/users/noumcomments/">comm</a>)</small></li>
			<li><a href="http://octavo-dia.blogspot.com/">Larry</a></li>
			<li><a href="http://hamlette.blogspot.com/">Rachel</a><span class="small"> (<a href="http://rachelkovaciny.blogspot.com/">#2</a>)</span></li>
		</ul>
        <p><font color="#CCCCCC"><small>***REMOVED***41</small></font></p>
		</td><td width=10>&nbsp;</td><td class="copy" style="padding:5px">
		<?php
	} else {
		header("Location: http://www.kovaciny.com/login.php");
	}
?>





