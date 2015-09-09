<!DOCTYPE html>
<html lang="en">
<head>
  <title>Mitch's CSLC Queue</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</head>
<body style='color: #fff; font-size: 72px; background-color: #aa0000'>
<center>
<?php
    //Get the IP
    $file = fopen("http://127.0.0.1/myip.php",'r');
    $ip = "http://" . fgets($file);
?>


To ask a question<br>
go to<br>
<div class='btn-round' style='background-color: #000'>
<?php echo $ip ?></div>
in your browser!

<center>
</body>