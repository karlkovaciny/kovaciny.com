 <?php 
 require_once ("head.php");
 ?>
	<div width="600" style="padding-left:180px">Posts per user this <select id="timeframeselect" class="copy" onChange="javascript:drawChart()">
		<option value="7" selected>Week</option>
		<option value="30">Month</option>
		<option value="365">Year</option>
		<option value="100000">All time</option>
	</select>
	</div>
	<!--Load the AJAX API-->
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

	<script type="text/javascript">
 
	  // Load the Visualization API and the piechart package.
	  google.load('visualization', '1.0', {'packages':['table']});
	  // Set a callback to run when the Google Visualization API is loaded.
	  google.setOnLoadCallback(drawChart);

	  // Callback that creates and populates a data table,
	  // instantiates the pie chart, passes in the data and
	  // draws it.
	  function drawChart() {

        var jsonData = $.ajax({
          url: "getstatistics.php",
          dataType:"json",
          async: false
          }).responseText;

		var data = new google.visualization.DataTable(jsonData);
/*			
		var selector = document.getElementById("timeframeselect");
		var timeframevalue = selector.value;
		console.log(timeframevalue);
		var timeframename = selector.options[selector.selectedIndex].text;
		console.log(timeframename);
		
		
	
	  // Create the data table.
	    var data = new google.visualization.DataTable();
	    data.addColumn('string', 'Username');
		data.addColumn('string', 'Searchlink');
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
			echo "['" . $username . "', " . 
				"\"search.php?q_author=" . $row2['authorid'] . "&q_timeframe=week&q_title=&q_matchAllComments=matchall\", " .
				$row2['theCommentCount'] . "],\n";
		}
		echo "['', '', 0]"; //hack because of trailing comma issue
		echo "]);";
		
		?>
		
		var view = new google.visualization.DataView(data);
		view.setColumns([0, 2]); //because we are storing the link in a hidden column
	*/
		// Set chart options
	    var options = {'width':600,
	                   'height':300,
					   'chartArea': { width:'80%', height: '80%' },
					   'titleTextStyle': {
							color: '333333',
							fontName: 'Arial',
							fontSize: 20
						},
						legend: 'none'
					};

	// Instantiate and draw our chart, passing in some options.
	    var chart = new google.visualization.Table(document.getElementById('chart_div'));
	    chart.draw(data, options);
		
		// Every time the table fires the "select" event, it should call your
		// selectHandler() function.
		google.visualization.events.addListener(chart, 'select', selectHandler);
		
		function selectHandler(e) {
			var selection = chart.getSelection();
			if (selection != null) {
				var item = selection[0];
				usernm = data.getValue(item.row, 0);
				window.location = data.getValue(item.row, 1);				
			}
		}
	 }
	
		
	</script>
	<!--Div that will hold the chart-->
     <div id="chart_div"></div>
 
 </body>
</html>

