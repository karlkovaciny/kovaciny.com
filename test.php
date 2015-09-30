<?php 
	require_once ("head.php");
   	
    function echoColumns($tablename) {
        $res = mysql_query("SHOW COLUMNS FROM `" . $tablename . "`;");
        echo "<pre>";
        while($resultArray[] = mysql_fetch_assoc($res)) {
            //echo array_pop($resultArray) . "<br>";
            echo array_slice($resultArray);
            echo "<br>";
            echo var_dump(array_pop($resultArray));
        }
        echo "</pre>";
    }

    function echoTableStatus() {
        $res = mysql_query ("SHOW TABLE STATUS FROM " . DATABASE . ";");
        echo mysql_num_rows($res);
        echo "<table>";
        $resultArray = array();
        while ($resultArray[] = mysql_fetch_assoc($res)) {
                $entry = array_pop($resultArray);
                foreach ($entry as $key => $value) {
                    echo "<tr><td>$key</td><td>$value</td>";
                    echo "</tr>";
                }
                echo "<tr class=\"blueHR\"><td colspan=2 class=\"blueHR\"><img src=\"gfx/-.gif\" border=0 width=1 height=1></td></tr>";
                echo "</table>";
        }
    }
    
/**
*   Content
*
*/
    echo "<div width=\"100%\"></div>";
    
    
    echo "date.timezone: " . ini_get('date.timezone') . "<BR><BR>";

    
?>
</td></tr></table>
</body>
</html>
