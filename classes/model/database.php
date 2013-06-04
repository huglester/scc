<?php

class Model_Database extends \Model_Crud
{
	protected static $_table_name = 'databases';

	protected static $_primary_key = 'id';

	protected static $_rules = array();

	protected static $_labels = array();

	protected static $_properties = array(
		'id',
		'account_id',
		'active',
		'title',
		'password',
		'created_at',
		'updated_at',
		'backuped_at',
		'restored_at',
	);

	protected static $_defaults = array();

	protected static $_created_at = 'created_at';

	protected static $_updated_at = 'updated_at';

	protected static $_mysql_timestamp = false;
}