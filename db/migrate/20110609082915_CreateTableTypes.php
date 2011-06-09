<?php
class CreateTableTypes
{
	function up()
	{
		create_table('types', array(
			array('name' => 'is_flag', 'type' => 'boolean'),
			array('name' => 'count', 'type' => 'integer'),
			array('name' => 'name', 'type' => 'string'),
			MIGRATION_TIMESTAMPS
		));
	}
}