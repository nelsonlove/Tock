<body bgcolor="#aaaaaa">
<?php

$remote_ip = "108.20.201.161";
$test_ip = "198.228.205.52";
date_default_timezone_set("America/New_York");

function customError($errno, $errstr, $errfile, $errline) {
	echo "Error $errno: $errstr at line $errline";
	die();
}
set_error_handler("customError");

function currTime() {
	$t = time();
	return date("g:ia",$t);
}

function currDate() {
	$t = time();
	return date("F j, Y",$t);
}

function timestamp() {
	$t = time();
	return date("Y-m-d H:i:s",$t);
}

$mysqli = new mysqli('letsragedb.cbvqkpxnwcmb.us-east-1.rds.amazonaws.com', 'letsrage', 'letsrage1', 'hrdb');
if (mysqli_connect_errno()) {
	exit('Connect failed: '. mysqli_connect_error());
}

$query = 'SET time_zone = "US/Pacific";';
if (!$mysqli->query($query)) { die('Error: ' . mysql_error()); }

$display_form = 1;
$errmsg = 0;
$statusmsg = 0; ?>

<center>
<div style="border: 4px double black; background-color: white; padding: 20; margin: 30; display: inline-block;">
<img src="logo.gif" width=191 height=54><br><br>
<div style="font-size: smaller; margin: 0;"><?php echo currDate() . " / " . currTime(); ?></div><br>

<?php
if (isset($_POST['submit'])) {

	if (isset($_POST['name'])) { $name = $_POST['name']; } else { $name = ""; }
	if (isset($_POST['event'])) { $event = $_POST['event']; } else { $event = ""; }
	if (isset($_POST['password'])) { $password = $_POST['password']; } else { $password = ""; }

	if (!$event or !$name or !$password) { $errmsg = 1; }
	if ($_SERVER['REMOTE_ADDR'] != $remote_ip and $_SERVER['REMOTE_ADDR'] != $test_ip) { $errmsg = 2; }

	$query = 'SELECT password FROM staff WHERE name = "' . $name . '" AND password = "' . $password . '";';
	if ($result = $mysqli->query($query)) {
		if ($result->num_rows == 0) { $errmsg = 3; }
	}

	$query = 'SELECT * FROM timeclock WHERE DATE(TIMESTAMP) = DATE(NOW()) AND NAME = "' . $name . '";';
	$numRows = 0;
	if ($result = $mysqli->query($query)) {
		if ($result->num_rows >= 4) { $errmsg = 4; }
	}

	$query = 'SELECT * FROM timeclock WHERE DATE(timestamp) = DATE(NOW()) AND NAME = "' . $name . '" ORDER BY timestamp DESC LIMIT 1;';
		if ($result = $mysqli->query($query)) {
		while ($row = $result->fetch_assoc() ) {
			if ($row['type'] == $event) { $errmsg = 5; }
		}
	}

	$query = 'SELECT * FROM timeclock WHERE DATE(TIMESTAMP) = DATE(NOW()) AND NAME = "' . $name . '";';
	$numRows = 0;
	if ($result = $mysqli->query($query)) {
		if ($result->num_rows == 0) {
			if ($event == "out") { $errmsg = 6; }
		}
	}

	$result->close();

	# what to do if there are no error messages
	if (!$errmsg) {
		$query = 'INSERT INTO timeclock (name, type, timestamp) VALUES ("' . $name . '", "' . $event . '", "' . timestamp() . '");';

		if (!$mysqli->query($query)) {
			die('Error: ' . mysql_error());
		} else {
			$statusmsg = "Thanks " . $_POST['name'] . ", you have been clocked " . $_POST['event'] . " at " . currTime() . ".";
		}

	#error handlers
	} elseif ($errmsg == 1) {
		$errmsg = "Please make sure you have completed the form.";
	} elseif ($errmsg == 2) {
		$errmsg = "Wrong location! Make sure you are currently on the LET'S RAGE Wi-Fi network.";
	} elseif ($errmsg == 3 ) {
		$errmsg = "Wrong password! Please retype your password.";
	} elseif ($errmsg == 4) {
		$errmsg = "You have already clocked in and out twice today.";
	} elseif ($errmsg == 5) {
		$errmsg = "You have already clocked " . $event . ".";
	} elseif ($errmsg == 6) {
		$errmsg = "You have not yet clocked in today.";
	}
}

# display the rest
if ($errmsg) { echo '<font color="red">' . $errmsg . "</font><br><br>"; }
if ($statusmsg) { echo $statusmsg . "<br><br>"; }
if ($display_form = 1) { ?>
	<table>
	<form name="form" action="timeclock.php" method="post">
		<tr><td><label>Name:</label></td><td>
		<select id="name" name="name">
		<option value=""></option>
		<?php $query = "SELECT * FROM staff WHERE active = 1 ORDER BY name ASC;";
		if ($result = $mysqli->query($query)) {
			/* fetch associative array */
			while ($row = $result->fetch_assoc()) {
				$name = $row["name"];
				echo '<option value="' . $name . '">' . $name . "</option>";
			}
		}
		/* free result set */
		$result->free();
		?>
	
		</select></td></tr>
		<tr><td><label>Password:</label></td><td>
		<input type="password" name="password" id="password" size="14">
		</td></tr><tr><td>
		<label>Choose one:</label></td><td>
		<label for="in"> <input type="radio" name="event" value="in" id="in" /> In</label>
		<label for="=out"> <input type="radio" name="event" value="out" id="out" /> Out</label>
		</td></tr></table><br>
		<input type="submit" name="submit">
	</form><br>
<?php }
$mysqli->close();
?>
</div>
</body>
