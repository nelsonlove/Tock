<?php

date_default_timezone_set("America/New_York");

function customError($errno, $errstr, $errfile, $errline) {
	echo "<b>Error:</b> [$errno] $errstr (line $errline)<br>";
	echo "Ending Script";
	die();
}

function dateToDatetime($date) {
	return date( 'Y-m-d H:i:s', $date);
}

function formatTime($time) {
	return date("g:ia", strtotime($time));
}

function formatDate($date) {
	return date("F j, Y",$date);
}

function mysqldate($date) {
	return date("Y-m-d",$date);
}

function getLastMonday() {
	$date = time();
        $offset = date("N",$date) - 1; 
	return mktime(0, 0, 0, date("m",$date) , date("d",$date)-$offset, date("Y",$date));
}

function getThisSaturday($date) {
	$offset = date("N",$date) -6;
	return mktime(0, 0, 0, date("m",$date) , date("d",$date)-$offset, date("Y",$date));
}

function getTime($date) {
	$pieces = explode(" ", $date);
	return $pieces[1];
}

function eventsbydaypop($in1,$out1,$in2,$out2) {
	$array = array(
		'in1' => $in1,
		'out1' => $out1,
		'in2' => $in2,
		'out2' => $out2,
	);
	return $array;
}

set_error_handler("customError");

echo "<h1>Timeclock report: week of " . formatDate(getLastMonday(time())) . "</h1>";

$mysqli =  new mysqli('letsragedb.cbvqkpxnwcmb.us-east-1.rds.amazonaws.com', 'letsrage', 'letsrage1', 'hrdb');

if (mysqli_connect_errno()) {
	exit('Connect failed: '. mysqli_connect_error());
}

$people = "";
$daysoftheweek = array("monday","tuesday","wednesday","thursday","friday","saturday");
$i = 0;
$datesoftheweek = "";
foreach ($daysoftheweek as $day) {
	$ourdate = "";
	$datesoftheweek[$i]['name'] = $day;
	$datesoftheweek[$i]['date'] = "";
	$lastmonday = date("Y-m-d",getLastMonday());
	$ourdate = new DateTime($lastmonday);
	$ourdate->add(new DateInterval("P" . $i . "D"));
	$datesoftheweek[$i]['date'] = $ourdate->format("Y-m-d");
	$i++;
}

# echo "<br><br><br><hr>";

# print_r($datesoftheweek);

$nulltime = "12:00am - 12:00am";
$nulltext = "&nbsp;";
$natext = "&mdash;";

$query = "SELECT * FROM staff WHERE active = 1 ORDER BY NAME ASC;";
if ($result = $mysqli->query($query)) {

	/* fetch associative array */
	while ($row = $result->fetch_assoc()) {

		$times = "";
		foreach ($daysoftheweek as $day) {
			$times[$day]['in'] = formatTime($row[$day . "_in"]);
			$times[$day]['out'] = formatTime($row[$day . "_out"]);
			$times[$day]['times'] = $times[$day]['in'] . " - " . $times[$day]['out'];
			if ($times[$day]['times'] == $nulltime) { $times[$day]['times'] = "<center>" . $natext . "</center>"; }
		}

		$people[] = array(
			'name'	=> $row['name'],
			'email'	=> $row['email'],
			'phone' => $row['phone'],
			'times'	=> $times,
		);
	}
}

?><table border=1><?php

$a=0;
foreach ($people as &$person) { ?>

	<?php if ($a==1) { echo "<tr><th colspan=7>&nbsp;</th></tr>"; } ?>
	<tr>
	<th><?php echo $person['name']; ?></th>
	<th><?php echo $person['phone']; ?></th>
	<th colspan = 5 align=left><?php echo $person['email']; ?></th>
	</tr>
	<tr><th></th><?php foreach ($daysoftheweek as $day) { ?><th><?php echo ucwords($day); ?></th><?php } ?>
	</tr><tr>
	<th>Scheduled Hours</th>

	<?php
	foreach ($daysoftheweek as $day) {
		echo "<td>" . $person['times'][$day]['times'] . "</td>";
	} ?>

	<?php
	$events = "";
	$eventsbyday = "";
	foreach ($datesoftheweek as $day) {
		$rows = array();
		$query = 'SELECT * FROM timeclock WHERE DATE(TIMESTAMP) = DATE("' . $day['date'] . '") AND NAME = "' . $person['name'] . '" ORDER BY TIMESTAMP ASC;';
		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row['timestamp'];
			}
		}

		$nulltext = "&nbsp;";
		switch (count($rows)) {
			case 0:
				$eventsbyday[$day['name']] = array(
					'in1' => $nulltext,
					'out1' => $nulltext,
					'in2' => $nulltext,
					'out2' => $nulltext,
				);
				break;
			case 1:
				$eventsbyday[$day['name']] = array(
					'in1' => formatTime(getTime($rows[0])),
					'out1' => $nulltext,
					'in2' => $nulltext,
					'out2' => $nulltext,
				);
				break;
			case 2:
				$eventsbyday[$day['name']] = array(
					'in1' => formatTime(getTime($rows[0])),
					'out1' => $nulltext,
					'in2' => $nulltext,
					'out2' => formatTime(getTime($rows[1])),
				);
				break;
			case 3:
				$eventsbyday[$day['name']] = array(
					'in1' => formatTime(getTime($rows[0])),
					'out1' => formatTime(getTime($rows[1])),
					'in2' => formatTime(getTime($rows[2])),
					'out2' => $nulltext,
				);
				break;
			case 4:
				$eventsbyday[$day['name']] = array(
					'in1' => formatTime(getTime($rows[0])),
					'out1' => formatTime(getTime($rows[1])),
					'in2' => formatTime(getTime($rows[2])),
					'out2' => formatTime(getTime($rows[3])),
				);
				break;
		}
	} ?>
	<tr>
	<th>In</th>
	<?php foreach ($daysoftheweek as $day) { echo "<td>" . $eventsbyday[$day]['in1'] . "</td>"; } ?>
	</tr><tr>
	<th>Out to Lunch</th>
	<?php foreach ($daysoftheweek as $day) { echo "<td>" . $eventsbyday[$day]['out1'] . "</td>"; } ?>
	</tr><tr>
	<th>Back from Lunch</th>
	<?php foreach ($daysoftheweek as $day) { echo "<td>" . $eventsbyday[$day]['in2'] . "</td>"; } ?>
	</tr><tr>
	<th>Out</th>
	<?php foreach ($daysoftheweek as $day) { echo "<td>" . $eventsbyday[$day]['out2'] . "</td>"; } ?>
	</tr>
<?php $a=1; } ?>
</table>
