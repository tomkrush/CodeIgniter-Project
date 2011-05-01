<?php

class Blogs_Model extends My_Model 
{	
	function init()
	{
		$this->fields('rss_url', 'slug', 'name', 'description');
		$this->validates('name', array('presence' => TRUE, 'uniqueness' => TRUE));
		$this->validates('slug', array('presence' => TRUE, 'uniqueness' => TRUE));
		
		$this->has_many('articles');
	}
}