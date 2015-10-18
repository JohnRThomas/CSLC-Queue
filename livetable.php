<?php
	include_once "util.php";

	
	
	/***************************************
	* List Unanswered questions            *
	***************************************/
	$result = $conn->query("SELECT * FROM questions WHERE (answered='0' OR answered IS NULL) ORDER BY utc ASC");
    $next = $conn->query("SELECT uid FROM questions WHERE (answered='0' OR answered IS NULL) ORDER BY utc ASC LIMIT 1")->fetch_row()[0];

    if ($next != null) {
    	$next = getNameAndNumber($next);
    }

    if ($next == null) { $next = "No Questions!"; }
    
    if ($admin) {
        echo "<div style='border-radius: 5px' class='bg-success text-success text-center'><h1>Next: <span class='badge'><h1>&nbsp;&nbsp;$next&nbsp;&nbsp;</h1></span></h1></div>";
    }

	echo "
	<div class='bg-info'><h3>Virtual Waiting Line</h3></div>";

    

    echo "
	<table class='table table-striped table-hover'>
		<thead>
			<tr>
				<th>#</th>
				<th>Age</th>
				<th>Class</th>
				<th>Question</th>
			</tr>
		</thead>
		<tbody>";

	$id = 0;
	$time = time();
	$firstIP = "";
	$sound = false;
	while($row = $result->fetch_assoc()){
		$id++;
		if ($id == 1) { $firstIP = $row['ip']; }
		$seen = intval($row['seen']);
		$timeDifference = time() - $row['utc'];
		$minutes = floor($timeDifference / 60);
		$seconds = $timeDifference % 60;
		$unansweredTime = $minutes . "m " . $seconds . "s";

		$canDelete = (isset($_SESSION['uid']) && $_SESSION['uid'] == $row['uid']);
		$class = ($canDelete) ? "danger" : "";
		
		//Admins and question owners can answer questions
		$answer = ($admin || $canDelete) ? "<a class='btn btn-danger btn-xs' onclick= 'function() { $('#livetable').load('livetable.php'); }' href='?answer=$row[id]'><span class='glyphicon glyphicon-trash'></span></a>" : "";

        $question = $row['question'];
        //Find antrhing like @Mitch and format it

        $start = 0;

        while ($start > -1) {
	        $index = strpos($question,"@",$start);
	        if (!($index === false)) {
	            //Find the next space
	            $end = strpos($question," ", $index);
	            if ($end === false) { $end = strlen($question); }
	            $start = $end;	//To make sure we dont get stuck in an infinite loop
	            $len = $end-$index;


	            //Replace this with the cool thing
	            $at = substr($question,$index,$len);
	            $at = "<kbd>$at</kbd>";
	            $question = substr($question,0,$index) . $at . substr($question,$end);
	        } else {
	        	$start = -1;	//Stop
	        }
	    }


        /**EASTER EGGS!**/
        //Green
        if (!(strpos($question,"<kbd>@green</kbd>",0) === false)) {
            $question = "<b>" . str_replace("<kbd>@green</kbd>","",$question) . "</b>";
            $class = "text-success success";
            $row['class'] = "EasterEgg";
        }

        //Yellow
        if (!(strpos($question,"<kbd>@yellow</kbd>",0) === false)) {
            $question = "<b>" . str_replace("<kbd>@yellow</kbd>","",$question) . "</b>";
            $class = "text-warning warning";
            $row['class'] = "EasterEgg";
        }

        //Blue
        if (!(strpos($question,"<kbd>@blue</kbd>",0) === false)) {
            $question = "<b>" . str_replace("<kbd>@blue</kbd>","",$question) . "</b>";
            $class = "text-info info";
            $row['class'] = "EasterEgg";
        }

        //Rainbow
        if (!(strpos($question,"<kbd>@rainbow</kbd>",0) === false)) {
            $question = str_replace("<kbd>@rainbow</kbd>","",$question);
            $dest = "";
            $colors = array("#f00","#f80","#ff0","#0f0","#00f", "#f0f");
            for($i=0; $i<strlen($question); $i++) {
                $color = $colors[$i%count($colors)];
                $dest .= "<font color='$color'>$question[$i]</font>";
            }
            $question = "<kbd><b>" . $dest . "</b><kbd>";
            $row['class'] = "EasterEgg";
        }
        
        //@Mitch
        if (!(strpos($question,"<kbd>@mitch</kbd>",0) === false) || !(strpos($question,"<kbd>@Mitch</kbd>",0) === false)) {
        	$new_tag = "@DO MY CODE!";
            $question = str_replace("@mitch",$new_tag,$question);
            $question = str_replace("@Mitch",$new_tag,$question);

        }
		
		//Code
        if (!(strpos($question,"@code",0) === false)) {
			$question = str_replace("<kbd>@code</kbd>","",$question);
			$question = str_replace("\n","<br>",$question);
			$question = str_replace(" ","&nbsp;",$question);
			$question = "<font style='font-family: \"Lucida Console\", Monaco, monospace'>" . $question . "</font>";
			
			if($answer == "") {$question ="<b><u>Code hidden from other students</u></b>";}
			else {
				$question = "<b><u>Note: This code is censored from other students</u></b><br>" . $question;
			}
        }
				
		//Public Code
        if (!(strpos($question,"@publiccode",0) === false)) {
			$question = str_replace("<kbd>@publiccode</kbd>","",$question);
			$question = str_replace("\n","<br>",$question);
			$question = str_replace(" ","&nbsp;",$question);
			$question = "<font style='font-family: \"Lucida Console\", Monaco, monospace'>" . $question . "</font>";
        }
		
		//Allow escapes to disappear! (change \\ to nothing)
        if (!(strpos($question,"\\",0) === false)) {
			$question = str_replace("\\","",$question);
        }
		
		
		if(!$seen && $admin && !$sound){
			$conn->query("UPDATE questions SET seen=1;");
        	$sound = true;
        	$dw = intval(date("w"));
        	$hr = intval((date("G") + 6) % 7);
        	
        	//This is a setting! (plays 0.wav - 14.wav)
        	$NUMSOUNDS = 15;
        	
        	$soundID = $row['uid'] % $NUMSOUNDS;
        	echo "<audio autoplay><source src='audio/" . $soundID . ".wav'></audio>";
			//echo "<audio autoplay><source src='new_question.wav'></audio>";
			
			//echo "<audio autoplay><source src='lilbits.wav'></audio>";
		}
		
	    echo "
	    <tr class='$class'>
	    	<td>$answer $row[uid]</td>
	    	<td>$unansweredTime</td>
	    	<td>$row[class]</td>
	    	<td> <div>$question</div></td>
	    </tr>";
	}
	echo "</tbody></table>";


	//Map the IPs
	//row 0 col 0 is near the window, closest to the whiteboard(left)
	function getIP($row,$col) {
		$map = array(
			array("1", "2"),
			array("3", "4"),
			array("5", "6"),
			array("7", "8"),
			array("9", "10")
		);

		if ($row >= sizeof($map)) { return ""; }
		if ($col >= sizeof($map[$row])) { return ""; }

		return $map[$row][$col];
	}

?>
