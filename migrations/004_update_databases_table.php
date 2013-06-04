<?php

namespace Fuel\Migrations;

class Update_databases_table
{
	public function up()
	{
		\DBUtil::add_fields('databases', array(
			'backuped_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
			'restored_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('databases', 'backuped_at');
		\DBUtil::drop_fields('databases', 'restored_at');
	}
}