<?php
class CreatePagesTable
{
	function up()
	{
		create_table('pages', array(
			array('name' => 'section_id', 'type' => 'integer'),
			array('name' => 'slug', 'type' => 'string', 'NOT_NULL' => false),
			array('name' => 'name', 'type' => 'string', 'NOT_NULL' => false),
			array('name' => 'title', 'type' => 'string', 'NOT_NULL' => false),
			array('name' => 'keywords', 'type' => 'string'),
			array('name' => 'description', 'type' => 'string'),
			array('name' => 'is_public', 'type' => 'boolean', 'NOT_NULL' => false, 'default' => false),
			array('name' => 'content', 'type' => 'text'),
			MIGRATION_TIMESTAMPS
		));		
	}
}