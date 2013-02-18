<?php

function __autoload($classname) {
	$filename = "./class.". $classname .".php";
	include_once($filename);
}

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

function customError($errno, $errstr, $errfile, $errline) {
	echo "<b>Error:</b> [$errno] $errstr (line $errline)<br>";
	echo "Ending Script";
	die();
}
set_error_handler("customError");

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


function getWeek($date) {
	return date('W', $date); 
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

function popEventArray($rows,$nulltext) {
	switch (count($rows)) {
		case 0:
			$events = array(
				'in1' => $nulltext,
				'out1' => $nulltext,
				'in2' => $nulltext,
				'out2' => $nulltext,
			);
			break;
		case 1:
			$events = array(
				'in1' => strtotime($rows[0]['timestamp']),
				'out1' => $nulltext,
				'in2' => $nulltext,
				'out2' => $nulltext,
			);
			break;
		case 2:
			$events = array(
				'in1' => strtotime($rows[0]['timestamp']),
				'out1' => $nulltext,
				'in2' => $nulltext,
				'out2' => strtotime($rows[1]['timestamp']),
			);
			break;
		case 3:
			$events = array(
				'in1' => strtotime($rows[0]['timestamp']),
				'out1' => strtotime($rows[1]['timestamp']),
				'in2' => strtotime($rows[2]['timestamp']),
				'out2' => $nulltext,
			);
			break;
		case 4:
			$events = array(
				'in1' => strtotime($rows[0]['timestamp']),
				'out1' => strtotime($rows[1]['timestamp']),
				'in2' => strtotime($rows[2]['timestamp']),
				'out2' => strtotime($rows[3]['timestamp']),
			);
			break;
	}
	return $events;
}

class timestamp {
	function __construct($event) {
		$datetime = $event['timestamp'];
		$pieces = explode(" ", $datetime);
		$this->date = $pieces[0];
		$this->time = $pieces[1];
		$this->unixdate = strtotime($datetime);
		$this->week_number = date("W",strtotime($datetime));
		$this->year = date("Y",strtotime($datetime));	
	}
}

?>