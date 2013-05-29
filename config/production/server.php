<?php

/**
 * Server configuration
 */
return array(
	// Server name. Uses in apache virtual-host 
	'name'		=> '',

	// Server ip. Uses in connecting to ssh
	'ip'		=> '',

	// Server port. Uses in connecting to ssh
	'port'		=> '',

	// Server user name. Uses in connecting to ssh
	'user'		=> '',

	// Server password. Uses in connecting to ssh
	'password'	=> '',

	// Server DB
	'db' => array(
		// Server db user name. Uses in database, creating users and tables
		'user'		=> '',

		// Server db user password. Uses in database, creating users and tables
		'password'	=> '',
	),
);