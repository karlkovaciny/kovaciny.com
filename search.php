<?php require_once ('head.php');
if ($username) {
	//get a list of the usernames and ids
	$res_users = mysql_query ("SELECT `userid`, `username` FROM `users`") or die ("Error getting usernames: " . mysql_error() . "<br />");
	while ($row=mysql_fetch_array($res_users)) {
		$key = $row["userid"];
		$userlist[$key] = $row["username"];
	}
}		
$refine = !empty($_REQUEST['refine']) ? htmlentities($_REQUEST['refine']) : "";
?>
	<h1>Search</h1>
	<form name="search" method="GET" action="conversationsearch.php">
		<table border=0 cellpadding=0 cellspacing=0 class="medium">
			<tr>
				<td style="padding:5px" class="large">Find comments with...&nbsp;</td>
				<td style="padding:5px"></td></tr>
			<tr>
				<td style="padding:5px">these words:&nbsp;</td>
				<td style="padding:5px"><input class="copy" type="text" size=20 name="q" value="<?php echo $refine;?>"></td></tr>
			<tr>
				<td style="padding:5px">by this author:&nbsp;</td>
				<td style="padding:5px">
					<select class="copy" name="q_author" style="width:44%">
						<option value = "" selected></option>
						<?php 
						foreach ($userlist as $value) {
							echo "<option value = \"" . $value . "\">" . $value . "</option><br />";
						} ?>
					</select></td></tr>
			<tr>
				<td style="padding:5px">within the last:&nbsp;</td>
				<td style="padding:5px">
					<select class="copy" name="q_timeframe" style="width:44%">
						<option value = "" selected></option>
						<option value = 7>week</option>
						<option value = 30>month</option>
						<option value = 365>year</option>
					</select></td></tr>
			<tr>
				<td style="padding:5px; vertical-align:top">where thread title contains:&nbsp;</td>
				<td style="padding:5px"><input class="copy" type="text" size=20 name="q_title"><BR>
				<input type="checkbox" name="q_matchAllComments" value="matchall" onclick="document.forms.search.q.disabled=document.forms.search.q_matchAllComments.checked;">&nbsp;Retrieve every comment in these threads&nbsp;&nbsp;</td>
				</td></tr>
			<tr>
				<td style="padding:5px" ><input type="submit" value="Search"></td>
				<td style="padding:5px"><input type="checkbox" name="q_oldestfirst" value="oldestfirst">&nbsp;Show older posts first&nbsp;&nbsp;<br>
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
</td></tr></table>
</body>
</html>
