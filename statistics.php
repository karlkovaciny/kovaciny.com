<?php 
require_once ("head.php");
?>
	<div width="600">Posts per user this <select id="timeframeselect" class="copy" onChange="javascript:drawChart()">
		<option value="7" selected>Week</option>
		<option value="30">Month</option>
		<option value="365">Year</option>
		<option value="100000">All time</option>
	</select>

	</div>
    <!--Div that will hold the chart-->
    <div id="chart_div"></div>


</body>
</html>
