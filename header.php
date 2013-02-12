# Login information for MySQL connection

$mysql_info = array(
	'hostname' => 'letsragedb.cbvqkpxnwcmb.us-east-1.rds.amazonaws.com',
	'username' => 'letsrage',
	'password' => 'letsrage1',
	'database' => 'hrdb',
);

# Set the IPs from which people can clock in/out

$allowed_ips = array(
	'108.20.201.161',
	'198.228.205.52'
);



function currTime() {
	$t = time();
	return date("g:ia",$t);
}

function currDate() {
	$t = time();
	return date("F j, Y",$t);
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

function timestamp() {
	$t = time();
	return date("Y-m-d H:i:s",$t);
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

function doQuery($query) {
	global $mysqli;
	$rows = array();
	if ($result = $db->query($query)) {
		# fetch associative array
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
	}
	return $rows;
}

function getTime($date) {
	$pieces = explode(" ", $date);
	return $pieces[1];
}

# Create MySQL object
$mysqli =  new mysqli($mysql_info['hostname'], $mysql_info['username'], $mysql_info['password'], $mysql_info['database']);
if (mysqli_connect_errno()) {
	exit('Connect failed: '. mysqli_connect_error());
}

# Set timezone in PHP and MySQL
date_default_timezone_set("America/New_York");
doQuery('SET time_zone = "US/Eastern";');