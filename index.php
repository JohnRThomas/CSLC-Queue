<!DOCTYPE html>
<!--

A Here's a small list of easter eggs!
Add the following to your question to play a sound!
	@hey, @haha, @starshipenterprise, @rickroll,
	@hestrying, @gong, @moo, @maa, @fanfare,
	@mariojump, @yum, @kimpossible
	
	There's more!  You have to be a good programmer to find them!
-->

<html lang="en">
<head>
  <title>CSLC Question Queue. Sponsored by Epic Systems!</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<script>

	//Validate asking a question
	function validate() {
		var selectedClass = $("#classSelect").val();
	
		// Only ask a question when the LC is open
		if (selectedClass.length == 0) {
			$("#questionError").html("No class selected");
			$("#questionError").show(500);
			return false;
		}

		if ($("#question-text").val() == '') {
			$("#questionError").html("Please type in a question!");
			$("#questionError").show(500);
			return false;
		}
		
		return true;
	}
</script>

<body>
<div id='pop' hidden=true></div>

<div class="container">
			<?php
				include_once "util.php";
				if(post('question')){
				?>
				<script>
					function popUp(){
						<?php echo "var question = ' ". post('question') . "'.trim().replace(' ','+');";?>
						$('#pop').html("Thanks!  Wait for us to call your number!");
						$('#pop').dialog({
							autoOpen: false,
							width: 'auto',
  							buttons: [
    						{
      							text: "OK",
      							click: function() {$( this ).dialog( "close" );}
							}
						  	]
						});
						$(".ui-dialog-titlebar").hide();
						$(".ui-dialog-buttonset").find("button").addClass("btn btn-success")
						$('#pop').dialog('open');
						$(".ui-dialog-buttonset").find("button").focus();
					}
					
					/*function playSound() {
						var sound = document.getElementById("dingding");
						sound.Play();
					}*/
					
					$(document).ready(function(){popUp();});
				</script>
				<?php
				}
				/***************************************
				* Set Optional Info                    *
				***************************************/
				if (post('optional') && isset($_SESSION['uid'])) {
					$name = post('name');
					$room = post('room');
					$uid = $_SESSION['uid'];
					$utc = time();

					//Make sure they set SOMETHING
					if ($name || $room) {
						$query = "INSERT INTO user (uid,name,room,utc) VALUES ($uid,'$name','$room',$utc)";
						$result = $conn->query($query);
					}
				}





				/***************************************
				* Ask a Question                       *
				***************************************/
				if (post('class') && post('question') && post('time')) {
					$class = post('class');
					$question = post('question');
					$utc = time();
					$token = post('time');
					$name="Mitch";

					//Get a unique user id
					if (!isset($_SESSION['uid'])) {
						//We need a new UID!
						$r = $conn->query("SELECT uid FROM questions ORDER BY uid DESC LIMIT 1");
						$row = $r->fetch_row();
						$uid = $row[0] + 1;
						$_SESSION['uid'] = $uid;
					}
					$uid = $_SESSION['uid'];

					//First, make sure they didnt ask this EXACT question
					$result = $conn->query("SELECT * FROM questions WHERE uid=$uid AND token=$token LIMIT 1");
					if ($result->num_rows > 0) {
						//Already posted
						echo "<div class='bg-danger text-danger'><h4>Question already asked.  Wait your turn.</h4></div>";
					} else {
						$ip = $_SERVER['REMOTE_ADDR'];
						if ($ip == "141.219.158.123") { exit(); }
						$browserinfo = $_SERVER['HTTP_USER_AGENT'];
						
						//if ($browserinfo == "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36") { exit(); }
						
						//Step 1: store info about the browser into the browser table
						$q = "INSERT INTO browser (browser) VALUES('$browserinfo')";
						$result = $conn->query($q);
						
						//Step 2: ask the Nquestions
						$q = "INSERT INTO questions (uid,utc,name,class,question,token,ip) VALUES($uid, $utc,'$name','$class','$question',$token,'$ip')";
						//echo $q . "<br>";
						$result = $conn->query($q);
						
						
					}
				}






				/***************************************
				* "Delete" an answered question        *
				***************************************/
				if (isset($_GET['answer'])) {
					$addedClause = "";
					if (!$admin) {
						//Only delete where uid
						$addedClause = "AND uid=$_SESSION[uid]";
					}
					$time = time();
					$id = $_GET['answer'];
					$conn->query("UPDATE questions SET answered=$time WHERE (answered=0 OR answered IS NULL) AND id=$id $addedClause");
				}

				/***************************************
				* Print the UID                        *
				***************************************/
				$number = "You haven't asked anything";
				if (isset($_SESSION['uid'])) {
					$number = "You are number " . ((intval(getNameAndNumber($_SESSION['uid'])) % 100) + 1);//(intval() % 100) + 1;

					//Did they vote?
					//$result = $conn->query("SELECT COUNT(*) FROM votes WHERE uid=0");
					//$row = $result->fetchRow();
					//echo "VOTES: " . $row[0];

				}


				if ($admin) {
                    //Get the IP
                    $url = "<br>$_SERVER[HTTP_HOST]<br><small>(open url in your browser)</small>";

					$number = "You are ADMIN <a href='?ilove=llamas' class='btn btn-primary'>Logout <span class='glyphicon glyphicon-log-out'></span></a>
                                <a href='instructions.php' class='btn btn-warning' target='_blank'>Instructions</a>";
				}else $url = "";
				
				echo "<div class='col-xs-12' style='background-color: #444; color:#fff; border-radius: 0px 0px 5px 5px'><h1>CSLC Question Queue
                        $url
						<br><small>$number</small></h1></div><br>";


				//begin the row
				echo "<div class='row'>";


				/***************************************
				* Set optional Info                    *
				***************************************/
				if (!$admin && isset($_SESSION['uid'])) {
					echo "
					<div class='col-xs-12'>
						<h4>Tell us more! <small>(Optional)</small></h4>
						<form name='additional' method='post' action='' role='form'>
							<div class='form-group'>
								<input type='hidden' name='optional' value='true'>
								<div class='col-sm-6'>
									<label for='name'>Nickname</label>
									<input name='name' type='text' class='form-control' value='" . getName($_SESSION['uid']) . "'>
								</div>
								<div class='col-sm-6'>
									<label for='room'>Room</label>
									<input name='room' type='text' class='form-control' value='" . getRoom($_SESSION['uid']) . "'>
								</div>
								<div class='col-sm-2'>
									<label for='room'></label>
									<input name='submit' type='submit' class='form-control btn-success btn' value='Save'>
								</div>
							</div>
						</form>
					</div>";
				}


				/***************************************
				* Ask Question form                    *
				***************************************/
				if (!$admin) {					

					echo "
					<div class='col-xs-12 col-sm-4'>						
						<form name='ask' method='post' action='' role='form' onSubmit='return validate()'>
							<input type='hidden' name='time' value='" . time() . "'>
							<input type='hidden' name='browser' value=''>
							<div class='bg-info'><h3>Virtually raise your hand!</h3></div>
							<div class='form-group'>
								<label for='class'>Class</label></td>
								<select name='class' id='classSelect' class='form-control'>
									<option value=''>Select Class...</option>
									<option value='CNSA'>CS1111 - C for CNSA</option>
									<option value='Intro I'>CS1121 - Intro To Prog. I</option>
									<option value='Intro II'>CS1122 - Intro To Prog. II</option>
									<option value='Accel Intro'>CS1131 - Accelerated Intro to Prog.</option>
									<option value='C'>CS1141 - C For Java Programmers</option>
									<option value='Discrete'>CS2311 - Discrete Structures</option>
									<option value='Data Structures'>CS2321 - Data Structures</option>
									<option value='OTHER'>Other</option>
								<select>
							</div>
							<div class='form-group'>
								<label for='question'>Question</label>
								<textarea id='question-text' class='form-control' name='question' placeholder='Please write a detailed question.'></textarea>
								Looking for some fun?  Try adding @haha or @hey to your question! See more fun things by reading the source code of this page!
							</div>
							<div class='col-xs-12 btn-danger' style='display:none' id='questionError'></div>
							<input type='submit' class='btn btn-success form-control' value='Ask Question!'>
						</form>
					</div>
					
					<script>
						document.ask.browser.value = window.screen.availWidth + ' ' + window.screen.availHeight + ' ' + window.navigator.platform + ' ' + window.navigator.userAgent;
					</script>";
				}	

				//if there is a question form, then we need to adjust the size of the column
				//accordingly.  If not, make this full screen
				$classSize = "col-xs-12 col-sm-8";
				if ($admin) {
					$classSize = "col-xs-12";
				}

				echo "
				<div id='livetable' class='$classSize'>";
					//Auto update stuffs here
					include "livetable.php";
				echo "</div>";
				
				echo "<div id='livetabletest'></div>";
				
			?>
	</div>
	<div class='col-xs-12'>
		Question Queue by <a href='http://imgur.com/jdWBDAW'>Mitch Davis</a> and <a href='https://github.com/JohnRThomas/'>John Thomas</a> 
	</div>
	<div class='col-xs-12'>
		<a href="statistics.php">Statistics</a>
	</div>
	<div class='col-xs-12'>
		<a href="http://cslc.mtu.edu/input4.txt">CS1122 Prog 4 test input</a>
	</div>
	<div class='col-xs-12'>
		<a href="MazeRunner.jar">Maze Runner (Dijkstra's Visualizer)</a>
		<a href='#' onClick="$('#mazediv').show(1000)">(How to run Maze Runner)</a>
		<div style='display: none' id="mazediv">
			If you are on windows, download MazeRunner.jar and double click it to run it<br>
			Linux is weird.  You can't double click to run, so you have to open a terminal and type:<br>
			java -jar MazeRunner.jar<br>
			<br>
			Please note that you must CD into the same directory as the jar before you can run it
		</div>
	</div>
</div>

<?php
	//Update the page every 5 seconds for 3.5 hours
	//This means we can refresh 2520 times
	$reloads = get('r');
	$reloads = ($reloads) ? $reloads+1 : 1;

	if ($reloads < 2520) {
		$updatePics = "";
		if ($admin) {
			$updatePics = "setInterval(function() { $('#livepics').load('livetable.php'); }, 1000 );";
		}

		echo "
		<script>
			var mostRecent = 0;	//The most recent ID we have loaded
			function reload() {
			
				if (disableRefresh) { return; }
				//First load the page that tells us the last ID
				//$('#livetabletest').load('livetable.php?lastupdateonly=1');
				$('#livetable').load('livetable.php');
				//If this ID is more recent than ours, we will update!
				//if ($('#livetabletest').val() - mostRecent > 0) {
				//	$('#livetable').load('livetable.php');
				//	mostRecent = $('#livetabletest').val() - 0;
				//}
			}
		
			setInterval(function() { reload(); }, 1000 );
			$updatePics
		</script>
		";
	} else {
		echo "Auto Refresh turned off.  Click <a href='/'>HERE</a> to turn back on.";
	}
?>
</body>
