<?php
    
	//Set this to true to set up database
	$INITIALIZE_DB = FALSE;

	$DBServer = "127.0.0.1";
	$DBUser = "root";
	$DBPass = "iloveprogramming";
	$DBName = "cslc";

	function clean($str) { 
		$str = strip_tags(addslashes(stripslashes(htmlspecialchars($str)))); 
		return $str; 
	}  

	function post($v) {
		if (isset($_POST[$v])) {
			return clean($_POST[$v]);
		}
		return false;
	}

	function get($v) {
		if (isset($_GET[$v])) {
			return clean($_GET[$v]);
		}
		return false;
	}

	session_start();
	$admin = false;
	if (isset($_SESSION['admin'])) {
		$admin = true;
	}

	if (isset($_GET['ilove'])) {
		if ($_GET['ilove'] == 'programming') {
			$_SESSION['admin'] = 'You Betcha';
		} else {
			unset($_SESSION['admin']);
			unset($_SESSION['uid']);
		}
		header("location: /");
	}




	/******************************
	*   DATABASE INITIALIZATION   *
	******************************/
	if ($INITIALIZE_DB) {
		echo "<div class='text-warning bg-warning'>Initializing databse.  If this is unintended, set the INITIALIZE_DB variable to FALSE in util.php</div>";

		//Create a connection and create database if necessary
		$conn = new mysqli($DBServer, $DBUser, $DBPass);
		if ($conn == null) {
			echo "<div class='bg-danger text-danger'>Could not connect to the database with the provided cridentials!</div>";
		}

		//Create database
		$query = "CREATE DATABASE $DBName; ";
		echo "<div class='bg-info text-info'>Executing query: $query</div>";
		$conn->query($query);

		//Create questions table
		$query = "CREATE TABLE $DBName.questions (
					id INT(11) AUTO_INCREMENT PRIMARY KEY,
					utc INT(11),
					name TEXT,
					class TEXT,
					question TEXT,
					uid INT(11),
					answered INT(11),
					ip TEXT,
					token INT(11),
					seen TINY_INT(1)
				);";
		echo "<div class='bg-info text-info'>Executing query: $query</div>";
		$conn->query($query);

		//Create user table
		$query = "CREATE TABLE $DBName.user (
					id INT(11) AUTO_INCREMENT PRIMARY KEY,
					uid INT(11),
					utc INT(11),
					name TEXT,
					room TEXT
				);";
		echo "<div class='bg-info text-info'>Executing query: $query</div>";
		$conn->query($query);

		echo "<div class='bg-success text-success'>Database initialization complete.  Be sure to set INITIALIZE_DB to FALSE in util.php</div>";
	}


	//Connect to server for realz now
	$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
	$success = $conn->select_db($DBName);

	if (!$success) {
		echo "<div class='text-warning bg-warning'>Error connecting to database.  If database has not been created, try setting INITIALIZE_DB to TRUE in util.php</div>";
		//exit();
	}

	function getName($uid) {
		global $conn;
		$result = $conn->query("SELECT name FROM user WHERE uid=$uid ORDER BY utc DESC LIMIT 1");
		if ($result) {
			$row = $result->fetch_row();
			return $row[0];
		}

		return "";
	}

	function getNameAndNumber($uid) {
		$number = $uid;

		$name = getName($uid);
		if ($name != "") {
			$number = $name . " [$number]";
		}

		return $number;
	}

	function getRoom($uid) {
		return "Functionality not implemented yet";
	}


?>
