<?php
	function func_generate_string() {
		$auto_string= chr(mt_rand(ord('A'), ord('Z')));
		for ($i= 0; $i<8; $i++) {
			$ltr= mt_rand(1, 3);
			if ($ltr==1) $auto_password .= chr(mt_rand(ord('A'), ord('Z')));
			if ($ltr==2) $auto_password .= chr(mt_rand(ord('a'), ord('z')));
			if ($ltr==3) $auto_password .= chr(mt_rand(ord('0'), ord('9')));
		}
		return $auto_string;
	}

	if (isset($_POST['username']) && isset($_POST['password'])) {
		$username= ltrim(rtrim(addslashes($_POST['username'])));
		$password= ltrim(rtrim(addslashes($_POST['password'])));
		$mdpass= md5($password);
	//	$db = mysql_connect('internal-db.s7387.gridserver.com', 'db7387', '***REMOVED***');
	//	mysql_select_db ("db7387_kovaciny");
		$db = mysql_connect('***REMOVED***', '***REMOVED***', '***REMOVED***');
		mysql_select_db ("db286662785");
		$res= mysql_query("SELECT * FROM users WHERE username='$username' AND pass='$mdpass'") or die("Could not select user ID.");
		if (mysql_num_rows($res)==1) {
			$user_obj= mysql_fetch_object($res);
			$userid= $user_obj->userid;
			$username= strtolower($user_obj->username);
			$logcode= md5(func_generate_string());
			setcookie("user", md5("hello this is $username"), time()+14400, "/", "kovaciny.com", 0);
			header("Location: http://www.kovaciny.com/index.php");
			exit;
		}
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Kovaciny.com</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="kovaciny.css" rel="stylesheet" type="text/css">
</head>

<body onLoad="document.login.username.focus();">
<div align="center" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 36px; background-color:#EEE; border-bottom: 1px solid #AAA;"><p class="small"><a href="http://www.kovaciny.com/">Kovaciny.com</a> <span style="padding: 0 20px 0 20px">&curren;</span> <a href="http://www.mankatopedia.com/">Mankato, Minnesota</a> <span style="padding: 0 20px 0 20px">&curren;</span> <a href="http://www.ukrainianbible.org/">Ukrainian Bible Translation Project</a> <span style="padding: 0 20px 0 20px">&curren;</span> <a href="http://www.minnesotavalleychorale.org/">Mankato Chorus</a> <span style="padding: 0 20px 0 20px">&curren;</span> <a href="http://www.blc.edu/">Lutheran Colleges</a></p></div>
<table border=0 cellpadding=0 cellspacing=0 height="70%" width="100%"><tr><td align="center"><form name="login" method="post" action="login.php">
		<h1>Please Sign In</h1><table border=0 cellpadding=3 cellspacing=0><tr><td><input name="username" type="text" size=13 maxlength=10></td></tr>
		<tr><td><input name="password" type="password" size=13 maxlength=32></td></tr>
		<tr><td><input type="submit" value="Sign In"></td></tr>
		</table></form></td></tr></table>
</body></html>