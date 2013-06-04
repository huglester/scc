<?php

class MyEvents
{
	public static function _init()
	{
		// Registering all events
		Event::register('accounts_create', function($args)
		{
		});

		Event::register('accounts_edit', function($args)
		{
			// Delete cache
			Event::trigger('delete_cache', array(
				'accounts'
			));
		});

		Event::register('accounts_delete', function($args)
		{
			// Delete cache
			Event::trigger('delete_cache', array(
				'accounts'
			));
		});

		Event::register('accounts_logout', function($args)
		{
			// Delete cache
			Event::trigger('delete_cache', array(
				'accounts'
			));
		});

		Event::register('hosts_create', function($args)
		{
			try
			{
				// Loading server configuration
				Config::load('server', 'server');

				// connect to server
				$ssh = new \PHPSecLib\Net_SSH2(Config::get('server.ip'), Config::get('server.port'));

				// login to server
				if ( ! $ssh->login(Config::get('server.user'), Config::get('server.password')))
				{
					throw new \Exception('ssh login failed');
				}

				// User
				$ssh->exec('useradd -m --home-dir /var/www/vhosts/'.$args['title'].' --shell /bin/bash '.$args['title']);
				$ssh->exec('chmod a-w /var/www/vhosts/'.$args['title']);
				$ssh->exec('mkdir -p /var/www/vhosts/'.$args['title'].'/wwwroot/public');
				$ssh->exec('chown -R '.$args['title'].':'.$args['title'].' /var/www/vhosts/'.$args['title'].'/wwwroot');
				$ssh->exec('echo '.$args['title'].':'.$args['password'].' | chpasswd');

				// Apache
				$ssh->exec('echo "<VirtualHost *:80>
        ServerAdmin krek@nnteam.eu
        ServerName '.$args['title'].'.'.Config::get('server.name').'
        ServerAlias '.$args['title'].'.'.Config::get('server.name').'
        DocumentRoot /var/www/vhosts/'.$args['title'].'/wwwroot/public
        <Directory /var/www/vhosts/'.$args['title'].'/wwwroot/public>
                Options FollowSymLinks
                AllowOverride All
        </Directory>
        <Directory /var/www/vhosts/'.$args['title'].'/wwwroot/public>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        <IfModule mod_ruid2.c>
            RMode config
            RUidGid '.$args['title'].' '.$args['title'].'
            RGroups '.$args['title'].'
        </IfModule>

        ErrorLog /var/log/apache2/'.$args['title'].'-error.log
        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn
        CustomLog /var/log/apache2/'.$args['title'].'-access.log combined
</VirtualHost>" >> "/etc/apache2/sites-enabled/vhost-'.$args['title'].'"');
				$ssh->exec('/etc/init.d/apache2 reload');

				// Disconnect from server
				$ssh->disconnect();

				// Delete cache
				Event::trigger('delete_cache', array(
					'hosts', 'databases'
				));
			}
			catch (Exception $e)
			{
			}
		});

		Event::register('hosts_edit', function($args)
		{
			// Delete cache
			Event::trigger('delete_cache', array(
				'hosts', 'databases'
			));
		});

		Event::register('hosts_delete', function($args)
		{
			try
			{
				// Loading server configuration
				Config::load('server', 'server');

				// connect to server
				$ssh = new \PHPSecLib\Net_SSH2(Config::get('server.ip'), Config::get('server.port'));

				// login to server
				if ( ! $ssh->login(Config::get('server.user'), Config::get('server.password')))
				{
					throw new \Exception('ssh login failed');
				}

				// User
				$ssh->exec('userdel '.$args['title']);
				$ssh->exec('rm -rf /var/www/vhosts/'.$args['title']);

				// Apache
				$ssh->exec('rm -rf /etc/apache2/sites-enabled/vhost-'.$args['title']);
				$ssh->exec('/etc/init.d/apache2 reload');

				// Disconnect from server
				$ssh->disconnect();

				// Delete cache
				Event::trigger('delete_cache', array(
					'hosts', 'databases'
				));
			}
			catch (Exception $e)
			{
			}
		});

		Event::register('databases_create', function($args)
		{
			try
			{
				// Loading server configuration
				Config::load('server', 'server');

				// connect to server
				$ssh = new \PHPSecLib\Net_SSH2(Config::get('server.ip'), Config::get('server.port'));

				// login to server
				if ( ! $ssh->login(Config::get('server.user'), Config::get('server.password')))
				{
					throw new \Exception('ssh login failed');
				}

				// Create mysql user and table
				$ssh->exec('mysql -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' -e "CREATE DATABASE '.$args['title'].'";');
				$ssh->exec('mysql -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' -e "GRANT ALL ON '.$args['title'].'.* to '.$args['title'].'@localhost IDENTIFIED BY \''.$args['password'].'\'";');
				$ssh->exec('mysql -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' -e "flush privileges";');

				// Disconnect from server
				$ssh->disconnect();

				// Delete cache
				Event::trigger('delete_cache', array(
					'hosts', 'databases'
				));
			}
			catch (Exception $e)
			{
			}
		});

		Event::register('databases_delete', function($args)
		{
			try
			{
				// Loading server configuration
				Config::load('server', 'server');

				// connect to server
				$ssh = new \PHPSecLib\Net_SSH2(Config::get('server.ip'), Config::get('server.port'));

				// login to server
				if ( ! $ssh->login(Config::get('server.user'), Config::get('server.password')))
				{
					throw new \Exception('ssh login failed');
				}

				// Delete mysql user and table
				$ssh->exec('mysql -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' -e "DROP DATABASE '.$args['title'].'";');
				$ssh->exec('mysql -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' -e "DROP USER '.$args['title'].'@localhost";');
				$ssh->exec('mysql -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' -e "flush privileges";');

				// Disconnect from server
				$ssh->disconnect();

				// Delete cache
				Event::trigger('delete_cache', array(
					'hosts', 'databases'
				));
			}
			catch (Exception $e)
			{
			}
		});

		Event::register('databases_backup', function($args)
		{
			try
			{
				// Loading server configuration
				Config::load('server', 'server');

				// connect to server
				$ssh = new \PHPSecLib\Net_SSH2(Config::get('server.ip'), Config::get('server.port'));

				// login to server
				if ( ! $ssh->login(Config::get('server.user'), Config::get('server.password')))
				{
					throw new \Exception('ssh login failed');
				}

				// Backup mysql table
				$ssh->exec('mysqldump -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' --default-character-set=utf8 '.$args['title'].' > /var/www/vhosts/scc/wwwroot/fuel/app/cache/'.$args['title'].'.sql');
				$ssh->exec('chown scc:scc /var/www/vhosts/scc/wwwroot/fuel/app/cache/'.$args['title'].'.sql');

				// Disconnect from server
				$ssh->disconnect();

				// Delete cache
				Event::trigger('delete_cache', array(
					'databases'
				));

			}
			catch (Exception $e)
			{
			}
		});

		Event::register('databases_restore', function($args)
		{
			try
			{
				// Loading server configuration
				Config::load('server', 'server');

				// connect to server
				$ssh = new \PHPSecLib\Net_SSH2(Config::get('server.ip'), Config::get('server.port'));

				// login to server
				if ( ! $ssh->login(Config::get('server.user'), Config::get('server.password')))
				{
					throw new \Exception('ssh login failed');
				}

				// Backup mysql table
				$ssh->exec('mysql -u'.Config::get('server.db.user').' -p'.Config::get('server.db.password').' --default-character-set=utf8 '.$args['title'].' < /var/www/vhosts/scc/wwwroot/fuel/app/cache/'.$args['title'].'.sql');

				// Disconnect from server
				$ssh->disconnect();

				// Delete cache
				Event::trigger('delete_cache', array(
					'databases'
				));

			}
			catch (Exception $e)
			{
			}
		});

		Event::register('delete_cache', function($args)
		{
			try
			{
				foreach ($args as $value)
				{
					Cache::delete_all($value);
				}
			}
			catch (Exception $e)
			{
			}
		});
	}
}