<?php 
require_once ("head.php");
?>

<div id="greeting">
<P>"Hello, world!"</P>
</div>
<script>
function hello() {
	var txtNode = document.createTextNode("Hello, element world!");
	var greetingEl = document.createElement('p');
	var greetingNode = document.getElementById('greeting');
	// var greeting = "Hello, Javascript world!";
	greetingNode.appendChild(txtNode);
}
window.onload = hello();
</script>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

	       // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Username');
        data.addColumn('number', 'Posts');

		<?php
		//get a list of the usernames and ids 
		$res_users = mysql_query ("SELECT `userid`, `username` FROM `users`") or die ("Error getting usernames: " . mysql_error() . "<br />");
		while ($row=mysql_fetch_array($res_users)) {
			$key = $row["userid"];
			$userlist[$key] = $row["username"];
		}
		
		$sql = "SELECT COUNT(1) AS theCommentCount, `authorid`, `visible`, `createdate` FROM `comments`
			WHERE `visible`='Y' AND DATEDIFF(CURDATE(), `createdate`) <= 7
			GROUP BY `authorid` ORDER BY theCommentCount DESC";
		$res_comment_count = mysql_query($sql) or die ("Error getting comment count: " . mysql_error() . "<br/>");
		
		echo "data.addRows([";
		while ($row2 = mysql_fetch_array($res_comment_count)) {
			$username = $userlist[$row2['authorid']];
			echo "['" . $username . "', " . $row2['theCommentCount'] . "],\n";
		}
		echo "['', 0]";
		
		echo "]);";
		?>
		
        // Set chart options
        var options = {'title':'Posts per user this week',
                       'width':600,
                       'height':300};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
	  function selectHandler(e) {
			alert('A table row was selected');
		}
		// Every time the table fires the "select" event, it should call your
		// selectHandler() function.
		google.visualization.events.addListener(data, 'select', selectHandler);

		
    </script>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>

</body>
</html>