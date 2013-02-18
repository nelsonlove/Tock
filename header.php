<?php

include("config.php");
include("functions.php");

# Connect to MySQL database

$db = new xmysqli($config['db_hostname'], $config['db_username'], $config['db_password'], $config['db_database']);
if (mysqli_connect_errno()) {
	exit('Connect failed: '. mysqli_connect_error());
}
	
# Set timezones
date_default_timezone_set($config['timezone']);
$db->query('SET time_zone = "' . $config['timezone'] .'";');

?>