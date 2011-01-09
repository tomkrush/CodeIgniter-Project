<?php
class CreateBlogsTable
{
	function up()
	{
		create_table('blogs', array(
			array('name' => 'rss_url', 'type' => 'string'),
			array('name' => 'slug', 'type' => 'string', 'NOT_NULL' => false),
			array('name' => 'name', 'type' => 'string', 'NOT_NULL' => false),
			array('name' => 'description', 'type' => 'string'),
			MIGRATION_TIMESTAMPS
		));
	}
}