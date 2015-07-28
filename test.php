<?php 
require_once ("head.php");
echo "<div width=\"100%\"></div>";
/*$res = mysql_query("SHOW COLUMNS FROM `conversations`;");
while($resultArray[] = mysql_fetch_assoc($res)) {
	//echo array_pop($resultArray) . "<br>";
	echo array_slice($resultArray);
	echo "<br>";
	echo var_dump(array_pop($resultArray));
}*/

echo "date.timzone: " . ini_get('date.timezone');

$res = mysql_query("SELECT NOW() as time");
while($resultArray[] = mysql_fetch_assoc($res)) {
	//echo array_pop($resultArray) . "<br>";
	//echo array_slice($resultArray);
	echo "---";
	echo var_dump(array_pop($resultArray));
}
?>
</td></tr></table>
</body>
</html>
