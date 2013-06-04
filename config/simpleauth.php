<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(

	/**
	 * DB connection, leave null to use default
	 */
	'db_connection' => null,

	/**
	 * DB table name for the user table
	 */
	'table_name' => 'accounts',

	/**
	 * Choose which columns are selected, must include: username, password, email, last_login,
	 * login_hash, group & profile_fields
	 */
	'table_columns' => array('*'),

	/**
	 * This will allow you to use the group & acl driver for non-logged in users
	 */
	'guest_login' => true,

	/**
	 * This will allow the same user to be logged in multiple times.
	 *
	 * Note that this is less secure, as session hijacking countermeasures have to
	 * be disabled for this to work!
	 */
	'multiple_logins' => false,

	/**
	 * Remember-me functionality
	 */
	'remember_me' => array(
		/**
		 * Whether or not remember me functionality is enabled
		 */
		'enabled' => false,

		/**
		 * Name of the cookie used to record this functionality
		 */
		'cookie_name' => 'rawrCookie',

		/**
		 * Remember me expiration (default: 31 days)
		 */
		'expiration' => 86400 * 31,
	),

	/**
	 * Groups as id => array(name => <string>, roles => <array>)
	 */
	'groups' => array(
		-1   => array('name' => 'Banned', 'roles' => array('banned')),
		0    => array('name' => 'Guests', 'roles' => array('guests')),
		3    => array('name' => 'Developers', 'roles' => array('developers')),
		100  => array('name' => 'Administrators', 'roles' => array('administrators')),
	),

	/**
	 * Roles as name => array(location => rights)
	 */
	'roles' => array(
		'banned' => array(
			'Controller_Accounts' => array('login'),
		),
		'guests' => array(
			'Controller_Accounts' => array('login'),
		),
		'developers' => array(
			'Controller_Accounts'	=> array('login', 'logout'),
			'Controller_Hosts'		=> array('create', 'index', 'delete'),
			'Controller_Databases'	=> array('create', 'index', 'delete', 'backup', 'restore'),
			'Controller_Dashboard'	=> array('index'),
		),
		'administrators' => array(
			'Controller_Accounts'	=> array('login', 'logout', 'create', 'index', 'delete', 'edit'),
			'Controller_Hosts'		=> array('create', 'index', 'delete'),
			'Controller_Databases'	=> array('create', 'index', 'delete', 'backup', 'restore'),
			'Controller_Dashboard'	=> array('index'),
		),
	),

	/**
	 * Salt for the login hash
	 */
	'login_hash_salt' => 'put_some_salt_in_here',

	/**
	 * $_POST key for login username
	 */
	'username_post_key' => 'username',

	/**
	 * $_POST key for login password
	 */
	'password_post_key' => 'password',
);
