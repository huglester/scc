<?php

class Model_Account extends \Model_Crud
{
	protected static $_table_name = 'accounts';

	protected static $_primary_key = 'id';

	protected static $_rules = array();

	protected static $_labels = array();

	protected static $_properties = array(
		'id',
		'username',
		'password',
		'group',
		'first_name',
		'last_name',
		'email',
		'last_login',
		'login_hash',
		'profile_fields',
		'created_at',
		'updated_at',
	);

	protected static $_defaults = array();

	protected static $_created_at = 'created_at';

	protected static $_updated_at = 'updated_at';

	protected static $_mysql_timestamp = false;
}