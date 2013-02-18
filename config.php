<?php
	
# Login information for MySQL connection
$config = array(
	'db_hostname' => 'letsragedb.cbvqkpxnwcmb.us-east-1.rds.amazonaws.com',
	'db_username' => 'letsrage',
	'db_password' => 'letsrage1',
	'db_database' => 'hrdb',
	'timezone' => 'US/Eastern',
);

# Set the IPs from which people can clock in/out
$config['db_allowed_ips'] = array(
	'108.20.201.161',
	'198.228.205.52',
);

?>