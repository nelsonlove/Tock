<?php

// Data scrubbed since this is on GitHub for demo purposes

// Login information for MySQL connection
$config = array(
	'db_hostname' => '[SCRUBBED].us-east-1.rds.amazonaws.com',
	'db_username' => '[SCRUBBED]',
	'db_password' => '[SCRUBBED]',
	'db_database' => 'hrdb',
	'timezone' => 'US/Eastern',
);

# Set the IPs from which people can clock in/out
$config['db_allowed_ips'] = array(
	'[SCRUBBED]',
);

?>