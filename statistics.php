<?php 
 require_once ("head.php");
 ?>
	<div width="600" style="padding-left:180px">Posts per user this <select id="timeframeselect" class="copy" onChange="javascript:drawChart()">
		<option value="1">Day</option>
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
 
	  // Load the Visualization API and the barchart package.
	  google.load('visualization', '1.0', {'packages':['corechart']});
	  // Set a callback to run when the Google Visualization API is loaded.
	  google.setOnLoadCallback(drawChart);

	  function errorhandler(jqXHR, textStatus, errorThrown) {
		$("div#error_message p").text("Error building chart: " + textStatus);
	  }
	  
	  // Callback that creates and populates a data table,
	  // instantiates the bar chart, passes in the data and
	  // draws it.
	  function drawChart() {

		var selector = document.getElementById("timeframeselect");
		var timeframevalue = selector.value;
		var timeframename = selector.options[selector.selectedIndex].text;
		
		var jsonData = $.ajax({
          url: "getstatistics.php?timeframe=" + timeframevalue,
          dataType:"json",
		  error:errorhandler, 
          async: false
          }).responseText;

        var data = new google.visualization.DataTable(jsonData);

		var view = new google.visualization.DataView(data);
		view.setColumns([0, 2]); //because we are storing the link in a hidden column

		// Set chart options
	    var options = {'width':600,
	                   'height':300,
					   'vAxis.viewWindowMode':'maximized',
					   'titleTextStyle': {
							color: '333333',
							fontName: 'Arial',
							fontSize: 20
						},
						legend: 'none'
					};

	// Instantiate and draw our chart, passing in some options.
	    var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
	    chart.draw(view, options);
		
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
	<div id="error_message"><p></p></div>
 </body>
</html>
