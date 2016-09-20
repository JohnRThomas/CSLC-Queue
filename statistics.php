Statistics Generator (BETA)<br>
Work in progress!<br>
<br>

The date/time format is very flexible.  Here's some examples of what you can enter:<br>
Now<br>
-2 Days<br>
11/5/2015<br>
<form action="" method="get">
From: <input type='text' name='start' value="today"><br>
until: <input type='text' name='end' value="now"><br>
<input type='submit'>
</form>

<?php
	include_once "util.php";
	
	if (get("start") && get("end")) {
		//Turn the dates entered into real utc times
		$start = strtotime(get("start"));
		$end = strtotime(get("end"));
		
		//Make sure times entered are valid
		
		$goodTimes = true;
		if (!$start) {
			echo "Invalid start time entered: " . get("start") . "<br>";
			$goodTimes = false;
		}

		if (!$end) {
			echo "Invalid end time entered: " . get("end") . "<br>";
			$goodTimes = false;
		}
		
		
		if ($goodTimes) {
			//Flip dates if people enter backwards
			if ($end < $start) {
				$temp = $end;
				$end = $start;
				$start = $temp;
			}
			
			generateReport($start,$end);
		}
	}
	function generateReport($start,$end) {
		global $conn;
	
		$result = $conn->query("SELECT DISTINCT class FROM questions ORDER BY class ASC");
		while ($row = $result->fetch_array()) {
			$count = $conn->query("SELECT COUNT(*) FROM questions WHERE class='$row[0]' AND utc BETWEEN $start AND $end");
			if ($countrow = $count->fetch_array()) {
				echo "$row[0] - $countrow[0]<br>";
			}
		}
		
		
		$result = $conn->query("SELECT question FROM questions");
		while ($row = $result->fetch_array()) {
			echo str_replace("@","","$row[0] ");
		}
	}
?>
