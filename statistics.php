<html lang="en">
<head>
  <title>CSLC Question Statistics</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php
include_once('util.php');

$query = "select count(*) from questions where utc > (UNIX_TIMESTAMP() - 86400);";
$result = $conn->query($query);

$row = $result->fetch_row();
$daycount = $row[0];

$query = "select count(*) from questions where utc > (UNIX_TIMESTAMP() - (86400 * 2)) and utc < UNIX_TIMESTAMP() - 86400;";
$result = $conn->query($query);

$row = $result->fetch_row();
$yesterdaycount = $row[0];

$query = "select count(*) from questions where utc > (UNIX_TIMESTAMP() - (86400 * 7));";
$result = $conn->query($query);

$row = $result->fetch_row();
$weekcount = $row[0];

$query = "select count(*) from questions where utc > (UNIX_TIMESTAMP() - (86400 * 14)) and utc < UNIX_TIMESTAMP() - (86400 * 7);";
$result = $conn->query($query);

$row = $result->fetch_row();
$yesterweekcount = $row[0];

$query = "select count(*) from questions where utc > (UNIX_TIMESTAMP() - 2592000);";
$result = $conn->query($query);

$row = $result->fetch_row();
$monthcount = $row[0];

$query = "select count(*) from questions where utc > (UNIX_TIMESTAMP() - (2592000 * 2)) and utc < UNIX_TIMESTAMP() - 2592000;";
$result = $conn->query($query);

$row = $result->fetch_row();
$yestermonthcount = $row[0];
?>
<body>
	<div class="container">
		<div class="col-12">
			<div class="row">
				<h1>Statistics</h1>
				<hr />
			</div>
			<h4>Questions today:&nbsp<?=$daycount?></h4>
			<h4><span style="display:inline-block; width: 15px;"></span>Versus yesterday:&nbsp<?=$yesterdaycount?></h4>
			<h4>Questions this week:&nbsp<?=$weekcount?></h4>
							
			<h4><span style="display:inline-block; width: 15px;"></span>Versus last week:&nbsp<?=$yesterweekcount?></h4>
			<h4>Questions this month:&nbsp<?=$monthcount?></h4>
			
			<h4><span style="display:inline-block; width: 15px;"></span>Versus last month:&nbsp<?=$yestermonthcount?></h4>
		</div>
	</div>
</body>
