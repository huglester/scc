<?php

namespace Fuel\Migrations;

class Create_accounts_table
{
	public function up()
	{
		\Config::load('simpleauth', true);

		\DBUtil::create_table(\Config::get('simpleauth.table_name'), array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'username' => array('type' => 'varchar', 'constraint' => 50),
			'password' => array('type' => 'varchar', 'constraint' => 255),
			'group' => array('type' => 'int', 'constraint' => 11, 'default' => 1),
			'first_name' => array('type' => 'varchar', 'constraint' => 255),
			'last_name' => array('type' => 'varchar', 'constraint' => 255),
			'email' => array('type' => 'varchar', 'constraint' => 255),
			'last_login' => array('type' => 'varchar', 'constraint' => 25),
			'login_hash' => array('type' => 'varchar', 'constraint' => 255),
			'profile_fields' => array('type' => 'text'),
			'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
		), array('id'));

		\Auth::create_user_custom(
			'FirstName',
			'LastName',
			'admin',
			'admin123',
			'admin@admin.com',
			100
		);
	}

	public function down()
	{
		\Config::load('simpleauth', true);

		\DBUtil::drop_table(\Config::get('simpleauth.table_name'));
	}
}