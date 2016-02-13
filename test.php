<?php 
require_once ("head.php");
?>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
		google.load('visualization', '1.0', {'packages':['table']});
		
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

	  // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {
		// Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Username');
		data.addColumn('string', 'UserStatus');
		var rawData = [["Monica", "Undercover"], 
			["Baby Sissy", "Ultranaughty"]];
		var data = google.visualization.arrayToDataTable(rawData);
		
		// Set chart options
        var options = {'title':'Posts per user this week',
                       'width':600,
                       'height':300,
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
		
     }
	
    </script>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
</body>
</html>
