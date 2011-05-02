<?php
class CreateColumnBlogIDOnPages
{
	function up()
	{
		create_column('pages', array('name' => 'blog_id', 'type' => 'integer', 'default'=>0));
	}
}