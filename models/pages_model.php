<?php

class Pages_Model extends My_Model 
{	
	function init()
	{
		$this->fields('blog_id', 'section_id', 'is_public', 'name', 'slug', 'title', 'keywords', 'description', 'content');
		$this->validates('name', array('presence' => TRUE, 'uniqueness' => array('scope'=>'section_id', 'exclude_self' => TRUE)));
		$this->validates('slug', array('presence' => TRUE, 'uniqueness' => array('scope' => 'section_id', 'exclude_self' => TRUE)));
		
		$this->belongs_to('blog');
	}
	
	function test($conditions)
	{
		$conditions['slug'] = strtoupper($conditions['slug']);
		
		return $conditions;
	}
}