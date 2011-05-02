<?php
class CreateArticlesTable
{
	function up()
	{
		create_table('articles', array(
			array('name' => 'blog_id', 'type' => 'integer', 'NOT_NULL' => FALSE),
			array('name' => 'title', 'type' => 'string'),
			array('name' => 'contents', 'type' => 'string', 'NOT_NULL' => false),
			MIGRATION_TIMESTAMPS
		));
	}
}