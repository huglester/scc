<?php

class Model_Host extends \Model_Crud
{
	protected static $_table_name = 'hosts';

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
	);

	protected static $_defaults = array();

	protected static $_created_at = 'created_at';

	protected static $_updated_at = 'updated_at';

	protected static $_mysql_timestamp = false;
}