<?php

namespace Fuel\Tasks;

class Databases
{
	public static function import_old_databases($old_password = '')
	{
		// Loading server configuration
		\Config::load('server', 'server');

		// Preparing counters
		$new_databases = 0;

		// connect to server
		$ssh = new \PHPSecLib\Net_SSH2(\Config::get('server.ip'), \Config::get('server.port'));

		// login to server
		if ( ! $ssh->login(\Config::get('server.user'), \Config::get('server.password')))
		{
			throw new \Exception('ssh login failed');
		}

		// Getting databases list
		$databases  = $ssh->exec('mysql -u'.\Config::get('server.db.user').' -p'.\Config::get('server.db.password').' -e "show databases";');

		// Disconnect from server
		$ssh->disconnect();

		// Convert string to databases names
		$matches = explode("\n", $databases);

		// If no matches break
		if ( ! $matches)
		{
			\Cli::write('Found '.count($matches).' stopping.', 'red');
			return;
		}

		// Defining system databases
		$system_databases = array(
			'Database', 'information_schema', 'mysql', 'performance_schema', 'phpmyadmin', ''
		);

		// Checking databases
		foreach ($matches as $value)
		{
			$database = \Model_Database::find_one_by('title', $value);

			if ( ! $database and ! in_array($value, $system_databases))
			{
				// Create database
				$database = \Model_Database::forge();
				$database->title 		= $value;
				$database->account_id	= 1;
				$database->active 		= 1;
				$database->password 	= $old_password;
				$database->save();

				$new_databases++;
			}

			// Delete hosts cache
			\Event::trigger('delete_cache', array(
				'hosts', 'databases'
			));
		}

		// Reporting job statics
		\Cli::write('Found '.$new_databases.' new hosts', 'green');
		
	}
}