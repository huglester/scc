<?php

namespace Fuel\Tasks;

class Hosts
{
	public static function import_old_hosts($old_password = '')
	{
		// Loading server configuration
		\Config::load('server', 'server');

		// Preparing counters
		$new_hosts = 0;

		// connect to server
		$ssh = new \PHPSecLib\Net_SSH2(\Config::get('server.ip'), \Config::get('server.port'));

		// login to server
		if ( ! $ssh->login(\Config::get('server.user'), \Config::get('server.password')))
		{
			throw new \Exception('ssh login failed');
		}

		// Getting hosts list
		$hosts  = $ssh->exec('ls /etc/apache2/sites-enabled');

		// Disconnect from server
		$ssh->disconnect();

		// Convert string to host names
		preg_match_all('/vhost-(.*)/', $hosts, $matches);

		// If no matches break
		if ( ! $matches[1])
		{
			\Cli::write('Found '.count($matches[1]).' stopping.', 'red');
			return;
		}

		// Checking DB for hosts
		foreach ($matches[1] as $value)
		{
			$host = \Model_Host::find_one_by('title', $value);

			if ( ! $host)
			{
				// Create host
				$host = \Model_Host::forge();
				$host->title 		= $value;
				$host->account_id	= 1;
				$host->active 		= 1;
				$host->password 	= $old_password;
				$host->save();

				$new_hosts++;
			}

			// Delete hosts cache
			\Event::trigger('delete_cache', array(
				'hosts', 'databases'
			));
		}

		// Reporting job statics
		\Cli::write('Found '.$new_hosts.' new hosts', 'green');
		
	}
}