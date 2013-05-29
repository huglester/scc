<?php

namespace Fuel\Migrations;

class Create_hosts_table
{
	public function up()
	{
		\DBUtil::create_table('hosts', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'account_id' => array('type' => 'int', 'constraint' => 11),
			'active' => array('type' => 'int', 'constraint' => 1),
			'title' => array('type' => 'varchar', 'constraint' => 50),
			'password' => array('type' => 'varchar', 'constraint' => 500),
			'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('hosts');
	}
}