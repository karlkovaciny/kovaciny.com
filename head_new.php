<?php
// db Connection
	
	$db= mysql_connect($_ENV['DATABASE_SERVER'], 'db7387', 'gMcJARrbgMcJARrb') or die ('I cannot connect to the database because: ' . mysql_error());
	mysql_select_db ("db7387_kovaciny");

// Functions
	function format_interval($timestamp, $granularity = 2) {
		$units = array('1 year|years' => 31536000, '1 week|weeks' => 604800, '1 day|days' => 86400, '1 hr|hrs' => 3600, '1 min|min' => 60, '1 sec|sec' => 1);
		$output = '';
		foreach ($units as $key => $value) {
			$key = explode('|', $key);
			if ($timestamp >= $value) {
				$output .= ($output ? ' ' : '') . format_plural(floor($timestamp / $value), $key[0], $key[1]);
				$timestamp %= $value;
				$granularity--;
			}
			if ($granularity == 0) {break;}
		}
		return $output ? $output : "0 sec";
	}
	
	function format_plural($count, $singular, $plural) {
		if ($count == 1) {return $singular;} else {return $count . " " . $plural;}
	}

	function pluralize($count, $singular, $plural = false) {if (!$plural) {$plural = $singular . 's';} return ($count == 1 ? $singular : $plural);}

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
			setcookie("user", md5("hello this is $username"), time()+3600, "/", "kovaciny.com", 0);
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
			header("Location: login.php");
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
			elseif ($usertoken == md5("hello this is john")) {$username = "monica";}
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
		
		<body marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>
		<table width="100%" border=0 cellpadding=0 cellspacing=0 bgcolor="#6699CC" class="medium white"><tr><td width=219><a href="/"><img src="gfx/kovaciny.gif" border=0 width=199 height=60 hspace=10></a></td><td align="center">
		<?php
			echo "Welcome <b>$me</b>.";
		?>
		</td><td width=219>&nbsp;</td></tr></table>
		<table width="100%" border=0 cellpadding=0 cellspacing=0><tr valign="top" height=800><td bgcolor="#DDDDDD" style="padding:5px" width=130>
		<ul style="padding-top: 10px">
			<li class="b"><a href="index.php">Conversations</a></li>
			<li class="b"><a href="newconv.php">Add New</a><br>&nbsp;</li>
			<li><a href="contactdir.php">Contact Directory</a></li>
			<li><a href="/gallery/">Photo Gallery</a></li>
			<li><a href="http://www.blc.edu/webservices/chat/offsitechat.asp?site=kovaciny.com" target="chatwin">Chat</a><br>&nbsp;</li>
			<li><a href="index.php?logout=true">Log out</a></li>
		</ul>
		</td><td width=10>&nbsp;</td><td class="copy" style="padding:5px">
		<?php
	} else {
		header("Location: login.php");
	}
?>